<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;

/**
 * Class Setup
 */
class ActionButtons extends Template
{
    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /**
     * @var \Magento\Amazon\Ui\AdminStorePageUrl
     */
    private $adminStorePageUrl;
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;
    /**
     * @var \Magento\Amazon\Api\ListingRuleRepositoryInterface
     */
    private $listingRuleRepository;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var AccountInterface
     */
    private $account;

    /**
     * @param Context $context
     * @param AccountRepositoryInterface $accountRepository
     * @param \Magento\Amazon\Ui\AdminStorePageUrl $adminStorePageUrl
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     * @param \Magento\Amazon\GraphQl\DataProvider\Websites $websites
     * @param \Magento\Amazon\Api\ListingRuleRepositoryInterface $listingRuleRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        AccountRepositoryInterface $accountRepository,
        \Magento\Amazon\Ui\AdminStorePageUrl $adminStorePageUrl,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl,
        \Magento\Amazon\Api\ListingRuleRepositoryInterface $listingRuleRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->accountRepository = $accountRepository;
        $this->adminStorePageUrl = $adminStorePageUrl;
        $this->frontendUrl = $frontendUrl;
        $this->listingRuleRepository = $listingRuleRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Returns account (if applicable)
     *
     * @return AccountInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAccount(): AccountInterface
    {
        if (null === $this->account) {
            $merchantId = (int)$this->getRequest()->getParam('merchant_id', 0);
            $this->account = $this->accountRepository->getByMerchantId($merchantId);
        }
        return $this->account;
    }

    public function getMagentoWebsite(AccountInterface $account): \Magento\Store\Api\Data\WebsiteInterface
    {
        $listingRule = $this->listingRuleRepository->getByMerchantId($account->getMerchantId());
        return $this->storeManager->getWebsite($listingRule->getWebsiteId());
    }

    private function isCurrentPage(string $name): bool
    {
        return (string)$this->getData('current') === $name;
    }

    public function isPricingRulesListPage(): bool
    {
        return $this->isCurrentPage('pricing_rules_list');
    }

    public function isListingSettingsPage(): bool
    {
        return $this->isCurrentPage('listing_settings');
    }

    public function isListingDetailsPage(): bool
    {
        return $this->isCurrentPage('listing_details');
    }

    public function isOrderSettingsPage(): bool
    {
        return $this->isCurrentPage('order_settings');
    }

    public function isStorePreviewPage(): bool
    {
        return $this->isCurrentPage('store_preview');
    }

    public function isListingRulesPage(): bool
    {
        return $this->isCurrentPage('listing_rules');
    }

    /**
     * @return \Magento\Amazon\Ui\AdminStorePageUrl
     */
    public function storeUrl(): \Magento\Amazon\Ui\AdminStorePageUrl
    {
        return $this->adminStorePageUrl;
    }

    /**
     * @return \Magento\Amazon\Ui\FrontendUrl
     */
    public function frontendUrl(): \Magento\Amazon\Ui\FrontendUrl
    {
        return $this->frontendUrl;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBackButtonUrl(): string
    {
        $account = $this->getAccount();
        if ($this->isStorePreviewPage()) {
            return $this->adminStorePageUrl->listingRulesIndex($account);
        }
        if ($this->isListingDetailsPage()) {
            $previousTab = $this->getRequest()->getParam('tab');
            return $this->adminStorePageUrl->manageListingsPage($account, $previousTab);
        }
        return $this->frontendUrl->getStoreDetailsUrl($account);
    }
}
