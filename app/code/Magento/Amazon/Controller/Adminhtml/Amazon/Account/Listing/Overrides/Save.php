<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing\Overrides;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\Indexer\StockIndexer;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Log as ListingLogResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Action
 */
class Save extends Action
{
    /** @var int */
    const DEFAULT_HANDLING_TIME = 2;
    /** @var int */
    const ADD_OVERRIDE = 1;
    /** @var int */
    const REMOVE_OVERRIDE = 2;

    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;
    /** @var ListingManagementInterface $listingManagement */
    protected $listingManagement;
    /** @var ListingLogResourceModel $listingLogResourceModel */
    protected $listingLogResourceModel;
    /** @var StockIndexer $stockIndexer */
    protected $stockIndexer;

    /**
     * @param Action\Context $context
     * @param AccountRepositoryInterface $accountRepository
     * @param ListingRepositoryInterface $listingRepository
     * @param ResourceModel $resourceModel
     * @param ListingManagementInterface $listingManagement
     * @param ListingLogResourceModel $listingLogResourceModel
     * @param StockIndexer $stockIndexer
     */
    public function __construct(
        Action\Context $context,
        AccountRepositoryInterface $accountRepository,
        ListingRepositoryInterface $listingRepository,
        ResourceModel $resourceModel,
        ListingManagementInterface $listingManagement,
        ListingLogResourceModel $listingLogResourceModel,
        StockIndexer $stockIndexer
    ) {
        parent::__construct($context);
        $this->accountRepository = $accountRepository;
        $this->listingRepository = $listingRepository;
        $this->resourceModel = $resourceModel;
        $this->listingManagement = $listingManagement;
        $this->listingLogResourceModel = $listingLogResourceModel;
        $this->stockIndexer = $stockIndexer;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Saves listing override (handles both mass action and grid row action)
     * Grid row action is passed as url parameter listing_id
     *
     * @return Redirect $resultRedirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');
        /** @var string */
        $tab = $this->getRequest()->getParam('tab');
        /** @var bool */
        $flag = false;

        /** @var array */
        if ($ids = $this->getRequest()->getParam('selected_ids')) {
            $ids = json_decode($ids);
        }

        if ($id = $this->getRequest()->getParam('id')) {
            $ids = [$id];
        }

        if (empty($ids)) {
            $this->messageManager
                ->addErrorMessage(__('Please select items.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => $tab]
            );
        }

        // condition override
        if ($action = $this->getRequest()->getParam('condition_override_action')) {
            $this->processConditionOverride($action, $ids);
            $flag = true;
        }

        // price override
        if ($action = $this->getRequest()->getParam('list_price_override_action')) {
            $this->processPriceOverride($action, $ids);
        }

        // handling time override
        if ($action = $this->getRequest()->getParam('handling_override_action')) {
            $this->processHandlingTimeOverride($action, $merchantId, $ids, $flag);
        }

        // condition notes override
        if ($action = $this->getRequest()->getParam('condition_notes_override_action')) {
            $this->processConditionNotesOverride($action, $ids);
        }

        $message = 'You have successfully saved ';
        $message .= count($ids);
        $message .= ' listing override(s) and updates are in progress.';

        $this->messageManager
            ->addSuccessMessage(__($message));
        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, 'active_tab' => $tab]
        );
    }

    /**
     * Processes Amazon listing price override
     *
     * @param int $action
     * @param array $ids
     * @return void
     */
    private function processPriceOverride($action, array $ids)
    {
        foreach ($ids as $id) {
            // add price override
            if ((int)$action === self::ADD_OVERRIDE) {

                /** @var float */
                $priceOverride = $this->getRequest()->getParam('list_price_override');

                if ($priceOverride) {
                    $this->listingManagement->setPriceOverride($id, $priceOverride);
                }
            } elseif ($action == self::REMOVE_OVERRIDE) { // remove existing price override
                $this->listingManagement->setPriceOverride($id);
            }
        }
    }

    /**
     * Processes Amazon listing handling time override
     *
     * @param int $action
     * @param int $merchantId
     * @param array $ids
     * @param bool $flag
     * @return void
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    private function processHandlingTimeOverride($action, $merchantId, array $ids, $flag = false)
    {
        /** @var int */
        $handlingOverride = 0;
        /** @var string */
        $actionText = __('Handling Time');

        // add handling override
        if ($action == self::ADD_OVERRIDE) {
            $handlingOverride = $this->getRequest()->getParam('handling_override');

            if ($handlingOverride) {
                $this->resourceModel->setHandlingOverride($ids, $handlingOverride);
            }
        } elseif ($action == self::REMOVE_OVERRIDE) { // remove handling override
            /** @var AccountInterface */
            $account = $this->accountRepository->getByMerchantId($merchantId);
            /** @var int */
            $handlingOverride = ($account->getHandlingTime()) ? $account->getHandlingTime() : 2;

            $this->resourceModel->removeHandlingOverride($ids);
        }

        if ($handlingOverride) {
            /** @var string */
            $notes = __('Handling time updated to ') . $handlingOverride . __(' days');

            if (!$flag) {
                $this->scheduleIndexProcess($ids);
            }

            $this->insertLogRequest($ids, $actionText, $notes);
        }
    }

    /**
     * Processes Amazon listing condition override
     *
     * @param int $action
     * @param array $ids
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processConditionOverride($action, array $ids)
    {
        // add condition override
        if ($action == self::ADD_OVERRIDE) {

            /** @var int */
            $conditionOverride = $this->getRequest()->getParam('condition_override');

            if ($conditionOverride) {
                $this->resourceModel->setConditionOverride($ids, $conditionOverride);

                // if set to new, must remove condition notes override
                if ($conditionOverride == Definitions::NEW_CONDITION_CODE) {
                    $this->resourceModel->setConditionNotesOverride($ids);
                }
            }
        } elseif ($action == self::REMOVE_OVERRIDE) { // remove condition override
            $this->resourceModel->removeConditionOverride($ids);
        }
    }

    /**
     * Processes Amazon listing condition notes override
     *
     * @param int $action
     * @param array $ids
     * @return void
     */
    private function processConditionNotesOverride($action, array $ids)
    {
        /** @var string */
        $conditionNotesOverride = $this->getRequest()->getParam('condition_notes_override');
        /** @var string */
        $actionText = __('Seller Notes');
        /** @var string */
        $notes = __('Requested seller note change');

        // add condition notes override
        if ($action == self::ADD_OVERRIDE) {
            if ($conditionNotesOverride) {
                $this->resourceModel->setConditionNotesOverride($ids, $conditionNotesOverride);
            }
        }

        // remove condition notes override
        if ($action == self::REMOVE_OVERRIDE) {
            $this->resourceModel->setConditionNotesOverride($ids);
        }

        $this->resourceModel->scheduleListStatusUpdate($ids, Definitions::CONDITION_OVERRIDE_LIST_STATUS);

        $this->insertLogRequest($ids, $actionText, $notes);
    }

    /**
     * Prepare insertion request for logging Amazon API action
     *
     * @param string $listingId
     * @param string $action
     * @param string $notes
     * @return void
     */
    private function insertLogRequest($ids, $action, $notes)
    {
        /** @var array */
        $data = [];

        foreach ($ids as $id) {
            try {
                $listing = $this->listingRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                continue;
            }

            $data[] = [
                'merchant_id' => $listing->getMerchantId(),
                'seller_sku' => $listing->getSellerSku(),
                'action' => __($action),
                'notes' => __($notes)
            ];
        }

        if (!empty($data)) {
            $this->listingLogResourceModel->insert($data);
        }
    }

    /**
     * Schedules partial reindex process
     *
     * @param array $ids
     * @return void
     */
    public function scheduleIndexProcess(array $ids)
    {
        /** @var array */
        $productIds = [];

        // collect product ids
        foreach ($ids as $id) {
            try {
                $listing = $this->listingRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                continue;
            }

            /** @var int */
            if ($productId = $listing->getCatalogProductId()) {
                $productIds[] = $productId;
            }
        }

        if (empty($productIds)) {
            return;
        }

        // qty reindex
        $this->resourceModel->clearReindexValues($ids, 'qty');
        $this->stockIndexer->executeList($productIds);
    }
}
