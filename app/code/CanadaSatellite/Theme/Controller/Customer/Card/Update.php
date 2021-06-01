<?php

namespace CanadaSatellite\Theme\Controller\Customer\Card;

use Magento\Framework\App\RequestInterface;

class Update extends \Magento\Framework\App\Action\Action
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

    /**
     * Customer helper
     *
     * @var \CanadaSatellite\DynamicsIntegration\DynamicsCrm\CustomerHelper
     */
    protected $_customerHelper;

    function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\CustomerHelper $customerHelper,
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
        \CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_customerHelper = $customerHelper;
        $this->_restApi = $restApi;
        $this->_logger = $logger;

        parent::__construct($context);
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
            $params = $this->getRequest()->getPostValue();
            if (!$params) {
                $this->_logger->info("[Update card] Empty card data!");
                return $this->redirectWithError();
            }

            $customer = $this->_getCustomer();
            $customerId = $customer->getId();
            $customerEmail = $customer->getEmail();

            $methodCode = "casat_customer_card"; 
            $cardId = $params[$methodCode]['card_id'];
            $this->_logger->info("[Update card] Saving card $cardId for customer $customerId...");

            if (empty($customerId)) {
                $this->_logger->info("[Update card] Customer $customerId is not set.");
                return $this->redirectWithError('Customer not found');
            }
            if (empty($cardId)) {
                $this->_logger->info("[Update card] Customer $customerId is not set.");
                return $this->redirectWithError('Card not found');
            }

            $card = $this->_restApi->getCard($cardId);
            if ($card === false) {
                $this->_logger->info("[Update card] Card $cardId does not exist");
                return $this->redirectWithError('Card does not exist.');
            }

            $this->_logger->info("[Update card] Got card $cardId"); // . var_export($card, true));

            if (!$this->checkCardBelongsToCustomer($card, $customerId)) {
                $this->_logger->info("[Update card] Card $cardId does not belong to $customerId");
                return $this->redirectWithError('Card does not exist');
            }

            $account = $this->_customerHelper->findCustomerAccount($customer);
            if ($account === null) {
                $this->_logger->info("[Update card] Failed to get CRM account id for customer $customerId ");
                return $this->redirectWithError('Customer not found');
            }
            $accountId = $account->accountid;
            $this->_logger->info("[Update card] Customer $customerId CRM account id is $accountId.");
            
            $card = array(
                'new_account@odata.bind' => "/accounts($accountId)",
                'new_cardtype' => $params[$methodCode]['payment_info']['cc_type'],
                'new_name' => $params[$methodCode]['payment_info']['cc_number'],
                'new_cardholdername' => $params[$methodCode]['payment_info']['cc_cardholder_name'],
                'new_expirymonth' => $params[$methodCode]['payment_info']['cc_exp_month'],
                'new_expiryyear' => $params[$methodCode]['payment_info']['cc_exp_year'],
                'new_cvv' => (int)$params[$methodCode]['payment_info']['cc_cvn'],
            );

            $this->_restApi->updateCard($cardId, $card);
            $this->_logger->info("[Update card] Card $cardId is updated for customer $customerId");

            return $this->redirectWithSuccess('Card is updated');
        } catch (\Exception $e) {
            $this->_logger->info("[Update card] Failed to update card $cardId for customer $customerId " . $e->getMessage() . "\r\nStack trace: " . $e->getTraceAsString());
            return $this->redirectWithError();
        }
    }

    protected function _getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    protected function _getSession()
    {
        return $this->_customerSession;
    }

    private function checkCardBelongsToCustomer($card, $customerId)
    {
    	if (empty($customerId) || empty($card)) {
    		return false;
    	}

    	if (!isset($card->new_account)) {
    		return false;
    	}

    	return $card->new_account->accountnumber == $customerId;
    }

    private function redirectWithError($error = null)
    {
    	if (empty($error)) {
    		$error = 'An error has occured.';
    	}

    	$this->messageManager->addError(__($error));

    	$resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/card_listing');
    }

    private function redirectWithSuccess($message = null)
    {
    	if (empty($message)) {
    		$message = 'Success.';
    	}

    	$this->messageManager->addSuccess(__($message));

    	$resultRedirect = $this->resultRedirectFactory->create();
		return $resultRedirect->setPath('*/*/card_listing');
    }
}