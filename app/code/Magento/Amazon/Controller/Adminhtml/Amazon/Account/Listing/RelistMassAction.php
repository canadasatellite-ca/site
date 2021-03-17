<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class RelistMassAction
 */
class RelistMassAction extends AbstractAction
{
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;
    /** @var ListingManagementInterface $listingManagement */
    protected $listingManagement;
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;
    /** @var Filter $filter */
    protected $filter;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;
    /** @var AccountListingRepositoryInterface $accountListingRepository */
    protected $accountListingRepository;
    /** @var ProductRepositoryInterface $productRepository */
    protected $productRepository;

    /**
     * @param Action\Context $context
     * @param ListingRepositoryInterface $listingRepository
     * @param ListingManagementInterface $listingManagement
     * @param ResourceModel $resourceModel
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param AccountListingRepositoryInterface $accountListingRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Action\Context $context,
        ListingRepositoryInterface $listingRepository,
        ListingManagementInterface $listingManagement,
        ResourceModel $resourceModel,
        Filter $filter,
        CollectionFactory $collectionFactory,
        AccountListingRepositoryInterface $accountListingRepository,
        ProductRepositoryInterface $productRepository,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl
    ) {
        parent::__construct(
            $context,
            $listingRepository,
            $listingManagement,
            $resourceModel,
            $filter,
            $collectionFactory,
            $frontendUrl
        );
        $this->listingRepository = $listingRepository;
        $this->listingManagement = $listingManagement;
        $this->resourceModel = $resourceModel;
        $this->accountListingRepository = $accountListingRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Generates the listing relist page
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');
        /** @var string */
        $tab = $this->getRequest()->getParam('tab');
        /** @var array */
        $ids = [];

        /** @var ListingInterface[] */
        if (!$collection = $this->getFilteredCollection($merchantId)) {
            $this->messageManager
                ->addErrorMessage(__('Please select items.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => $tab]
            );
        }

        try {
            /** @var AccountListingInterface */
            $account = $this->accountListingRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager
                ->addErrorMessage(__('Unable to load the account. Please try again.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => $tab]
            );
        }

        /** @var ListingInterface $listing */
        foreach ($collection->getItems() as $listing) {

            /** @var string */
            $listStatus = $listing->getListStatus();

            // must be fully ended to republish
            if ($listStatus == Definitions::TOBEENDED_LIST_STATUS) {
                continue;
            }

            // check for updates
            if ($productId = $listing->getCatalogProductId()) {
                try {
                    $product = $this->productRepository->getById($productId);
                } catch (NoSuchEntityException $e) {
                    continue;
                }

                if ($productData = $this->listingManagement->isCatalogMatch($account, $product)) {
                    $listing->setProductId((isset($productData['product_id'])) ? $productData['product_id'] : '');
                    $productIdType = (isset($productData['product_type'])) ? $productData['product_type'] : '';
                    $listing->setProductIdType($productIdType);

                    try {
                        $this->listingRepository->save($listing);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            $ids[] = $listing->getId();
        }

        if (!empty($ids)) {
            $this->resourceModel->scheduleListStatusUpdate($ids, Definitions::VALIDATE_ASIN_LIST_STATUS);

            $this->messageManager
                ->addSuccessMessage(__('The selected listings are in the process of being published to Amazon.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => $tab]
            );
        }

        $response = __('The selected listing could not be published at this time.  ') .
            __('Please try again.');

        $this->messageManager
            ->addWarningMessage($response);
        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, 'active_tab' => $tab]
        );
    }
}
