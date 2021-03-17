<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\Indexer\StockIndexer;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ResourceModel;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class EditFulfillment
 */
class EditFulfillment extends Action
{
    const DEFAULT_FULFILLMENT_CODE = 'DEFAULT';

    /** @var ListingRepositoryInterface */
    protected $listingRepository;

    /** @var ResourceModel */
    protected $resourceModel;

    /** @var AccountRepositoryInterface */
    protected $accountRepository;

    /** @var StockIndexer */
    protected $stockIndexer;
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;

    /**
     * @param Action\Context $context
     * @param ListingRepositoryInterface $listingRepository
     * @param ResourceModel $resourceModel
     * @param AccountRepositoryInterface $accountRepository
     * @param StockIndexer $stockIndexer
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     */
    public function __construct(
        Action\Context $context,
        ListingRepositoryInterface $listingRepository,
        ResourceModel $resourceModel,
        AccountRepositoryInterface $accountRepository,
        StockIndexer $stockIndexer,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl
    ) {
        parent::__construct($context);
        $this->listingRepository = $listingRepository;
        $this->resourceModel = $resourceModel;
        $this->accountRepository = $accountRepository;
        $this->stockIndexer = $stockIndexer;
        $this->frontendUrl = $frontendUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Edit fulfillment type by listing
     *
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var int */
        $id = $this->getRequest()->getParam('id');
        /** @var string */
        $tab = $this->getRequest()->getParam('tab');

        try {
            /** @var ListingRepositoryInterface */
            $listing = $this->listingRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Unable to edit the fulfillment type. Please try again.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var int */
        $merchantId = $listing->getMerchantId();

        try {
            /** @var AccountRepositoryInterface */
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Unable to edit the fulfillment type. Please try again.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var string */
        $countryCode = $account->getCountryCode();
        /** @var string */
        $sellerSku = $listing->getSellerSku();
        /** @var string */
        $sellerId = $account->getSellerId();
        /** @var string */
        $fulfilledBy = $listing->getFulfilledBy();
        /** @var string */
        $fulfillmentCode = $this->getFulfillmentCode($fulfilledBy, $countryCode);

        $records = $this->resourceModel->toggleFulfillmentType($fulfillmentCode, $sellerId, $sellerSku);

        if ($productId = $listing->getCatalogProductId()) {
            $this->stockIndexer->executeRow($productId);
        }

        if ($records) {
            $this->messageManager
                ->addSuccessMessage(__('The fulfillment type is in the process of being updated.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => $tab]
            );
        }

        $this->messageManager
            ->addErrorMessage(__('Unable to edit the fulfillment type. Please try again.'));
        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, 'active_tab' => $tab]
        );
    }

    /**
     * Returns the Amazon fulfillment code by marketplace
     *
     * @param string $fulfilledBy
     * @param string $countryCode
     * @return string
     */
    private function getFulfillmentCode(string $fulfilledBy, string $countryCode): string
    {
        if ($fulfilledBy != self::DEFAULT_FULFILLMENT_CODE) {
            return self::DEFAULT_FULFILLMENT_CODE;
        }

        return Definitions::getFulfillmentCode($countryCode, self::DEFAULT_FULFILLMENT_CODE);
    }
}
