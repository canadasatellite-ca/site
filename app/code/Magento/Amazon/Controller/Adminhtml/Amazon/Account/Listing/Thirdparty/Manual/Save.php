<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing\Thirdparty\Manual;

use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\Data\ListingInterfaceFactory;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Save
 */
class Save extends Action
{
    /** @var ListingInterfaceFactory $listingFactory */
    protected $listingFactory;
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;

    /**
     * @param Action\Context $context
     * @param ListingInterfaceFactory $listingFactory
     * @param ListingRepositoryInterface $listingRepository
     */
    public function __construct(
        Action\Context $context,
        ListingInterfaceFactory $listingFactory,
        ListingRepositoryInterface $listingRepository
    ) {
        parent::__construct($context);
        $this->listingFactory = $listingFactory;
        $this->listingRepository = $listingRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Save manual update of assigned Magento product to Amazon listing
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');
        /** @var int */
        $listingId = $this->getRequest()->getParam('listing_id');
        /** @var int */
        $productId = $this->getRequest()->getParam('id');
        /** @var string */
        $sku = $this->getRequest()->getParam('sku');

        try {
            /** @var ListingInterface */
            $thirdpartyListing = $this->listingRepository->getById($listingId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Unable to load the third party listing. Please try again.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => "listing_view_thirdparty"]
            );
        }

        /** @var ListingInterface */
        $listing = $this->assignCatalogProduct($thirdpartyListing, $productId, $sku);

        try {
            $this->listingRepository->save($listing);
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(__('Unable to save the catalog association. Please try again.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => "listing_view_thirdparty"]
            );
        }

        $this->messageManager->addSuccessMessage(__('Successfully matched the Amazon listing.'));
        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, 'active_tab' => "listing_view_thirdparty"]
        );
    }

    /**
     * Adds catalog product match data to third party
     * imported Amazon listing
     *
     * Completion results in the listing moving from
     * "third party import" status to "published" status
     *
     * @return ListingInterface
     * @var int $productId
     * @var string $sku
     * @var ListingInterface $listing
     */
    private function assignCatalogProduct(ListingInterface $listing, $productId, $sku)
    {
        // add catalog product data
        $listing->setEligible(true);
        $listing->setListStatus(Definitions::ACTIVE_LIST_STATUS);
        $listing->setCatalogProductId($productId);
        $listing->setCatalogSku($sku);

        return $listing;
    }
}
