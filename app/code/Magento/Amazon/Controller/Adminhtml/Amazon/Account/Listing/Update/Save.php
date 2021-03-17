<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing\Update;

use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\Data\MultipleInterface;
use Magento\Amazon\Api\Data\VariantInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Api\MultipleRepositoryInterface;
use Magento\Amazon\Api\VariantRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Save
 */
class Save extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;
    /** @var MultipleRepositoryInterface $multipleRepository */
    protected $multipleRepository;
    /** @var VariantRepositoryInterface $variantRepository */
    protected $variantRepository;
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ListingRepositoryInterface $listingRepository
     * @param ResourceModel $resourceModel
     * @param MultipleRepositoryInterface $multipleRepository
     * @param VariantRepositoryInterface $variantRepository
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ListingRepositoryInterface $listingRepository,
        ResourceModel $resourceModel,
        MultipleRepositoryInterface $multipleRepository,
        VariantRepositoryInterface $variantRepository,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->listingRepository = $listingRepository;
        $this->resourceModel = $resourceModel;
        $this->multipleRepository = $multipleRepository;
        $this->variantRepository = $variantRepository;
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
     * Saves user selected listing update
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var int */
        $id = $this->getRequest()->getParam('id');

        try {
            /** @var ListingInterface */
            $listing = $this->listingRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Could not load the listing data. Please try again.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var int */
        $merchantId = (int)$listing->getMerchantId();
        /** @var int */
        $listStatus = $listing->getListStatus();
        /** @var string */
        $tab = $this->getRequest()->getParam('tab');

        // custom handler for current list status
        switch ($listStatus) {
            case Definitions::MISSING_CONDITION_LIST_STATUS:
                $listing = $this->processMissingCondition($listing);
                break;
            case Definitions::MULTIPLE_LIST_STATUS:
                $listing = $this->processMultipleMatches($listing);
                break;
            case Definitions::VARIANTS_LIST_STATUS:
                $listing = $this->processVariants($listing);
                break;
            default:
                $listing = $this->processNoMatchFound($listing);
                break;
        }

        // schedule ASIN lookups
        $this->resourceModel->scheduleAsinLookups($merchantId);

        try {
            $this->listingRepository->save($listing);
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(__('An error occured while saving. Please try again.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => $tab]
            );
        }

        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, 'active_tab' => $tab]
        );
    }

    /**
     * Process missing condition user update
     *
     * @param ListingInterface $listing
     * @return ListingInterface
     */
    private function processMissingCondition($listing)
    {
        /** @var int */
        if (!$condition = $this->getRequest()->getParam('condition')) {
            $this->messageManager->addErrorMessage(__('Could not assign the condition. Please try again.'));
            return $listing;
        }

        $this->messageManager->addSuccessMessage(__('The listing condition has been scheduled for update.'));

        $listing->setCondition($condition);
        $listing->setListStatus(Definitions::VALIDATE_ASIN_LIST_STATUS);

        return $listing;
    }

    /**
     * Process no match found user update
     *
     * @param ListingInterface $listing
     * @param string $asin
     * @return ListingInterface
     */
    private function processNoMatchFound($listing, $asin = null)
    {
        if (!$asin) {

            /** @var int */
            if (!$asin = $this->getRequest()->getParam('actual_asin')) {
                $this->messageManager->addErrorMessage(__('Could not assign the new ASIN. Please try again.'));
                return $listing;
            }
        }

        $this->messageManager->addSuccessMessage(
            __('The newly assigned ASIN has been updated and is in the process of validating.')
        );

        $listing->setProductIdType(Definitions::TYPE_ASIN);
        $listing->setProductId($asin);
        $listing->setAsin($asin);
        $listing->setListStatus(Definitions::VALIDATE_ASIN_LIST_STATUS);

        return $listing;
    }

    /**
     * Process multiple matches user update
     *
     * @param ListingInterface $listing
     * @return ListingInterface
     */
    private function processMultipleMatches($listing)
    {
        /** @var int */
        $multipleId = $this->getRequest()->getParam('multiple_id');

        try {
            /** @var MultipleInterface */
            $multiple = $this->multipleRepository->getById($multipleId);
        } catch (NoSuchEntityException $e) {

            /** @var string */
            if ($multipleAsin = $this->getRequest()->getParam('multiple_asin')) {
                $listing = $this->processNoMatchFound($listing, $multipleAsin);
                return $listing;
            }

            $this->messageManager->addErrorMessage(__('Could not locate the selected listing. Please try again.'));
            return $listing;
        }

        // skip if no assigned asin
        if (!$asin = $multiple->getAsin()) {
            $this->messageManager->addErrorMessage(__('Could not assign the ASIN. Please try again.'));
            return $listing;
        }

        // assign asin
        $listing->setAsin($asin);
        $listing->setProductIdType(Definitions::TYPE_ASIN);
        $listing->setProductId($asin);
        $listing->setListStatus(Definitions::VALIDATE_ASIN_LIST_STATUS);

        // update status based on remaining info required (if applicable)
        if ($listing->getVariants()) {
            $listing->setListStatus(Definitions::VARIANTS_LIST_STATUS);
        } elseif (!$listing->getCondition()) {
            $listing->setListStatus(Definitions::MISSING_CONDITION_LIST_STATUS);
        }

        return $listing;
    }

    /**
     * Process variant user update
     *
     * @param ListingInterface $listing
     * @return ListingInterface
     */
    private function processVariants($listing)
    {
        /** @var int */
        $variantId = $this->getRequest()->getParam('variant_id');

        try {
            /** @var VariantInterface */
            $variant = $this->variantRepository->getById($variantId);
        } catch (NoSuchEntityException $e) {
            // check for newly assigned asin
            if ($variantAsin = $this->getRequest()->getParam('variant_asin')) {
                $listing = $this->processNoMatchFound($listing, $variantAsin);
                return $listing;
            }

            $this->messageManager->addErrorMessage(__('Could not locate the selected listing. Please try again.'));
            return $listing;
        }

        // skip if no assigned asin
        if (!$asin = $variant->getAsin()) {
            $this->messageManager->addErrorMessage(__('Could not assign the ASIN. Please try again.'));
            return $listing;
        }

        // assign asin
        $listing->setAsin($asin);
        $listing->setProductIdType(Definitions::TYPE_ASIN);
        $listing->setProductId($asin);
        $listing->setListStatus(Definitions::VALIDATE_ASIN_LIST_STATUS);

        // needs condition
        if (!$listing->getCondition()) {
            $listing->setListStatus(Definitions::MISSING_CONDITION_LIST_STATUS);
        }

        return $listing;
    }
}
