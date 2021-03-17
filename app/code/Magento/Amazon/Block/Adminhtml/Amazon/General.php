<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Block\Adminhtml\Amazon;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\ConfigManagementInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class General
 */
class General extends Template
{
    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /** @var ConfigManagementInterface $configManagement */
    protected $configManagement;

    /** @var AccountInterface */
    private $account;
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;

    /**
     * @param Context $context
     * @param AccountRepositoryInterface $accountRepository
     * @param ConfigManagementInterface $configManagement
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     * @param array $data
     */
    public function __construct(
        Context $context,
        AccountRepositoryInterface $accountRepository,
        ConfigManagementInterface $configManagement,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->accountRepository = $accountRepository;
        $this->configManagement = $configManagement;
        $this->setData('use_container', true);
        $this->frontendUrl = $frontendUrl;
    }

    /**
     * Get Amazon account
     *
     * @return AccountInterface
     * @throws NoSuchEntityException
     */
    public function getAccount()
    {
        if (null === $this->account) {
            $merchantId = $this->getMerchantId();
            $this->account = $this->accountRepository->getByMerchantId($merchantId);
        }
        /** @var AccountInterface */
        return $this->account;
    }

    /**
     * Get store name
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreName(): string
    {
        return $this->getAccount()->getName();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAccountUrl(): string
    {
        return $this->frontendUrl->getStoreDetailsUrl($this->getAccount());
    }

    public function getHomeUrl(): string
    {
        return $this->frontendUrl->getHomeUrl();
    }

    /**
     * Returns account id
     *
     * @return int
     */
    public function getMerchantId()
    {
        return $this->getRequest()->getParam('merchant_id');
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getUserGuideUrl()
    {
        /** @var string */
        $url = Definitions::UG_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getMainMenuUrl()
    {
        /** @var string */
        $url = Definitions::MAIN_MENU_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getCredentialSettingsUrl()
    {
        /** @var string */
        $url = Definitions::CREDENTIAL_SETTINGS_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingSettingsUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_SETTINGS_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getOrderSettingsUrl()
    {
        /** @var string */
        $url = Definitions::ORDER_SETTINGS_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingRulesUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_RULES_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingRulesIneligibleUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_RULES_INELIGIBLE_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingRulesAdditionsUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_RULES_ADDITIONS_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getPricingRulesUrl()
    {
        /** @var string */
        $url = Definitions::PRICING_RULES_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getPricingRulesCreateUrl()
    {
        /** @var string */
        $url = Definitions::PRICING_RULES_CREATE_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingActiveUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_ACTIVE_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingInactiveUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_INACTIVE_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingIncompleteUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_INCOMPLETE_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingInProgressUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_IN_PROGRESS_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingThirdpartyUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_THIRDPARTY_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingIneligibleUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_INELIGIBLE_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingOverridesUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_OVERRIDES_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url for ended listings
     *
     * @return string
     */
    public function getListingEndedUrl(): string
    {
        /** @var string */
        $url = Definitions::LISTING_ENDED_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingDetailActivityUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_DETAIL_ACTIVITY_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingDetailBestbuyboxUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_DETAIL_BESTBUYBOX_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingDetailLowestUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_DETAIL_LOWEST_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingOverridesFormUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_OVERRIDES_FORM_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingUpdateFormUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_UPDATE_FORM_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getListingAliasFormUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_ALIAS_FORM_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getThirdpartyCreateFormUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_THIRDPARTY_CREATE_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getThirdpartyManualFormUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_THIRDPARTY_MANUAL_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getReportPricingUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_REPORT_PRICING_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getReportDefectUrl()
    {
        /** @var string */
        $url = Definitions::LISTING_REPORT_DEFECT_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getMainMenuOrderUrl()
    {
        /** @var string */
        $url = Definitions::MAIN_MENU_ORDER_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getMainMenuAttributeUrl()
    {
        /** @var string */
        $url = Definitions::MAIN_MENU_ATTRIBUTE_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getAttributeProductsUrl()
    {
        /** @var string */
        $url = Definitions::ATTRIBUTE_PRODUCTS_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getAttributeActionsUrl()
    {
        /** @var string */
        $url = Definitions::ATTRIBUTE_ACTIONS_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getMainMenuActivityUrl()
    {
        /** @var string */
        $url = Definitions::MAIN_MENU_ACTIVITY_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getOrderDetailsUrl()
    {
        /** @var string */
        $url = Definitions::ORDER_DETAILS_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getOrderItemsUrl()
    {
        /** @var string */
        $url = Definitions::ORDER_ITEMS_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getOrderTrackingUrl()
    {
        /** @var string */
        $url = Definitions::ORDER_TRACKING_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getOrderCancellationUrl()
    {
        /** @var string */
        $url = Definitions::ORDER_CANCELLATION_URL;
        return $this->escapeHtml($url);
    }

    /**
     * Returns user guide url
     *
     * @return string
     */
    public function getMainMenuErrorUrl()
    {
        /** @var string */
        $url = Definitions::MAIN_MENU_ERROR_URL;
        return $this->escapeHtml($url);
    }
}
