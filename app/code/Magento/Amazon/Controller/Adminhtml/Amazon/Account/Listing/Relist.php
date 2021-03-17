<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Relist
 */
class Relist extends Action
{
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;
    /** @var AccountListingRepositoryInterface $accountListingRepository */
    protected $accountListingRepository;
    /** @var ListingManagementInterface $listingManagement */
    protected $listingManagement;
    /** @var ProductRepositoryInterface $productRepository */
    protected $productRepository;
    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;

    /**
     * @param Action\Context $context
     * @param ListingRepositoryInterface $listingRepository
     * @param AccountListingRepositoryInterface $accountListingRepository
     * @param ListingManagementInterface $listingManagement
     * @param ProductRepositoryInterface $productRepository
     * @param AccountRepositoryInterface $accountRepository
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     */
    public function __construct(
        Action\Context $context,
        ListingRepositoryInterface $listingRepository,
        AccountListingRepositoryInterface $accountListingRepository,
        ListingManagementInterface $listingManagement,
        ProductRepositoryInterface $productRepository,
        AccountRepositoryInterface $accountRepository,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl
    ) {
        parent::__construct($context);
        $this->listingRepository = $listingRepository;
        $this->accountListingRepository = $accountListingRepository;
        $this->listingManagement = $listingManagement;
        $this->productRepository = $productRepository;
        $this->accountRepository = $accountRepository;
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
     * Allows merchant to attempt relist for a closed listing
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var string */
        $errorMessage = __('An error occured while processing the request. Please try again.');

        /** @var array */
        if (!$id = $this->getRequest()->getParam('id')) {
            $this->messageManager->addErrorMessage($errorMessage);
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        try {
            /** @var ListingRepositoryInterface */
            $listing = $this->listingRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($errorMessage);
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var int */
        $merchantId = $listing->getMerchantId();
        /** @var int */
        $productId = $listing->getCatalogProductId();
        /** @vae string */
        $tab = $this->getRequest()->getParam('tab');

        try {
            /** @var AccountInterface */
            $accountListing = $this->accountListingRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($errorMessage);
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        try {

            /** @var ProductInterface */
            $product = $this->productRepository->getById($productId);
            /** @var array */
            $productData = $this->listingManagement->isCatalogMatch($accountListing, $product);
            /** @var string */
            $productId = (isset($productData['product_id'])) ? $productData['product_id'] : '';
            /** @var string */
            $productIdType = (isset($productData['product_type'])) ? $productData['product_type'] : '';
            /** @var string */
            $listStatus = Definitions::VALIDATE_ASIN_LIST_STATUS;

            $listing->setProductId($productId);
            $listing->setProductIdType($productIdType);
            $listing->setListStatus($listStatus);
        } catch (NoSuchEntityException $e) {
            $listing->setCatalogProductId(null);
            $listing->setListStatus(Definitions::THIRDPARTY_LIST_STATUS);
        }

        try {
            $this->listingRepository->save($listing);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An error occured while processing the request. Please try again.')
            );
        }

        $this->messageManager->addSuccessMessage(
            __('The selected listing is in the process of being published to Amazon.')
        );
        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, "active_tab" => $tab]
        );
    }
}
