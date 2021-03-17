<?php

namespace MW\Onestepcheckout\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLE = 'onestepcheckout/general/enabled';
    const DELIVERY_ENABLE = 1;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_config;

	/**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $_subscriber;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_storeModel;

    /**
     * @var \Magento\Store\Model\Website
     */
    protected $_websiteModel;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface
     */
    protected $_reinitConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Config\Model\ResourceModel\Config $config
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param \Magento\Store\Model\Store $storeModel
     * @param \Magento\Store\Model\Website $websiteModel
     * @param \Magento\Framework\App\Config\ReinitableConfigInterface $reinitConfig
     */
    public function __construct(
    	\Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\ResourceModel\Config $config,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Magento\Store\Model\Store $storeModel,
        \Magento\Store\Model\Website $websiteModel,
        \Magento\Framework\App\Config\ReinitableConfigInterface $reinitConfig
    ) {
        $this->_config = $config;
    	$this->_scopeConfig = $context->getScopeConfig();
        $this->_checkoutSession = $checkoutSession;
        $this->_sessionManager = $sessionManager;
        $this->_blockFactory = $blockFactory;
        $this->_date = $date;
        $this->_layoutFactory = $layoutFactory;
        $this->_customerSession = $customerSession;
        $this->_subscriber = $subscriber;
        $this->_storeModel = $storeModel;
        $this->_websiteModel = $websiteModel;
        $this->_reinitConfig = $reinitConfig;

    	parent::__construct($context);
    }

    public function enabledInFrontend()
    {
        return (int) $this->getStoreConfig(self::XML_PATH_ENABLE);
    }

    /**
     * Retrieve store config value
     *
     * @param string $xmlPath
     * @return string|int
     */
	public function getStoreConfig($xmlPath)
	{
		return $this->_scopeConfig->getValue(
			$xmlPath,
			ScopeInterface::SCOPE_STORE
		);
	}

    /**
     * Save store config value
     *
     * @param  string $xmlPath
     * @param  string|int $value
     */
    public function saveStoreConfig($xmlPath, $value)
    {
        $this->_config->saveConfig(
            $xmlPath,
            $value,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );
    }

    /**
     * Retrive session manager object
     *
     * @return object
     */
    public function getSessionManager()
    {
        return $this->_sessionManager;
    }

    /**
     * Check has gift wrap
     *
     * @return int
     */
    public function checkSession()
    {
        $iswrap = $this->_sessionManager->getIsWrap();
        if ($iswrap) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Retrieve checkout session model object
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckout()
    {
        return $this->_checkoutSession;
    }

    /**
     * Retrieve checkout quote model object
     *
     * @return Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function onlyProductDownloadable()
    {
        $itemProduct = $this->getQuote()->getAllVisibleItems();
        foreach ($itemProduct as $item) {
            if ($item->getProduct()->getTypeId() != 'downloadable'
                && $item->getProduct()->getTypeId() != 'virtual'
                && $item->getProduct()->getTypeId() != 'giftcard'
            ) {
                return false;
            }
        }

        return true;
    }

    public function haveProductDownloadable()
    {
        $itemProduct = $this->getQuote()->getAllVisibleItems();
        foreach ($itemProduct as $item) {
            if ($item->getProduct()->getTypeId() == 'downloadable') {
                return true;
            }
        }

        return false;
    }

    public function switchTemplate()
    {
        $changeStyle = 0;
        $disableOs = $this->getStoreConfig('onestepcheckout/general/disable_os');
        $disableOsArray = explode(',', $disableOs);
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $redirectOs = false;

        if (count($disableOsArray) > 0) {
            foreach ($disableOsArray as $regex) {
                if (strlen(trim($regex)) > 3) {
                    $regex_prg = '/'.trim($regex).'/i';

                    if (preg_match($regex_prg, $userAgent)) {
                        $redirectOs = true;
                    }
                }
            }

            if ($redirectOs) {
                $changeStyle = 1;
            } else {
                if ($this->getStoreConfig('onestepcheckout/general/enabled')) {
                    $changeStyle = 0;
                } else {
                    $changeStyle = 1;
                }
            }
        }

        if ($changeStyle) {
            return "onepage.phtml";
        } else {
            return "MW_Onestepcheckout::onepage.phtml";
        }
    }

    public function isDDateRunning()
    {
        if ($this->isModuleOutputEnabled('MW_Ddate')) {
            if ($this->getStoreConfig('onestepcheckout/deliverydate/allow_options') != self::DELIVERY_ENABLE) {
                return true;
            }
        }

        return false;
    }

    public function getAdditionaldays()
    {
        $result = [];
        $week = $this->getStoreConfig('onestepcheckout/deliverydate/weekend');
        $listDay = explode(",", $this->getStoreConfig("onestepcheckout/deliverydate/enableday"));
        if (!$listDay[0]) {
            return '';
        }

        foreach ($listDay as $item) {
            $t = explode("/", $item);
            $numday = date("w", mktime(0, 0, 0, $t[0], $t[1], $t[2]));
            if (strstr($week, $numday)) {
                $result[] = $item;
            }
        }

        return implode(",", $result);
    }

    public function getNationaldays()
    {
        $result = [];
        $week = $this->getStoreConfig('onestepcheckout/deliverydate/weekend');
        $listDay = explode(",", $this->getStoreConfig("onestepcheckout/deliverydate/disableday"));
        if (!$listDay[0]) {
            return '';
        }

        foreach ($listDay as $item) {
            $t = explode("/", $item);
            $numday = date("w", mktime(0, 0, 0, $t[0], $t[1], $t[2]));
            if (!strstr($week, $numday)) {
                $result[] = $item;
            }
        }

        return implode(",", $result);
    }

    /**
     * Retrive css block
     * @return html/css
     */
    public function renderConfigCss()
    {
        return $this->_blockFactory->createBlock('MW\Onestepcheckout\Block\Checkout\Config\Css')->toHtml();
    }

    /**
     * Retrive js block
     * @return html/js
     */
    public function renderConfigJs()
    {
        return $this->_blockFactory->createBlock('MW\Onestepcheckout\Block\Checkout\Config\Js')->toHtml();
    }

    /**
     * Calculate color brightness
     * @param  string $hex
     * @param  string $percent
     * @return string
     */
    public function colorBrightness($hex, $percent)
    {
        $hash = '';
        if (stristr($hex,'#')) {
            $hex = str_replace('#','',$hex);
            $hash = '#';
        }

        // HEX TO RGB
        $rgb = [hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2))];
        for ($i = 0; $i < 3; $i++) {
            // See if brighter or darker
            if ($percent > 0) {
                // Lighter
                $rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1-$percent));
            } else {
                // Darker
                $positivePercent = $percent - ($percent*2);
                $rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1-$positivePercent));
            }
            // In case rounding up causes us to go to 256
            if ($rgb[$i] > 255) {
                $rgb[$i] = 255;
            }
        }

        // RBG to Hex
        $hex = '';
        for($i = 0; $i < 3; $i++) {
            // Convert the decimal digit to hex
            $hexDigit = dechex($rgb[$i]);
            // Add a leading zero if necessary
            if (strlen($hexDigit) == 1) {
                $hexDigit = "0" . $hexDigit;
            }
            // Append to the hex string
            $hex .= $hexDigit;
        }

        return $hash.$hex;
    }

    /**
     * Check show address book
     * @return boolean
     */
    public function showAddressBook()
    {
        if ($this->getStoreConfig('onestepcheckout/addfield/addressbook')) {
            return true;
        }

        return false;
    }

    /**
     * Check show edit cart link
     * @return boolean
     */
    public function showEditCartLink()
    {
        if ($this->getStoreConfig('onestepcheckout/addfield/editcartlink')) {
            return true;
        }

        return false;
    }

    /**
     * Check show comment
     * @return boolean
     */
    public function showComment()
    {
        if ($this->getStoreConfig('onestepcheckout/addfield/enable_messagetosystem')) {
            return true;
        }

        return false;
    }

    /**
     * Check show comment
     * Default coupon is disable
     *
     * @return boolean
     */
    public function showCouponCode()
    {
        if ($this->getStoreConfig('onestepcheckout/addfield/allowcoupon')) {
            return true;
        }

        return false;
    }

    /**
     * Check show product image
     * Default show product image is disable
     *
     * @return boolean
     */
    public function showImageProduct()
    {
        if ($this->getStoreConfig('onestepcheckout/addfield/showimageproduct')) {
            return true;
        }

        return false;
    }

    /**
     * Check show product image
     * Default enable_giftmessage is disable
     *
     * @return boolean
     */
    public function enableGiftMessage()
    {
        if ($this->getStoreConfig('onestepcheckout/addfield/enable_giftmessage')) {
            return true;
        }

        return false;
    }

    /**
     * Get Date Time
     * @return object
     */
    public function getDateTime()
    {
        return $this->_date;
    }

    /**
     * @param  string $name
     * @param  string $template
     * @return html
     */
    public function renderOnepageItemAfter($name, $template)
    {
        $layout = $this->_layoutFactory->create();
        $layout->getUpdate()->load('checkout_onepage_review');
        $layout->generateXml();
        $layout->generateBlocks();

        return $layout->getBlock($name)->setTemplate($template)->toHtml();
    }

    /**
     * Check customer is subscriber
     *
     * @return boolean
     */
    public function isSubscribed()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return true;
        } else {
            $customerId = $this->_customerSession->getCustomer()->getId();
            $isSubscriber = $this->_subscriber->loadByCustomerId($customerId)->isSubscribed();
            if (!$isSubscriber) {
                return true;
            }

            return false;
        }
    }

    /**
     * Check customer is subscriber by email
     *
     * @return boolean
     */
    public function isSubscriberByEmail($email)
    {
        $isSubscriber = $this->_subscriber->loadByEmail($email)->isSubscribed();
        if (!$isSubscriber) {
            return true;
        } else{
            return false;
        }
    }

    /**
     * Disable module if Mcore module is disabled
     */
    public function disableConfig()
    {
        $this->saveStoreConfig(self::XML_PATH_ENABLE, 0);

        $websiteCollection = $this->_websiteModel->getCollection();
        foreach ($websiteCollection as $website) {
            if ($website->getCode() != 'admin') {
                $this->_config->deleteConfig(
                    self::XML_PATH_ENABLE,
                    'websites',
                    $website->getWebsiteId()
                );
            }
        }

        $storeCollection = $this->_storeModel->getCollection();
        foreach ($storeCollection as $store) {
            if ($store->getCode() != 'admin') {
                $this->_config->deleteConfig(
                    self::XML_PATH_ENABLE,
                    'stores',
                    $store->getStoreId()
                );
            }
        }

        $this->_reinitConfig->reinit();
    }

    /**
     * @param $data
     * @param string $filter
     * @return array
     */
    public function filterdata($data, $filter = "true")
    {
        // If filter = true, assign n/a to save data when load ajax
        // If filter = false, assign null to data to load ajax when place order
        if($filter == "init") {
            $filterdata = [
                'address_id'           => '',
                'firstname'            => '',
                'lastname'             => '',
                'company'              => '',
                'email'                => null,
                'street'               => ['', '', '', ''],
                'city'                 => '',
                'region_id'            => '',
                'region'               => '',
                'postcode'             => '',
                'country_id'           => '',
                'telephone'            => '',
                'fax'                  => '',
                'month'                => null,
                'day'                  => null,
                'year'                 => null,
                'save_in_address_book' => '0'
            ];
        } else {
            if($filter == 'true') {
                $filterdata = [
                    'address_id'           => 'n/a',
                    'firstname'            => 'n/a',
                    'lastname'             => 'n/a',
                    'company'              => 'n/a',
                    'email'                => 'n/a@na.na',
                    'street'               => ['n/a', 'n/a', 'n/a', 'n/a'],
                    'city'                 => 'n/a',
                    'region_id'            => 'n/a',
                    'region'               => 'n/a',
                    'postcode'             => '.',
                    'country_id'           => 'n/a',
                    'telephone'            => 'n/a',
                    'fax'                  => 'n/a',
                    'month'                => null,
                    'day'                  => null,
                    'year'                 => null,
                    'save_in_address_book' => '0'
                ];
            } else {
                $filterdata = [
                    'address_id'           => '',
                    'firstname'            => '',
                    'lastname'             => '',
                    'company'              => '',
                    'email'                => '',
                    'street'               => ['', '', '', ''],
                    'city'                 => '',
                    'region_id'            => '',
                    'region'               => '',
                    'postcode'             => '.',
                    'country_id'           => '',
                    'telephone'            => '',
                    'fax'                  => '',
                    'month'                => null,
                    'day'                  => null,
                    'year'                 => null,
                    'save_in_address_book' => '0'
                ];
            }
        }

        $filterdata = $this->filterShowField($filterdata, $filter);

        foreach ($data as $item => $value) {
            if (!is_array($value)) {
                if ($value != '') {
                    // Fix error save address book when saveaddressbook.value = 0
                    $filterdata[$item] = $value;
                }
            } else {
                $street       = $value;
                $street_lines = $this->getStoreConfig('onestepcheckout/addfield/street_lines');

                if (isset($street[0])) {
                    if (($filter == "true" || $filter == "init") && empty($street[0])) {
                        $street[0] = "n/a";
                    }

                    switch (intval($street_lines)) {
                        case 2:
                            $filterdata[$item] = [$street[0], $street[1]];
                            break;
                        case 3:
                            $filterdata[$item] = [$street[0], $street[1], $street[2]];
                            break;
                        case 4:
                            $filterdata[$item] = [$street[0], $street[1], $street[2], $street[3]];
                            break;
                        default:
                            $filterdata[$item] = [$street[0]];
                            break;
                    }
                }
            }
        }

        return $filterdata;
    }

    /**
     * @TODO: Re-check later
     * @param $filterdata
     * @param $filter
     */
    public function filterShowField($filterdata, $filter)
    {
        if ($this->getStoreConfig('customer/address/prefix_show') != '') {
            if ($filter != "init" && $filter != "true") {
                $filterdata['prefix'] = '';
            } else {
                if ($this->getStoreConfig('customer/address/prefix_show') == 'req') {
                    $filterdata['prefix'] = 'n/a';
                } else {
                    $filterdata['prefix'] = '';
                }
            }
        }

        if ($this->getStoreConfig('customer/address/dob_show') != '') {
            if ($filter != "init" && $filter != "true") {
                $filterdata['dob'] = null;
            } else {
                if ($this->getStoreConfig('customer/address/dob_show') == 'req') {
                    $filterdata['dob'] = '01/01/1900';
                } else {
                    $filterdata['dob'] = '';
                }
            }
        }

        if ($this->getStoreConfig('customer/address/gender_show') != '') {
            if ($filter != "init" && $filter != "true") {
                $filterdata['gender'] = '';
            } else {
                if ($this->getStoreConfig('customer/address/gender_show') == 'req') {
                    $filterdata['gender'] = 'n/a';
                } else {
                    $filterdata['gender'] = '';
                }
            }
        }

        if ($this->getStoreConfig('customer/address/taxvat_show') != '') {
            if ($filter != "init" && $filter != "true") {
                $filterdata['taxvat'] = '';
            } else {
                if ($this->getStoreConfig('customer/address/taxvat_show') == 'req') {
                    $filterdata['taxvat'] = 'n/a';
                } else {
                    $filterdata['taxvat'] = '';
                }
            }
        }

        if ($this->getStoreConfig('customer/address/suffix_show') != '') {
            if ($filter != "init" && $filter != "true") {
                $filterdata['suffix'] = '';
            } else {
                if ($this->getStoreConfig('customer/address/suffix_show') == 'req') {
                    $filterdata['suffix'] = 'n/a';
                } else {
                    $filterdata['suffix'] = '';
                }
            }
        }
    }
}
