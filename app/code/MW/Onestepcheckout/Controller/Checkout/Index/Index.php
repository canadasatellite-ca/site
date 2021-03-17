<?php

namespace MW\Onestepcheckout\Controller\Checkout\Index;

// Load Mobile Detect library
if (!class_exists('Mobile_Detect')) {
    require_once __DIR__ . '/../../../lib/Mobile_Detect.php';
}

class Index extends \Magento\Checkout\Controller\Index\Index
{
    /**
     * @var \MW\Onestepcheckout\Helper\Data
     */
    protected $_dataHelper = null;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession = null;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager = null;

    /**
     * Define properties for methods
     */
    public function defineProperties()
    {
        $this->_dataHelper      = $this->_objectManager->get('MW\Onestepcheckout\Helper\Data');
        $this->_checkoutSession = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $this->_sessionManager  = $this->_objectManager->get('Magento\Framework\Session\SessionManagerInterface');
    }

	/**
     * Override checkout page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Define properties for methods
        $this->defineProperties();

        // Check module is enabled or not
        if (!$this->_dataHelper->enabledInFrontend()) {
            return parent::execute();
        }

        $registerParam = $this->getRequest()->getParam('register');
        if ($registerParam || $registerParam === '') {
            $this->_checkoutSession->setPersistentRegister('register');
        } else if ($this->_checkoutSession->getPersistentRegister()) {
            $this->_checkoutSession->unsPersistentRegister();
        }

        /** @var \Magento\Checkout\Helper\Data $checkoutHelper */
        $checkoutHelper = $this->_objectManager->get('Magento\Checkout\Helper\Data');
        if (!$checkoutHelper->canOnepageCheckout()) {
            $this->messageManager->addError(__('One-page checkout is turned off.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        if (!$this->_customerSession->isLoggedIn() && !$checkoutHelper->isAllowedGuestCheckout($quote)) {
            $this->messageManager->addError(__('Guest checkout is disabled.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $this->_customerSession->regenerateId();
        $this->_objectManager->get('Magento\Checkout\Model\Session')->setCartWasUpdated(false);
        $currentUrl = $this->_url->getUrl('*/*/*', ['_secure' => true]);
        $this->_objectManager->get('Magento\Customer\Model\Session')->setBeforeAuthUrl($currentUrl);
        $this->getOnepage()->initCheckout();

        $changeStyle = $this->checkChangeStyle(); // $changeStyle = 1, use default checkout
        $resultPage = $this->resultPageFactory->create();

        if ($changeStyle == 1) {
            $resultPage->getLayout()->unsetElement('onestepcheckout.dashboard');
            $resultPage->getLayout()->unsetElement('onestepcheckout.head');
            $resultPage->getConfig()->getTitle()->set(__('Checkout'));
        } else {
            $this->getGeoip();

            if ($this->_initAction($resultPage)) {
                if ($this->_objectManager->get('Magento\Persistent\Helper\Session')->isPersistent()) {
                    return $this->resultRedirectFactory->create()->setPath('persistent/index/saveMethod');
                }
                // Check if exists block checkout.onepage
                if ($resultPage->getLayout()->getBlock('checkout.onepage')) {
                    // Remove block checkout.onepage default magento
                    $resultPage->getLayout()->getBlock('checkout.onepage')->unsetChildren();
                }
            } else {
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }
        }

        return $resultPage;
    }

    /**
     * Check using One Step Checkout or Onepage checkout of Magento
     *
     * @return int
     */
    public function checkChangeStyle()
    {
        $changeStyle = 0;
        $this->_sessionManager->unsOs();

        if (!$this->_sessionManager->getOs()) {
            $disableOs  = (int) $this->_dataHelper->getStoreConfig('onestepcheckout/general/disable_os');
            $moduleIsEnabled = (int) $this->_dataHelper->getStoreConfig('onestepcheckout/general/enabled');

            if ($disableOs) {
                $detect     = new \Mobile_Detect;
                $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
                if($deviceType == 'phone') {
                    $status ='change';
                    $changeStyle = 1;
                } else {
                    if($moduleIsEnabled) {
                        $status = 'notchange';
                        $changeStyle = 0;
                    } else {
                        $status = 'change';
                        $changeStyle = 1;
                    }
                }
            } else {
                if ($moduleIsEnabled) {
                    $status = 'notchange';
                    $changeStyle = 0;
                } else {
                    $status = 'change';
                    $changeStyle = 1;
                }
            }

            $this->_sessionManager->setOs($status);
        }

        return $changeStyle;
    }

    public function getGeoip()
    {
        if ($this->_dataHelper->getStoreConfig('onestepcheckout/general/enable_geoip')) {
            try {
                $key             = "718b4c6e9d4b09b15cbb35e846457723cd0fe7d842401c7203069b7252f7d289";
                $date            = new \Zend_Date();
                $timezone_server = $date->get(\Zend_Date::TIMEZONE_SECS) / 3600;
                $xml             = @simplexml_load_file('http://api.ipinfodb.com/v3/ip-city/?key=' . $key . '&ip=' . $_SERVER['REMOTE_ADDR'] . '&format=xml');

                if (!$xml) {
                    return false;
                }

                $info_ip_address = $xml->children();

                if ((string)$info_ip_address[0] != 'OK' || strpos((string)$info_ip_address[3], "-") !== false) {
                    return false;
                }

                if (!empty($info_ip_address[3])) {
                    $longitude = $info_ip_address[9];
                    $hourpart  = (float)$longitude / 15;
                    $off       = $hourpart - (int)$hourpart;
                    $timezone_client = (int)$hourpart;

                    if(abs($off) > 0.5) {
                        $timezone_client = ((int)$hourpart > 0) ? (int)$hourpart + 1 : (int)$hourpart - 1;
                    }

                    $this->_coreRegistry->register('Countrycode', (string)$info_ip_address[3]);
                    $this->_coreRegistry->register('Countryname', (string)$info_ip_address[4]);
                    $Regioncode = "";
                    $Regionid   = "";

                    if ((string)$info_ip_address[5] != "-" && (string)$info_ip_address[5] != "") {
                        $this->_coreRegistry->register('Regionname', ucfirst(strtolower((string)$info_ip_address[5])));
                    } else {
                        $this->_coreRegistry->register('Regionname', "");
                    }

                    if((string)$info_ip_address[6] != "-" && (string)$info_ip_address[6] != "") {
                        $this->_coreRegistry->register('City', ucfirst(strtolower((string)$info_ip_address[6])));
                    } else {
                        $this->_coreRegistry->register('City', "");
                    }

                    if((string)$info_ip_address[7] != "-" && (string)$info_ip_address[7] != "") {
                        $this->_coreRegistry->register('Zipcode', (string)$info_ip_address[7]);
                    } else {
                        $this->_coreRegistry->register('Zipcode', "");
                    }

                    $this->_coreRegistry->register('Latitude', (string)$info_ip_address[8]);
                    $this->_coreRegistry->register('Timezoneclient', $timezone_client);
                    $this->_coreRegistry->register('Timezoneserver', $timezone_server);

                    $regionModel = $this->_objectManager->get('Magento\Directory\Model\Region');
                    $statesCollection = $regionModel->getCollection()
                        ->addCountryFilter((string)$info_ip_address[3])
                        ->load();
                    if (count($statesCollection->getData()) == 0) {
                        $statesCollection = $regionModel->getCollection()
                            ->addCountryFilter((string)$info_ip_address[4])
                            ->load();
                    }
                    foreach ($statesCollection as $state) {
                        if (!$state->getRegionId()) {
                            continue;
                        }
                        if ((string)$info_ip_address[5] != "-" && (string)$info_ip_address[5] != "") {
                            if (trim(strtolower($state->getName())) == trim(strtolower((string)$info_ip_address[5]))) {
                                $Regioncode = $state->getCode();
                                $Regionid   = $state->getRegionId();
                                break;
                            }
                        }
                    }
                    $this->_coreRegistry->register('Regionid', $Regionid);
                    $this->_coreRegistry->register('Regioncode', $Regioncode);
                } else {
                    $this->_coreRegistry->register('Countrycode', '');
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function _initAction($resultPage)
    {
        // Get checkout quote
        $quote = $this->_checkoutSession->getQuote();
        // Init address (if not init address, it can't load ajax)
        $this->initAddressInfo($quote);

        $defaultPaymentMethod = $this->initPaymentMethod($quote);
        if($defaultPaymentMethod) {
            try {
                $quote->getShippingAddress()->setPaymentMethod($defaultPaymentMethod);
                $payment = $quote->getPayment();
                $payment->importData(['method' => $defaultPaymentMethod]);
            } catch (\Exception $e) {
            }
        }

        $defaultShippingMethod = $this->initShippingMethod($quote);
        if($defaultShippingMethod) {
            $quote->getShippingAddress()->setShippingMethod($defaultShippingMethod);
        }

        // If Promotions -> Shopping Cart Price Rule has "apply action" = cart_fixed
        // Recalculate discount
        $applyRule   = $quote->getAppliedRuleIds();
        $applyAction = $this->_objectManager->get('Magento\SalesRule\Model\Rule')
            ->load($applyRule)
            ->getSimpleAction();
        if($applyAction != 'cart_fixed') {
            $quote->setTotalsCollectedFlag(false);
        }
        // End init address

        // Clear all message
        $this->_objectManager->get('Magento\Catalog\Model\Session')->unsetData('messages');
        $this->_checkoutSession->unsetData('messages');

        $resultPage->getConfig()->getTitle()->set(__('Checkout'));

        return true;
    }

    public function initAddressInfo($quote)
    {
        $quoteAddress = $quote->getBillingAddress();
        $countryid    = $quoteAddress->getCountryId();
        $postcode     = $quoteAddress->getPostcode();
        $region       = $quoteAddress->getRegion();
        $regionid     = $quoteAddress->getRegionId();
        $city         = $quoteAddress->getCity();

        if (!empty($countryid) && strpos('n/a', $countryid) === false) {
            $this->_sessionManager->setCountryId($countryid);
        } else {
            $this->_sessionManager->unsCountryId();
        }

        if (!empty($postcode) && strpos('n/a', $postcode) === false) {
            $this->_sessionManager->setPostcode($postcode);
        } else {
            $this->_sessionManager->unsPostcode();
        }

        if (!empty($region) && strpos('n/a', $region) === false) {
            $this->_sessionManager->setRegion($region);
        } else {
            $this->_sessionManager->unsRegion();
        }

        if (!empty($regionid) && strpos('n/a', $regionid) === false) {
            $this->_sessionManager->setRegionId($regionid);
        } else {
            $this->_sessionManager->unsRegionId();
        }

        if (!empty($city) && strpos('n/a', $city) === false) {
            $this->_sessionManager->setCity($city);
        } else {
            $this->_sessionManager->unsCity();
        }

        $customerAddressId = '';
        $countrygeo = (string) $this->_coreRegistry->registry('Countrycode');
        if ($this->_dataHelper->getStoreConfig('onestepcheckout/general/enable_geoip') && $countrygeo != null) {
            if (!($this->_sessionManager->getCountryId())) {
                $countryid = (string) $this->_coreRegistry->registry('Countrycode');
                if($countryid != "") {
                    $this->_sessionManager->setCountryId($countryid);
                }
            }

            if (!($this->_sessionManager->getPostcode())) {
                $postcode = (string) $this->_coreRegistry->registry('Zipcode');
                if($postcode != "") {
                    $this->_sessionManager->setPostcode($postcode);
                }

            }

            if (!($this->_sessionManager->getRegion())) {
                $region = (string) $this->_coreRegistry->registry('Regionname');
                if($region != "") {
                    $this->_sessionManager->setRegion($region);
                }
            }

            $customerAddressAbstract = $this->_objectManager->get('Magento\Customer\Model\Address\AbstractAddress');
            if ($customerAddressAbstract->getRegionModel((string)$this->_coreRegistry->registry('Regionid'))) {
                if(!($this->_sessionManager->getRegionId())) {
                    $regionid = (string) $this->_coreRegistry->registry('Regionid');
                    if($regionid != "")
                        $this->_sessionManager->setRegionId($regionid);
                }
            }

            if (!($this->_sessionManager->getCity())) {
                $city = (string) $this->_coreRegistry->registry('City');
                if ($city != "") {
                    $this->_sessionManager->setCity($city);
                }
            }

        } elseif ($this->_dataHelper->getStoreConfig('onestepcheckout/general/default_country')) {
            if (empty($countryid)) {
                $countryid = $this->_dataHelper->getStoreConfig('onestepcheckout/general/default_country');
            }
        } else {
            if (empty($countryid)) {
                $countryid = $this->_dataHelper->getStoreConfig('general/country/default');
            }
        }
        $postData = [
            'address_id'           => '',
            'firstname'            => '',
            'lastname'             => '',
            'company'              => '',
            'email'                => $quoteAddress->getEmail(),
            'street'               => ['', '', '', ''],
            'city'                 => $city,
            'region_id'            => $regionid,
            'region'               => $region,
            'postcode'             => $postcode,
            'country_id'           => $countryid,
            'telephone'            => '',
            'fax'                  => '',
            'save_in_address_book' => '0'
        ];

        if ($this->_customerSession->isLoggedIn()) {
            $customerAddressId = $this->_customerSession->getCustomer()->getDefaultBilling();
        }

        if ((isset($postData['country_id']) && $postData['country_id'] != '') || $customerAddressId) {
            $data = $this->_dataHelper->filterdata($postData, "init");
            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }

            $this->saveBilling($data, $customerAddressId, $quote);
            $this->saveShipping($data, $customerAddressId, $quote);
        } else {
            $this->_getQuote()->getShippingAddress()
                ->setCountryId('')
                ->setPostcode('')
                ->setCollectShippingRates(true);
            $this->_getQuote()->save();
        }
    }

    public function initPaymentMethod($quote)
    {
        $listmethod = [];
        $store      = $quote ? $quote->getStoreId() : null;
        $methods    = $this->_objectManager->get('Magento\Payment\Helper\Data')->getStoreMethods($store, $quote);
        foreach ($methods as $key => $method) {
            if ($this->_canUseMethod($method, $quote)) {
                $listmethod[] = $method->getCode();
            }
        }

        try {
            if (empty($listmethod)) {
                return;
            }

            if (sizeof($listmethod) == 1) {
                return $listmethod[0];
            } else {
                foreach ($listmethod as $methodname) {
                    $defaultPaymentMethod = $this->_dataHelper->getStoreConfig('onestepcheckout/general/default_paymentmethod');
                    if ($defaultPaymentMethod == $methodname) {
                        return $methodname;
                    }
                }

                return;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function initShippingMethod($quote)
    {
        $listMethod = [];
        $addresses  = $quote->getShippingAddress();
        $listShippingMethod = $addresses->getGroupedAllShippingRates();
        foreach ($listShippingMethod as $code => $_rates) {
            $listMethod[] = $code;
        }

        if (!$quote->isVirtual()) {
            if (empty($listMethod)) {
                return;
            }

            if (sizeof($listMethod) == 1) {
                return $listMethod[0] . '_' . $listMethod[0];
            } else {
                foreach ($listMethod as $methodName) {
                    $defaultShippingMethod = $this->_dataHelper->getStoreConfig('onestepcheckout/general/default_shippingmethod');
                    $shippingMethod = $methodName . '_' . $methodName;
                    if ($defaultShippingMethod == $shippingMethod) {
                        return $shippingMethod;
                    }
                }
            }
        }

        return;
    }

    /**
     * Check payment method for satisfied
     *
     * @param  string $method
     * @return boolean
     */
    public function _canUseMethod($method, $quote)
    {
        if($quote == null) {
            $quote = $this->getQuote();
        }

        if (!$method->canUseForCountry($quote->getBillingAddress()->getCountry())) {
            return false;
        }

        if (!$method->canUseForCurrency($quote->getCurrency()->getBaseCurrencyCode())) {
            return false;
        }

        /**
         * Checking for min/max order total for assigned payment method
         */
        $baseGrandTotal    = $quote->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');
        if ((!empty($minTotal) && ($baseGrandTotal < $minTotal))
            || (!empty($maxTotal) && ($baseGrandTotal > $maxTotal))
        ) {
            return false;
        }

        return true;
    }

    public function saveBilling($data, $customerAddressId, $quote)
    {
        if (empty($data)) {
            return [
                'error' => -1,
                'message' => __('Invalid data.')
            ];
        }

        $address = $quote->getBillingAddress();
        if (!empty($customerAddressId)) {
            $addressData = $this->_objectManager->get(
                'Magento\Customer\Api\AddressRepositoryInterface'
            )->getById($customerAddressId);
            if ($addressData->getId()) {
                if ($addressData->getCustomerId() != $quote->getCustomerId()) {
                    return [
                        'error' => 1,
                        'message' => __('Customer Address is not valid.')
                    ];
                }
                $address->importCustomerAddressData($addressData);
            }
        } else {
            unset($data['address_id']);
            $address->addData($data);
        }

        if(!$quote->isVirtual()) {
            $shipping = $quote->getShippingAddress();
            $shipping->setSameAsBilling(0);
        }
    }

    public function saveShipping($data, $customerAddressId, $quote)
    {
        if (empty($data)) {
            return [
                'error' => -1,
                'message' => __('Invalid data.')
            ];
        }
        $address = $quote->getShippingAddress();

        if (!empty($customerAddressId)) {
            $addressData = $this->_objectManager->get(
                'Magento\Customer\Api\AddressRepositoryInterface'
            )->getById($customerAddressId);
            if ($addressData->getId()) {
                if ($addressData->getCustomerId() != $quote->getCustomerId()) {
                    return [
                        'error' => 1,
                        'message' => __('Customer Address is not valid.')
                    ];
                }
                $address->importCustomerAddressData($addressData);
            }
        } else {
            unset($data['address_id']);
            $address->addData($data);
        }

        $address->setCollectShippingRates(true);
    }
}
