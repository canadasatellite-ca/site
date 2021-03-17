<?php

namespace CanadaSatellite\Theme\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Registry;

class UpdateNickname extends \Magento\Framework\App\Action\Action
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

    public function __construct(
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

    public function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        return parent::dispatch($request);
    }

    public function execute()
    {
        try {
            $simId = $this->_request->getParam('id');
            $nickname = $this->_request->getParam('nickname');
            $customerId = $this->_customerSession->getCustomerId();
            $this->_logger->info("[SIM Nickname update] New nickname $nickname for SIM $simId of customer $customerId ...");

            if (empty($simId)) {
                $this->_logger->info("[SIM Nickname update] Unable to find card $simId.");
                return $this->redirectWithError();
            }
            if (empty($customerId)) {
                $this->_logger->info("[SIM Nickname update] Customer $customerId is not set.");
                return $this->redirectWithError();
            }

            $sim = $this->_simFactory->create()->load($simId);
            if (!$sim->getId()) {
                $this->_logger->info("[SIM Nickname update] SIM $simId not found");
                return $this->redirectWithError('SIM does not exist');
            }

            if ($sim->getMagentoCustomerId() != $customerId) {
                $this->_logger->info("[SIM Nickname update] SIM $simId does not belong to $customerId");
                return $this->redirectWithError('SIM does not exist');
            }

            $crmSim = array(
                'new_nickname' => $nickname
            );

            $this->_restApi->updateSim($simId, $crmSim);
            $this->_logger->info("[SIM Nickname update] SIM $simId updated nickname");

            return $this->redirectWithSuccess($simId,'SIM updated nickname');
        } catch (\Exception $e) {
            $this->_logger->info("[SIM Nickname update] Failed to update nickname for SIM $simId of customer $customerId" . $e->getMessage() . "\r\nStack trace: " . $e->getTraceAsString());
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

    private function redirectWithSuccess($simId, $message = null)
    {
        if (empty($message)) {
            $message = 'Success.';
        }

        $this->messageManager->addSuccess(__($message));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/simdetails/id/' . $simId);
    }
}