<?php

namespace CanadaSatellite\Theme\Controller\Customer\Card;

use Magento\Framework\App\RequestInterface;

class Edit extends \Magento\Framework\App\Action\Action
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
     * @var \CanadaSatellite\Theme\Model\CardFactory
     */
    protected $_cardFactory;

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
        \CanadaSatellite\Theme\Model\CardFactory $cardFactory,
        \CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_cardFactory = $cardFactory;
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
            $cardId = $this->getRequest()->getParam('id');
            $customerId = $this->_customerSession->getCustomerId();

            $this->_logger->info("[Edit card] Edit card $cardId of customer $customerId ...");

            if (empty($cardId)) {
                $this->_logger->info("[Edit card] Unable to find card $cardId to edit.");
                return $this->redirectWithError();
            }
            if (empty($customerId)) {
                $this->_logger->info("[Edit card] Customer $customerId is not set.");
                return $this->redirectWithError();
            }

            $card = $this->_cardFactory->create()->load($cardId);

            if (!$card->getId()) {
                $this->_logger->info("[Edit card] Card $cardId does not exist");
                return $this->redirectWithError('Card does not exist');
            }

            $this->_logger->info("[Edit card] Got card $cardId"); // . var_export($card, true));

            if (!$card->belongsToCustomer($customerId)) {
                $this->_logger->info("[Edit card] Card $cardId does not belong to $customerId");
                return $this->redirectWithError('Card does not exist');
            }

            $resultPage = $this->_resultPageFactory->create();
            return $resultPage;
        } catch (\Exception $e) {
            $this->_logger->info("[Edit card] Failed to edit card $cardId " . $e->getMessage() . "\r\nStack trace: " . $e->getTraceAsString());
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