<?php

namespace CanadaSatellite\Theme\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Registry;

class SimNoRecharge extends \Magento\Framework\App\Action\Action
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
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
        \CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_simFactory = $simFactory;
        $this->_restApi = $restApi;
        $this->_logger = $logger;

        parent::__construct($context);
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
            $customerId = $this->_customerSession->getCustomerId();
            $this->_logger->info("[SIM Auto-recharge disable] Disable auto-recharge SIM $simId of customer $customerId ...");

            if (empty($simId)) {
                $this->_logger->info("[SIM Auto-recharge disable] Unable to find card $simId to disable auto-recharge.");
                return $this->redirectWithError();
            }
            if (empty($customerId)) {
                $this->_logger->info("[SIM Auto-recharge disable] Customer $customerId is not set.");
                return $this->redirectWithError();
            }

            $sim = $this->_simFactory->create()->load($simId);
            if (!$sim->getId()) {
                $this->_logger->info("[SIM Auto-recharge disable] SIM $simId not found");
                return $this->redirectWithError('SIM does not exist');
            }

            if ($sim->getMagentoCustomerId() != $customerId) {
                $this->_logger->info("[SIM Auto-recharge disable] SIM $simId does not belong to $customerId");
                return $this->redirectWithError('SIM does not exist');
            }

            $crmSim = array(
                'new_substatus' => null, 
            );

            $this->_restApi->updateSim($simId, $crmSim);
            $this->_logger->info("[SIM Auto-recharge disable] SIM $simId sub-status auto-recharge disabled");

            return $this->redirectWithSuccess('SIM will not be auto-recharged');
        } catch (\Exception $e) {
            $this->_logger->info("[SIM Auto-recharge disable] Failed to disable auto-recharge for SIM $simId of customer $customerId" . $e->getMessage() . "\r\nStack trace: " . $e->getTraceAsString());
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