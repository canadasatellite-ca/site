<?php

namespace CanadaSatellite\Theme\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Registry;

class SimRecharge extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Customer session.
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * The SIM Factory
     *
     * @var \CanadaSatellite\Theme\Model\SimFactory
     */
    protected $_simFactory;

    /**
     * The Card collection Factory
     *
     * @var \CanadaSatellite\Theme\Model\ResourceModel\Card\CollectionFactory
     */
    protected $_cardCollectionFactory;

    /**
     * Customer helper
     *
     * @var \CanadaSatellite\DynamicsIntegration\DynamicsCrm\CustomerHelper
     */
    protected $_customerHelper;

    /**
     * REST API for MS Dynamics
     *
     * @var \CanadaSatellite\DynamicsIntegration\Rest\RestApi
     */
    protected $_restApi;

    /**
     * Logger
     *
     * @var \CanadaSatellite\DynamicsIntegration\Logger\Logger
     */
    protected $_logger;

    function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \CanadaSatellite\Theme\Model\SimFactory $simFactory,
        \CanadaSatellite\Theme\Model\ResourceModel\Card\CollectionFactory $cardCollectionFactory,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\CustomerHelper $customerHelper,
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
        \CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_simFactory = $simFactory;
        $this->_cardCollectionFactory = $cardCollectionFactory;
        $this->_customerHelper = $customerHelper;
        $this->_restApi = $restApi;
        $this->_logger = $logger;

        parent::__construct($context);
    }

    protected function _getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    protected function _getSession()
    {
        return $this->_customerSession;
    }

    function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        return parent::dispatch($request);
    }
    function execute()
    {
        try {      
            $simId = $this->_request->getParam('id');
            $customer = $this->_getCustomer();
            $customerId = $customer->getId();
            $customerEmail = $customer->getEmail();

            $this->_logger->info("[SIM Auto-recharge] Auto-recharge SIM $simId of customer $customerId ...");

            if (empty($simId)) {
                $this->_logger->info("[SIM Auto-recharge] Unable to find card $simId to auto-recharge.");
                return $this->redirectWithError();
            }
            if (empty($customerId)) {
                $this->_logger->info("[SIM Auto-recharge] Customer $customerId is not set.");
                return $this->redirectWithError();
            }
     
            $sim = $this->_simFactory->create()->load($simId);
            if (!$sim->getId()) {
                $this->_logger->info("[SIM Auto-recharge] SIM $simId not found");
                return $this->redirectWithError('SIM does not exist');
            }

            if ($sim->getMagentoCustomerId() != $customerId) {
                $this->_logger->info("[SIM Auto-recharge] SIM $simId does not belong to $customerId");
                return $this->redirectWithError('SIM does not exist');
            }

            $cards = $this->_cardCollectionFactory->create()->addCustomerFilter($customerId);
            $params = $this->getRequest()->getPostValue();
            //$this->_logger->info("[SIM Auto-recharge] Recharge params: " . var_export($params, true));

            $methodCode = 'casat_customer_card';

            if (count($cards) == 0) {
                if (!isset($params[$methodCode])) {
                    $this->_logger->info("[SIM Auto-recharge] Credit card is mandatory for auto-recharge.");
                    // Request credit card page.
                    $resultPage = $this->_resultPageFactory->create();
                    return $resultPage;
                }


                $account = $this->_customerHelper->findCustomerAccount($customer);
                if ($account === null) {
                    $this->_logger->info("[Save card] Failed to get CRM account id for customer $customerId ");
                    return $this->redirectWithError('Customer not found');
                }
                $accountId = $account->accountid;
                $this->_logger->info("[SIM Auto-recharge] Customer $customerId CRM account id is $accountId.");

                $crmCard = array(
                    'new_account@odata.bind' => "/accounts($accountId)",
                    'new_cardtype' => $params[$methodCode]['payment_info']['cc_type'],
                    'new_name' => $params[$methodCode]['payment_info']['cc_number'],
                    'new_cardholdername' => $params[$methodCode]['payment_info']['cc_cardholder_name'],
                    'new_expirymonth' => $params[$methodCode]['payment_info']['cc_exp_month'],
                    'new_expiryyear' => $params[$methodCode]['payment_info']['cc_exp_year'],
                    'new_cvv' => (int)$params[$methodCode]['payment_info']['cc_cvn'],
                );

                $cardId = $this->_restApi->createCard($crmCard);
                $this->_logger->info("[SIM Auto-recharge] Card is added for customer $customerId with card id $cardId");
            }

            $crmSim = array(
                'new_substatus' => '100000000', // AUTO-RECHARGE
            );

            $this->_restApi->updateSim($simId, $crmSim);
            $this->_logger->info("[SIM Auto-recharge] SIM $simId sub-status updated to auto-recharge");

            return $this->redirectWithSuccess('SIM will be auto-recharged');
        } catch (\Exception $e) {
            $this->_logger->info("[SIM Auto-recharge] Failed to auto-recharge $simId for customer $customerId" . $e->getMessage() . "\r\nStack trace: " . $e->getTraceAsString());
            return $this->redirectWithError();
        }
    }

    private function redirectWithError($error = null)
    {
        if (empty($error)) {
            $error = 'An error has occured.';
        }

        $this->messageManager->addError(__($error));

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/viewsim');
    }

    private function redirectWithSuccess($message = null)
    {
        if (empty($message)) {
            $message = 'Success.';
        }

        $this->messageManager->addSuccess(__($message));

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/viewsim');
    }
}