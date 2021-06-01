<?php

namespace CanadaSatellite\Theme\Controller\Customer\Card;

use Magento\Framework\App\RequestInterface;

class Delete extends \Magento\Framework\App\Action\Action
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
     * Sim resource model
     *
     * @var \CanadaSatellite\Theme\Model\ResourceModel\Sim\CollectionFactory
     */
    protected $_simCollectionFactory;

    /**
     * The Card collection Factory
     *
     * @var \CanadaSatellite\Theme\Model\ResourceModel\Card\CollectionFactory
     */
    protected $_cardCollectionFactory;

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
        \CanadaSatellite\Theme\Model\ResourceModel\Sim\CollectionFactory $simCollectionFactory,
        \CanadaSatellite\Theme\Model\ResourceModel\Card\CollectionFactory $cardCollectionFactory,
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
        \CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_simCollectionFactory = $simCollectionFactory;
        $this->_cardCollectionFactory = $cardCollectionFactory;
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
            $deleteCardId = $this->getRequest()->getPostValue('card_id');
            $customerId = $this->_customerSession->getCustomerId();

            $this->_logger->info("[Delete card] Deleting card $deleteCardId of customer $customerId ...");

            if (empty($deleteCardId)) {
                $this->_logger->info("[Delete card] Unable to find card $deleteCardId to delete.");
                return $this->redirectWithError();
            }
            if (empty($customerId)) {
                $this->_logger->info("[Delete card] Customer $customerId is not set.");
                return $this->redirectWithError();
            }

            try {
                $card = $this->_restApi->getCard($deleteCardId);
            } catch (\Exception $e) {
                $this->_logger->info("[Delete card] Failed to get card $deleteCardId " . $e->getMessage() . "\r\nStack trace: " . $e->getTraceAsString());
                return $this->redirectWithError();
            }

            if ($card === false) {
                $this->_logger->info("[Delete card] Card $deleteCardId does not exist");
                return $this->redirectWithSuccess('Card deleted successfully.');
            }
            $this->_logger->info("[Delete card] Got card $deleteCardId"); // . var_export($card, true));

            if (!$this->checkCardBelongsToCustomer($card, $customerId)) {
                $this->_logger->info("[Delete card] Card $deleteCardId does not belong to $customerId");
                return $this->redirectWithError('Card does not exist');
            }

            $autorechargeEnabled = false;
            $sims = $this->_simCollectionFactory->create()->addCustomerFilter($customerId);
            $this->_logger->info("[Delete card] Total SIMs: " . count($sims));
            foreach ($sims as $sim) {
                $this->_logger->info("[Delete card] Check SIM " . $sim->getId() . " for auto-recharge: " . var_export($sim->isAutoRecharged(), true));
                if ($sim->isAutoRecharged()) {
                    $autorechargeEnabled = true;
                    break;
                }
            }

            if ($autorechargeEnabled) {
                $cards = $this->_cardCollectionFactory->create()->addCustomerFilter($customerId);
                if (count ($cards) == 1) {
                    $this->_logger->info("[Delete card] Can not delete last credtit card when auto-charge enabled");
                    return $this->redirectWithError('Please disable SIM auto-recharge to delete card');
                }
            }

            $this->_restApi->deleteCard($deleteCardId);
            $this->_logger->info("[Delete card] Card $deleteCardId is deleted.");

            return $this->redirectWithSuccess('Card deleted successfully.');
        } catch (\Exception $e) {
            $this->_logger->info("[Delete card] Failed to delete card $deleteCardId for customer $customerId" . $e->getMessage() . "\r\nStack trace: " . $e->getTraceAsString());
            return $this->redirectWithError();
        }
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