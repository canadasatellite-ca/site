<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Settings\Listings;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\AccountListingInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Save
 */
class Save extends Action
{
    /** @var string */
    const LISTINGS = 2;
    /** @var int */
    const BY_ATTRIBUTE = 0;

    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /** @var AccountListingRepositoryInterface $accountListingRepository */
    protected $accountListingRepository;
    /** @var ProductAttributeRepositoryInterface $productAttributeRepository */
    protected $productAttributeRepository;
    /**
     * @var AscClientLogger
     */
    private $logger;
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;
    /**
     * @var \Magento\Amazon\Ui\AdminStorePageUrl
     */
    private $adminStorePageUrl;

    /**
     * @param Action\Context $context
     * @param AccountRepositoryInterface $accountRepository
     * @param AccountListingRepositoryInterface $accountListingRepository
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param AscClientLogger $logger
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     * @param \Magento\Amazon\Ui\AdminStorePageUrl $adminStorePageUrl
     */
    public function __construct(
        Action\Context $context,
        AccountRepositoryInterface $accountRepository,
        AccountListingRepositoryInterface $accountListingRepository,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        AscClientLogger $logger,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl,
        \Magento\Amazon\Ui\AdminStorePageUrl $adminStorePageUrl
    ) {
        parent::__construct($context);
        $this->accountRepository = $accountRepository;
        $this->accountListingRepository = $accountListingRepository;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->logger = $logger;
        $this->frontendUrl = $frontendUrl;
        $this->adminStorePageUrl = $adminStorePageUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Save account listing settings
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var array */
        $data = $this->getRequest()->getParams();
        /** @var string */
        $merchantId = $this->getRequest()->getParam('merchant_id');

        if (!$data['id']) {
            unset($data['id']);
        }

        try {
            /** @var AccountInterface */
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Unable to load Amazon account. Please try again.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var AccountListingInterface */
        $accountListing = $this->accountListingRepository->getByMerchantId($merchantId, true);

        // edit fulfillment data
        $data = $this->editFulfillmentData($data);
        // edit condition data
        $data = $this->editConditionData($data, $accountListing, $merchantId);
        // edit business pricing data
        $data = $this->editBusinessPricing($data);

        // add form data
        $accountListing->setData($data);

        try {
            $this->accountListingRepository->save($accountListing);
            $this->messageManager->addSuccessMessage(
                __('You have successfully saved the listing settings.')
            );
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->logger->critical('Exception occurred during saving listing settings', ['exception' => $e]);
        }

        return $resultRedirect->setUrl($this->adminStorePageUrl->settingsListings($account));
    }

    /**
     * Edits fulfillment data (based on text or select type)
     *
     * @param array $data
     * @return array
     */
    private function editFulfillmentData(array $data)
    {
        /** @var string */
        $fulfilledBy = (isset($data['fulfilled_by'])) ? $data['fulfilled_by'] : '';
        /** @var string */
        $code = (isset($data['fulfilled_by_field'])) ? $data['fulfilled_by_field'] : '';

        if ($code) {
            // if fulfilled by attribute
            if ($fulfilledBy == self::BY_ATTRIBUTE) {
                // if select type
                if ($this->obtainAttributeType($code) == 'select') {
                    $data['fulfilled_by_seller'] =
                        (isset($data['fulfilled_by_seller_select'])) ? $data['fulfilled_by_seller_select'] : '';
                    $data['fulfilled_by_amazon'] =
                        (isset($data['fulfilled_by_amazon_select'])) ? $data['fulfilled_by_amazon_select'] : '';
                }
            }
        }

        // unset select values
        unset($data['fulfilled_by_seller_select']);
        unset($data['fulfilled_by_amazon_select']);

        return $data;
    }

    /**
     * Edits condition data (based on text or select type)
     *
     * @param array $data
     * @param AccountListingInterface $accountListing
     * @param int $merchantId
     * @return array
     */
    private function editConditionData(array $data, AccountListingInterface $accountListing, $merchantId)
    {
        /** @var string */
        $listCondition = (isset($data['list_condition'])) ? $data['list_condition'] : '';
        /** @var string */
        $code = (isset($data['list_condition_field'])) ? $data['list_condition_field'] : '';

        // if listing condition by attribute
        if ($listCondition == self::BY_ATTRIBUTE) {
            // select type
            if ($this->obtainAttributeType($code) == 'select') {

                /** @var int */
                $data['list_condition_new'] =
                    $this->parseSelectValue($data, 'list_condition_new_select');
                /** @var int */
                $data['list_condition_refurbished'] =
                    $this->parseSelectValue($data, 'list_condition_refurbished_select');
                /** @var int */
                $data['list_condition_likenew'] =
                    $this->parseSelectValue($data, 'list_condition_likenew_select');
                /** @var int */
                $data['list_condition_verygood'] =
                    $this->parseSelectValue($data, 'list_condition_verygood_select');
                /** @var int */
                $data['list_condition_good'] =
                    $this->parseSelectValue($data, 'list_condition_good_select');
                /** @var int */
                $data['list_condition_acceptable'] =
                    $this->parseSelectValue($data, 'list_condition_acceptable_select');
                /** @var int */
                $data['list_condition_collectible_likenew'] =
                    $this->parseSelectValue($data, 'list_condition_collectible_likenew_select');
                /** @var int */
                $data['list_condition_collectible_verygood'] =
                    $this->parseSelectValue($data, 'list_condition_collectible_verygood_select');
                /** @var int */
                $data['list_condition_collectible_good'] =
                    $this->parseSelectValue($data, 'list_condition_collectible_good_select');
                /** @var int */
                $data['list_condition_collectible_acceptable'] =
                    $this->parseSelectValue($data, 'list_condition_collectible_acceptable_select');
            }
        }

        // unset select values
        unset(
            $data['list_condition_new_select'],
            $data['list_condition_refurbished_select'],
            $data['list_condition_likenew_select'],
            $data['list_condition_verygood_select'],
            $data['list_condition_good_select'],
            $data['list_condition_acceptable_select'],
            $data['list_condition_collectible_likenew_select'],
            $data['list_condition_collectible_verygood_select'],
            $data['list_condition_collectible_good_select'],
            $data['list_condition_collectible_acceptable_select']
        );

        return $data;
    }

    /**
     * Edits business pricing data
     *
     * @param array $data
     * @return array
     */
    private function editBusinessPricing(array $data)
    {
        if (isset($data['qty_price_one'])) {
            if (!$data['qty_price_one']) {
                $data['qty_price_one'] = null;
            }
        }
        if (isset($data['qty_price_two'])) {
            if (!$data['qty_price_two']) {
                $data['qty_price_two'] = null;
            }
        }
        if (isset($data['qty_price_three'])) {
            if (!$data['qty_price_three']) {
                $data['qty_price_three'] = null;
            }
        }
        if (isset($data['qty_price_four'])) {
            if (!$data['qty_price_four']) {
                $data['qty_price_four'] = null;
            }
        }
        if (isset($data['qty_price_five'])) {
            if (!$data['qty_price_five']) {
                $data['qty_price_five'] = null;
            }
        }

        if (isset($data['lower_bound_one'])) {
            if (!$data['lower_bound_one']) {
                $data['lower_bound_one'] = null;
            }
        }
        if (isset($data['lower_bound_two'])) {
            if (!$data['lower_bound_two']) {
                $data['lower_bound_two'] = null;
            }
        }
        if (isset($data['lower_bound_three'])) {
            if (!$data['lower_bound_three']) {
                $data['lower_bound_three'] = null;
            }
        }
        if (isset($data['lower_bound_four'])) {
            if (!$data['lower_bound_four']) {
                $data['lower_bound_four'] = null;
            }
        }
        if (isset($data['lower_bound_five'])) {
            if (!$data['lower_bound_five']) {
                $data['lower_bound_five'] = null;
            }
        }

        return $data;
    }

    /**
     * Parses select value
     *
     * @param array $data
     * @param string $field
     * @return string
     */
    private function parseSelectValue(array $data, $field)
    {
        /** @var int */
        if (isset($data[$field])) {
            return $data[$field];
        }

        return '';
    }

    /**
     * Returns product attribute type
     *
     * @param string $attributeCode
     * @return string
     */
    private function obtainAttributeType($attributeCode)
    {
        try {
            /** @var ProductAttributeInterface $attribute */
            $attribute = $this->productAttributeRepository->get($attributeCode);
        } catch (NoSuchEntityException $e) {
            return 'text';
        }

        // if attribute has options (i.e. select type)
        if ($attribute->getOptions()) {
            return 'select';
        }

        return 'text';
    }
}
