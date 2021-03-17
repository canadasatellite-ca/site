<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing\Rules;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory as AccountCollectionFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory as ListingCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\CatalogRule\Api\Data\RuleInterface;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Preview
 */
class Preview extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;
    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /** @var AccountCollectionFactory $accountCollectionFactory */
    protected $accountCollectionFactory;
    /** @var ListingCollectionFactory $listingCollectionFactory */
    protected $listingCollectionFactory;
    /** @var RuleFactory $ruleFactory */
    protected $ruleFactory;
    /** @var DataPersistorInterface $dataPersistor */
    protected $dataPersistor;

    /** @var array */
    const EXCLUDED_LIST_STATUSES = [
        Definitions::ENDED_LIST_STATUS,
        Definitions::TOBEENDED_LIST_STATUS,
        Definitions::REMOVE_IN_PROGRESS_LIST_STATUS
    ];

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param AccountRepositoryInterface $accountRepository
     * @param AccountCollectionFactory $accountCollectionFactory
     * @param ListingCollectionFactory $listingCollectionFactory
     * @param RuleFactory $ruleFactory
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        AccountRepositoryInterface $accountRepository,
        AccountCollectionFactory $accountCollectionFactory,
        ListingCollectionFactory $listingCollectionFactory,
        RuleFactory $ruleFactory,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->accountRepository = $accountRepository;
        $this->accountCollectionFactory = $accountCollectionFactory;
        $this->listingCollectionFactory = $listingCollectionFactory;
        $this->ruleFactory = $ruleFactory;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Generates pricing rule edit page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        // set params
        $this->dataPersistor->set('listing_rule_post_data', $this->getRequest()->getParams());

        // compiles listing preview data
        if ($excluded = $this->syncListings()) {

            /** @var string */
            $message = 'This is a unified account and some listings ';
            $message .= 'share a common Amazon seller SKU with a previously integrated marketplace.  ';
            $message .= 'In all, ' . $excluded . ' listings exist in this ';
            $message .= 'marketplace whose eligibility is managed by another marketplace.';

            $this->messageManager
                ->addWarningMessage(__($message));
        }

        /** @var Page */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Amazon::channel_amazon_index');
        $resultPage->getConfig()->getTitle()->prepend(__('Listing Preview'));

        return $resultPage;
    }

    /**
     * Pulls changes created as a result of any listing rule changes
     * to include new listing additions, eligible listings, ineligible
     * listings, and displays messaging to the user if any listings
     * are controlled via another "unified" Amazon seller account
     *
     * @return int
     */
    private function syncListings()
    {
        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');
        /** @vara int */
        $excluded = 0;
        /** @var array */
        $processedIds = [];

        try {
            /** @var AccountInterface */
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            return $excluded;
        }

        /** @var string */
        $sellerId = $account->getSellerId();

        /** @var AccountInterface[] */
        $collection = $this->accountCollectionFactory->create();

        $collection->addFieldToFilter('seller_id', $sellerId);
        $collection->getSelect()->order(['is_active ASC', 'created_on ASC']);

        /** @var AccountInterface */
        foreach ($collection as $account) {
            if ($account->getMerchantId() == $merchantId) {
                /** @var int */
                $excluded = $this->getListingChanges($account, $processedIds);
                break;
            }

            /** @var array */
            $processedIds = array_merge($processedIds, $this->getUnifiedListingIds($account));
        }

        return $excluded;
    }

    /**
     * Registers listing rule preview data to include newly
     * discovered listings, new eligible listings, and new
     * ineligible listings.  In addition, it returns the count
     * of listings discovered as the same seller sku controlled
     * by previously registered seller account.
     *
     * @param AccountInterface $account
     * @param array $processedIds
     * @return int
     */
    private function getListingChanges(AccountInterface $account, array $processedIds)
    {
        /** @var int */
        $merchantId = $account->getMerchantId();
        /** @var array */
        $listedIds = [];
        /** @var array */
        $listedEligibleIds = [];
        /** @var array */
        $listedIneligibleIds = [];
        /** @var int */
        $count = 0;

        /** @var ListingInterface[] */
        $listingCollection = $this->listingCollectionFactory->create();

        $listingCollection->addFieldToFilter('merchant_id', $merchantId);
        $listingCollection->addFieldToFilter('catalog_product_id', ['notnull' => true]);

        /** @var ListingInterface */
        foreach ($listingCollection as $listing) {

            /** @var string */
            $sellerSku = $listing->getSellerSku();
            /** @var int */
            $productId = $listing->getCatalogProductId();
            /** @var bool */
            $listStatus = $listing->getListStatus();

            $listedIds[] = $productId;

            // create only eligible listings
            if ($listStatus != Definitions::NO_LONGER_ELIGIBLE_STATUS) {
                if (in_array($sellerSku, $processedIds)) {
                    $count++;
                    continue;
                }
                $listedEligibleIds[] = $productId;
                continue;
            }

            if (in_array($sellerSku, $processedIds)) {
                $count++;
                continue;
            }
            $listedIneligibleIds[] = $productId;
        }

        /** @var array */
        $eligibleIds = $this->getEligibleProducts();

        $this->dataPersistor->set('listing_additions', array_diff($eligibleIds, $listedIds));
        $this->dataPersistor->set('listing_eligible', array_intersect($listedIneligibleIds, $eligibleIds));
        $this->dataPersistor->set('listing_ineligible', array_diff($listedEligibleIds, $eligibleIds));

        return $count;
    }

    /**
     * Checks for unified listings that are controlled by another
     * marketplaces, and if so, returns an array of matches
     *
     * @param AccountInterface $account
     * @return array
     */
    private function getUnifiedListingIds(AccountInterface $account)
    {
        $ids = [];

        if ($account->getAuthenticationStatus()) {
            return $ids;
        }

        /** @var int */
        $merchantId = $account->getMerchantId();

        /** @var ListingInterface[] */
        $listingCollection = $this->listingCollectionFactory->create();

        $listingCollection->addFieldToFilter('merchant_id', $merchantId);
        $listingCollection->addFieldToFilter('catalog_product_id', ['notnull' => true]);

        /** @var ListingInterface */
        foreach ($listingCollection as $listing) {
            $ids[$listing->getId()] = $listing->getSellerSku();
        }

        return $ids;
    }

    /**
     * Processes listing rule engine and returns catalog ids
     * that are eligible per listing rules
     *
     * @return array
     */
    private function getEligibleProducts()
    {
        $ids = [];
        /** @var RuleInterface */
        $rules = $this->ruleFactory->create();
        $params = $this->getRequest()->getParams();
        $websiteId = $this->getRequest()->getParam('website_id');
        $conditions = [];

        // set conditions
        if (isset($params['rule']['conditions'])) {
            $conditions = $params['rule']['conditions'];
        }

        /** @var array */
        $rule['conditions'] = $conditions;
        $rules->setWebsiteIds($websiteId);
        $rules->loadPost($rule);

        // get matching product ids
        $eligibleIds = $rules->getMatchingProductIds(true);

        if (is_array($eligibleIds)) {
            foreach ($eligibleIds as $productId => $product) {
                foreach ($product as $value) {
                    if ($value) {
                        $ids[] = $productId;
                        break;
                    }
                }
            }
        }

        return array_unique($ids);
    }
}
