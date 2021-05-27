<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

use Cart2Quote\Quotation\Model\Quote;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @package Cart2Quote\Quotation\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path to frontend_show_quote_created_by configuration in system.xml
     */
    const CREATED_BY_VISIBILITY = 'cart2quote_advanced/general/frontend_show_quote_created_by';

    /**
     * @const admin config xml route
     */
    const REJECT_PROPOSAL_DISABLE = 'cart2quote_advanced/general/enable_proposal_rejection';

    /**
     * @const quotation coupon config
     */
    const QUOTATION_COUPON_CONFIG = 'cart2quote_advanced/checkout/remove_coupon_option_for_quotation_quote';

    /**
     * Path to define custom product request form name in system.xml
     */
    const XML_PATH_NAME_CUSTOM_PRODUCT = 'cart2quote_advanced/general/name_custom_product';

    /**
     * @const admin config xml route
     */
    const XML_PATH_ENABLE_CUSTOM_REQUEST_FORM = 'cart2quote_advanced/general/enable_custom_request_form';

    /**
     * @constant Use Default \Cart2Quote\Quotation\Model\Config\Backend\Product\Quotable
     */
    const USE_DEFAULT = 2;

    /**
     * @const auto user login
     */
    const AUTO_LOGIN_ENABLE = 'cart2quote_advanced/checkout/auto_user_login';

    /**
     * @const disable checkout
     */
    const CHECKOUT_DISABLE = 'cart2quote_advanced/checkout/accept_quote_without_checkout';

    /**
     * @const auto confirm proposal
     */
    const AUTO_CONFIRM_PROPOSAL = 'cart2quote_advanced/checkout/auto_confirm_proposal';

    /**
     * @const disable product comment
     */
    const DISABLE_PRODUCT_REMARK = 'cart2quote_advanced/remarks/disable_product_remark';

    /**
     * @const hide order references
     */
    const HIDE_ORDER_REFERENCES = 'cart2quote_quotation/global/hide_order_references';

    /**
     * Path to allow_guest_quote_request in system.xml
     */
    const XML_PATH_GUEST_QUOTE_REQUEST = 'cart2quote_quote_form_settings/quote_form_settings/allow_guest_quote_request';

    /**
     * Path to hide_dashboard_prices in system.xml
     */
    const XML_PATH_HIDE_DASHBOARD_PRICES = 'cart2quote_advanced/general/hide_dashboard_prices';

    /**
     * Path to show_customer_dashboard_images in system.xml
     */
    const XML_PATH_SHOW_CUSTOMER_DASHBOARD_IMAGES = 'cart2quote_advanced/general/show_customer_dashboard_images';

    /**
     * Path to lock_proposal in system.xml
     */
    const XML_PATH_LOCK_PROPOSAL = 'cart2quote_advanced/general/lock_proposal';

    /**
     * Path to enable frontend quotation visibility in system.xml
     */
    const XML_PATH_FRONTEND_QUOTATION_VISIBILITY = 'cart2quote_quotation/global/enable';

    /**
     * Path to enable backend quotation stock check
     */
    const XML_PATH_BACKEND_STOCK_CHECK = 'cart2quote_advanced/stock/out_of_stock_backend';

    /**
     * Path to enable frontend quotation stock check
     */
    const XML_PATH_FRONTEND_STOCK_CHECK = 'cart2quote_advanced/stock/out_of_stock_frontend';

    /**
     * Path to enable frontend move to cart
     */
    const XML_PATH_MOVE_TO_CART = 'cart2quote_quotation/global/enable_move_to_cart';

    /**
     * Path to enable frontend move To Quote
     */
    const XML_PATH_MOVE_TO_QUOTATION = 'cart2quote_quotation/global/enable_move_to_quote';

    /**
     * Path to enable_direct_print in system.xml
     */
    const XML_PATH_ENABLE_DIRECT_PRINT = 'cart2quote_quotation/global/enable_direct_print';

    /**
     * Path to enable frontend Direct Quote
     */
    const XML_PATH_DIRECT_QUOTE = 'cart2quote_quotation/global/enable_direct_quote';

    /**
     * Path to cart2quote_notice in system.xml
     */
    const ADMIN_USER_LIST = 'cart2quote_quotation/global/cart2quote_notice';

    /**
     * Path to hide_item_price in system.xml
     */
    const XML_PATH_HIDE_EMAIL_REQUEST_PRICE = 'cart2quote_email/quote_request/hide_item_price';

    /**
     * Path to frontend_tier in system.xml
     */
    const XML_PATH_FRONTEND_TIER = 'cart2quote_advanced/general/frontend_tier';

    /**
     * Path to show_message_not_logged_in in system.xml
     */
    const XML_PATH_FRONTEND_SHOW_MESSAGE_NOT_LOGGED_IN = 'cart2quote_quotation/global/show_message_not_logged_in';

    /**
     * Path to enable frontend move to quote from wish list
     */
    const XML_PATH_QUOTE_A_WISH = 'cart2quote_quotation/global/enable_quote_a_wish';

    /**
     * Path to enable convert custom price
     */
    const XML_PATH_CONVERT_CUSTOM_PRICE = 'cart2quote_advanced/general/convert_custom_price';

    /**
     * Path to quote prefix
     */
    const XML_PATH_QUOTE_PREFIX = 'cart2quote_advanced/configuration/quote_prefix';

    /**
     * Path to Disable Frontend Quote Changes Visibility
     */
    const XML_PATH_QUOTE_CHANGES_VISIBILITY = 'cart2quote_advanced/lock_quote/quotation_changes';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * Data constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->customerSession = $customerSession;
        $this->_moduleList = $moduleList;
        parent::__construct(
            $context
        );
    }

    /**
     * @return bool
     */
    public function isFrontendCreatedByVisible()
    {
        return (bool)$this->scopeConfig->getValue(
            self::CREATED_BY_VISIBILITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isRejectProposalEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::REJECT_PROPOSAL_DISABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if coupons are allowed for quotation quote checkouts
     *
     * @return boolean
     */
    public function isQuotationCouponDisabled() {
        return $this->scopeConfig->getValue(
            self::QUOTATION_COUPON_CONFIG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if can change request on this quote
     *
     * @param Quote $quote
     * @return bool
     */
    public function canChangeRequestQuote($quote)
    {
        if (!$this->isAllowed($quote->getStore())) {
            return false;
        }
        if ($this->customerSession->isLoggedIn()) {
            return $quote->canChangeRequest();
        } else {
            return true;
        }
    }

    /**
     * Check if re-request quote is allowed for given store
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        return true;
    }

    /**
     * Check if this quote can be accepted
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return mixed
     */
    public function canAccept($quote)
    {
        return $quote->canAccept();
    }

    /**
     * Function to determine the current installed version of Cart2Quote
     *
     * @return mixed
     */
    public function getCart2QuoteVersion()
    {
        $moduleCode = 'Cart2Quote_Quotation';
        $moduleInfo = $this->_moduleList->getOne($moduleCode);

        return $moduleInfo['setup_version'];
    }

    /**
     * Check auto user login is turned on
     *
     * @return boolean
     */
    public function isAutoLoginEnabled()
    {
        return $this->scopeConfig->getValue(
            self::AUTO_LOGIN_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns true if checkout is disabled
     *
     * @return boolean
     */
    public function isCheckoutDisabled()
    {
        return $this->scopeConfig->getValue(
            self::CHECKOUT_DISABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if direct print is enabled
     *
     * @return boolean
     */
    public function isDirectPrintEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_DIRECT_PRINT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @used-by \Interactivated\Quotecheckout\Controller\Index\Updateordermethod::execute()
     * @return boolean
     */
    public function isAutoConfirmProposalEnabled()
    {
        return $this->scopeConfig->getValue(
            self::AUTO_CONFIRM_PROPOSAL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check disable product comment field
     *
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->scopeConfig->getValue(
            self::DISABLE_PRODUCT_REMARK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check order references
     *
     * @deprecated
     * @return mixed
     */
    public function getShowOrderReferences()
    {
        return false;
//        return $this->scopeConfig->getValue(
//            self::HIDE_ORDER_REFERENCES,
//            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
//        );
    }

    /**
     * Check if guest quote request is allowed
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param null|int|Store $store
     * @return bool
     */
    public function isAllowedGuestQuoteRequest(\Magento\Quote\Model\Quote $quote, $store = null)
    {
        if ($store === null) {
            $store = $quote->getStoreId();
        }

        $guestQuoteRequest = $this->scopeConfig->isSetFlag(
            self::XML_PATH_GUEST_QUOTE_REQUEST,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        if ($guestQuoteRequest == true) {
            $result = new \Magento\Framework\DataObject();
            $result->setIsAllowed($guestQuoteRequest);
            $this->_eventManager->dispatch(
                'quote_request_allow_guest',
                ['quote' => $quote, 'store' => $store, 'result' => $result]
            );

            $guestQuoteRequest = $result->getIsAllowed();
        }

        return $guestQuoteRequest;
    }

    /**
     * Config check if hide prices for dashboard
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return bool
     */
    public function isHidePrices($quote)
    {
        $show = $this->scopeConfig->getValue(
            self::XML_PATH_HIDE_DASHBOARD_PRICES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($show) {
            return $quote->showPrices();
        }

        return true;
    }

    /**
     * Config check if show images for dashboard
     *
     * @param $quote
     * @return mixed
     */
    public function isShowCustomerDashboardImages($quote)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SHOW_CUSTOMER_DASHBOARD_IMAGES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Function that sets or un-sets the confirm mode based on the given value
     *
     * @param bool $value
     */
    public function setActiveConfirmMode($value)
    {
        $confirmationMode = $this->scopeConfig->getValue(
            self::XML_PATH_LOCK_PROPOSAL
        );

        if ($value && $confirmationMode) {
            $this->customerSession->setQuoteConfirmation($value);
        } else {
            $this->customerSession->setQuoteConfirmation(null);
        }
    }

    /**
     * Get locked proposal value from the session
     *
     * @return bool|null
     */
    public function getActiveConfirmMode()
    {
        return $this->customerSession->getQuoteConfirmation();
    }

    /**
     * Check Frontend Quotation Visibility setting
     *
     * @return bool
     */
    public function isFrontendEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_FRONTEND_QUOTATION_VISIBILITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check Backend Stock Status Enabled setting
     *
     * @return bool
     */
    public function isStockEnabledBackend()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_BACKEND_STOCK_CHECK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check Frontend Stock Status Enabled setting
     *
     * @return bool
     */
    public function isStockEnabledFrontend()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_FRONTEND_STOCK_CHECK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if product is quotable
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $customerGroupId
     * @return bool
     */
    public function isQuotable(\Magento\Catalog\Model\Product $product, $customerGroupId)
    {
        //Check if object is product
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $quotable = $product->getData('cart2quote_quotable');
            //Product configuration has priority over global config
            if (isset($quotable) && $quotable != self::USE_DEFAULT) {
                return (boolean)$quotable;
            }
        }

        //Get global config setting
        $configQuotable = $this->scopeConfig->getValue(
            'cart2quote_quotation/global/quotable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (isset($configQuotable) && (int)$configQuotable < 2) {
            return (boolean)$configQuotable;
        }

        if ((int)$configQuotable == 2) {
            $configCustomerGroups = $this->scopeConfig->getValue(
                \Cart2Quote\Quotation\Model\Config\Backend\Quote\QuotableCustomerGroups::QUOTABLE_CUSTOMER_GROUP,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            return in_array($customerGroupId, explode(',', $configCustomerGroups));
        }

        return false;
    }

    /**
     * Check if dynamic add to quote buttons are enabled
     *
     * @return boolean
     */
    public function isDynamicAddButtonsEnabled()
    {
        return (boolean)$this->scopeConfig->getValue(
            'cart2quote_quotation/global/dynamic_add_to_buttons',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check and return module "enable move to cart" config
     *
     * @return bool
     */
    public function isMoveToCartEnabled()
    {
        return (boolean)$this->scopeConfig->getValue(
            self::XML_PATH_MOVE_TO_CART,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Move To Quote enabled
     *
     * @return bool
     */
    public function isMoveToQuoteEnabled()
    {
        return (boolean)$this->scopeConfig->getValue(
            self::XML_PATH_MOVE_TO_QUOTATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * is wish list move to quote enabled
     *
     * @return bool
     */
    public function isQuoteAWishEnabled()
    {
        return (boolean)$this->scopeConfig->getValue(
            self::XML_PATH_QUOTE_A_WISH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

     /**
     * Is direct quote enabled
     *
     * @return bool
     */
    public function isDirectQuoteEnabled()
    {
        return (boolean)$this->scopeConfig->getValue(
            self::XML_PATH_DIRECT_QUOTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if items to be moved are quotable
     *
     * @param Quote $quote
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isToQuoteAllowed($quote)
    {
        $showMessageNotLoggedIn = $this->showMessageNotLoggedIn();
        if ($showMessageNotLoggedIn && ($quote->getCustomerGroupId() == GroupInterface::NOT_LOGGED_IN_ID)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Login to request a Quote')
            );
        }

        foreach ($quote->getAllVisibleItems() as $item) {
            //if dynamic add buttons enabled and product type is configurable, check the child item
            if ($this->isDynamicAddButtonsEnabled()
                && $item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $children = $item->getChildren();
                if (!empty($children) && is_array($children) && isset($children[0])) {
                    $childItem = $children[0];
                    if ($childItem instanceof \Magento\Quote\Model\Quote\Item) {
                        //overwrite the configurable item with the child item
                        $item = $childItem;
                    }
                }
            }

            if (!$this->isQuotable($item->getProduct(), $quote->getCustomerGroupId())) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __(
                        'Quotes are not available for %1. Please remove item from your cart and try again.',
                        $item->getName()
                    )
                );
            }
        }

        return true;
    }

    /**
     * Check if items to be directly quoted are quotable
     *
     * @param Quote $quote
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isDirectQuoteAllowed($quote) {
        return $this->isToQuoteAllowed($quote);
    }

    /**
     * Display quote notice
     *
     * @param int $id
     * @return boolean
     */
    public function displayQuoteNotice($id)
    {
        $values = $this->scopeConfig->getValue(self::ADMIN_USER_LIST);

        if (isset($values)) {
            return in_array($id, explode(',', $values));
        }

        return false;
    }

    /**
     * Get hide email request price configuration
     *
     * @return boolean
     */
    public function isHideEmailRequestPrice()
    {
        return (boolean)$this->scopeConfig->getValue(
            self::XML_PATH_HIDE_EMAIL_REQUEST_PRICE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get frontend tier request configuration
     *
     * @return boolean
     */
    public function isFrontendTierEnabled()
    {
        return (boolean)$this->scopeConfig->getValue(
            self::XML_PATH_FRONTEND_TIER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Show button
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $customerGroupId
     * @param string $configPath
     * @return bool
     */
    private function showButton(\Magento\Catalog\Model\Product $product, $customerGroupId, $configPath)
    {
        $quotable = $this->isQuotable(
            $product,
            $customerGroupId
        );
        $showFromConfig = (bool)$this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        //check stock setting
        $stockCheckFrontend = $this->isStockEnabledFrontend();
        if ($stockCheckFrontend && !$product->isAvailable()) {
           return false;
        }

        return $quotable && $showFromConfig;
    }

    /**
     * Check if we can show the button on the product view
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $customerGroupId
     * @return bool
     */
    public function showButtonOnProductView(\Magento\Catalog\Model\Product $product, $customerGroupId)
    {
        return $this->showButton($product, $customerGroupId, 'cart2quote_quotation/global/show_btn_detail');
    }

    /**
     * Check if we can show the button on the products list
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $customerGroupId
     * @return bool
     */
    public function showButtonOnList(\Magento\Catalog\Model\Product $product, $customerGroupId)
    {
        return $this->showButton($product, $customerGroupId, 'cart2quote_quotation/global/show_btn_list');
    }

    /**
     * Guide to add custom request form manually.
     *
     * @return string
     */
    public function getBlogUrl()
    {
        return '<a href="https://cart2quote.zendesk.com/hc/en-us/articles/360028730291-No-Custom-Form-Request"->__(Here)</a>';
    }

    /**
     * @return string|null
     */
    public function getCustomRequestName()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NAME_CUSTOM_PRODUCT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if custom price is converted on backend currency change
     *
     * @return boolean
     */
    public function convertOnChange()
    {
        return (boolean)$this->scopeConfig->getValue(
            self::XML_PATH_CONVERT_CUSTOM_PRICE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Function to get the quote prefix
     *
     * @param int $storeId
     * @return string
     */
    public function getQuotePrefix($storeId = 0) {
        return $this->scopeConfig->getValue(
            self::XML_PATH_QUOTE_PREFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return string
     */
    public function getCustomerFullname(\Magento\Customer\Model\Data\Customer $customer)
    {
        $customerNameParts = [$customer->getFirstname(), $customer->getMiddlename(), $customer->getLastname()];
        $customerName = '';
        foreach ($customerNameParts as $customerNamePart) {
            if ($customerNamePart) {
                $customerName .= $customerNamePart;
                $customerName .= ' ';
            }
        }

        return \trim($customerName);
    }

    /**
     * Check if we can show the not logged in message
     *
     * @return bool
     */
    public function showMessageNotLoggedIn()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_FRONTEND_SHOW_MESSAGE_NOT_LOGGED_IN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if Disable Frontend Quote Changes Visibility is enabled
     *
     * @return bool
     */
    public function quoteChangesVisibility()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_QUOTE_CHANGES_VISIBILITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
