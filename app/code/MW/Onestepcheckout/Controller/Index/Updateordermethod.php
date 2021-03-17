<?php

namespace MW\Onestepcheckout\Controller\Index;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Updateordermethod extends \MW\Onestepcheckout\Controller\Checkout\Onepage
{
    protected $customerManagement;

    protected $orderCollectionFactory;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    const LIMIT_TIME_BETWEEN_REQUESTS = 86400;

        public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        RemoteAddress $remoteAddress,
        DateTime $dateTime,
        \Magento\Quote\Model\CustomerManagement $customerManagement
    )
    {
        $this->customerManagement = $customerManagement;
        $this->remoteAddress = $remoteAddress;
        $this->dateTime = $dateTime;
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context,
            $customerSession,
            $customerRepository,
            $accountManagement,
            $coreRegistry,
            $translateInline,
            $formKeyValidator,
            $scopeConfig,
            $layoutFactory,
            $quoteRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory);
    }

    public function execute(){
        $remoteIp = $this->remoteAddress->getRemoteAddress();

        $lastRecordCreationTimestamp = $this->loadLastRecordCreationTimestamp($remoteIp);

        if ($lastRecordCreationTimestamp && (
                self::LIMIT_TIME_BETWEEN_REQUESTS >
                ($this->dateTime->gmtTimestamp() - $lastRecordCreationTimestamp)
            )) {
            if (!$this->getRequest()->getParam('success_v3')) {
                echo json_encode(
                    [
                        'error' => 1,
                        'msg'   => __('Invalid Recaptcha!')
                    ]
                );
                exit;
            }
        }

        $this->defineProperties();

        // Validate checkout
        $checkoutHelper = $this->_objectManager->get('Magento\Checkout\Helper\Data');
        if (!$checkoutHelper->canOnepageCheckout()) {
            echo json_encode(
                [
                    'error' => 0,
                    'msg' => '',
                    'redirect' => $this->_url->getUrl('checkout/cart')
                ]
            );
            exit;
        }
        // Validate checkout
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            echo json_encode(
                [
                    'error' => 0,
                    'msg' => '',
                    'redirect' => $this->_url->getUrl('checkout/cart')
                ]
            );
            exit;
        }

        // Check has MW_DDate
		if ($this->_dataHelper->isDDateRunning()) {
            $checkDDate = $this->getRequest()->getPost('ddate');
            if ($checkDDate['date'] == '') {
                echo json_encode(
                	[
                        'error' => 1,
                        'msg'   => __('Please select Delivery Time!')
                    ]
                );
                exit;
            }
        }

        $isLoggedIn = $this->_customerSession->isLoggedIn();
        if (!$isLoggedIn) {
            if (isset($_POST['register_new_account'])) {
                $isGuest = $this->getRequest()->getPost('register_new_account');
                if ($isGuest == '1' || $this->_dataHelper->haveProductDownloadable()) {
                	// If checkbox register_new_account checked or exist downloadable product, create new account
                    $this->getOnepage()->saveCheckoutMethod('register');
                    $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');

                    // Preparing data for new customer
                    $customer = $this->_objectManager->get(
                        'Magento\Customer\Model\CustomerFactory'
                    )->create();

                    $customer->setWebsiteId($storeManager->getWebsite()->getId())
                        ->setEmail(isset($_POST['billing']['email']) ? $_POST['billing']['email'] : '')
                        ->setPrefix(isset($_POST['billing']['prefix']) ? $_POST['billing']['prefix'] : '')
                        ->setFirstname(isset($_POST['billing']['firstname']) ? $_POST['billing']['firstname']: '')
                        ->setLastname(isset($_POST['billing']['lastname']) ? $_POST['billing']['lastname'] : '')
                        ->setMiddlename(isset($_POST['billing']['middlename']) ? $_POST['billing']['middlename']: '')
                        ->setSuffix(isset($_POST['billing']['suffix']) ? $_POST['billing']['suffix']: '')
                        ->setDob(isset($_POST['dob']) ? $_POST['dob']: '')
                        ->setTaxvat(isset($_POST['billing']['taxvat']) ? $_POST['billing']['taxvat']: '')
                        ->setGender(isset($_POST['billing']['gender']) ? $_POST['billing']['gender']: '')
                        ->setPassword(isset($_POST['billing']['customer_password']) ? $_POST['billing']['customer_password'] : '');

                    // Set customer information to quote
                    $quote->setCustomer($customer->getDataModel())
                        ->setPasswordHash($customer->getPasswordHash());
                } else {
                    $this->getOnepage()->saveCheckoutMethod('guest');
                }
            } else {
                // Fix for persistent
                if($this->getCheckout()->getPersistentRegister()
                	&& $this->getCheckout()->getPersistentRegister() == "register"
                ) {
                    $this->getOnepage()->saveCheckoutMethod('register');
                } else {
                    if(!$this->_dataHelper->getStoreConfig('onestepcheckout/general/allowguestcheckout')
                    	|| !$this->_dataHelper->getStoreConfig('checkout/options/guest_checkout')
                    	|| $this->_dataHelper->haveProductDownloadable()
                    ) {
                        $this->getOnepage()->saveCheckoutMethod('register');
                        $this->getOnepage()->saveCheckoutMethod('register');
                        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');

                        // Preparing data for new customer
                        $customer = $this->_objectManager->get(
                            'Magento\Customer\Model\CustomerFactory'
                        )->create();

                        $email = isset($_POST['billing']['email']) ? $_POST['billing']['email'] : '';
                        if ($email){
                            $customer1 = $this->_objectManager->get(
                                'Magento\Customer\Model\CustomerFactory'
                            )->create();
                            $customer1->loadByEmail($email);
                            if ($customer1->getId()) {
                                if(!$customer1->getPasswordHash()){
                                    $customer->setEntityId($customer1->getEntityId());
                                    $this->_customerSession->setCustomerId($customer1->getEntityId());
                                } else {
                                    $password = isset($_POST['billing']['customer_password']) ? $_POST['billing']['customer_password'] : '';
                                    $accountManagement = $this->_objectManager->get('Magento\Customer\Api\AccountManagementInterface');

                                    $customer2 = $accountManagement->authenticate($email, $password);
                                    /*$this->_customerSession->setCustomerDataAsLoggedIn($customer2);
                                    $this->_customerSession->regenerateId();*/
                                    //$customer->setEntityId($customer1->getEntityId());
                                    $this->getOnepage()->saveCheckoutMethod('guest');
                                }
                            }
                        }

                        $customer->setWebsiteId($storeManager->getWebsite()->getId())
                            ->setEmail(isset($_POST['billing']['email']) ? $_POST['billing']['email'] : '')
                            ->setPrefix(isset($_POST['billing']['prefix']) ? $_POST['billing']['prefix'] : '')
                            ->setFirstname(isset($_POST['billing']['firstname']) ? $_POST['billing']['firstname']: '')
                            ->setLastname(isset($_POST['billing']['lastname']) ? $_POST['billing']['lastname'] : '')
                            ->setMiddlename(isset($_POST['billing']['middlename']) ? $_POST['billing']['middlename']: '')
                            ->setSuffix(isset($_POST['billing']['suffix']) ? $_POST['billing']['suffix']: '')
                            ->setDob(isset($_POST['dob']) ? $_POST['dob']: '')
                            ->setTaxvat(isset($_POST['billing']['taxvat']) ? $_POST['billing']['taxvat']: '')
                            ->setGender(isset($_POST['billing']['gender']) ? $_POST['billing']['gender']: '')
                            ->setPassword(isset($_POST['billing']['customer_password']) ? $_POST['billing']['customer_password'] : '');

                        // Set customer information to quote
                        $quote->setCustomer($customer->getDataModel())
                            ->setPasswordHash($customer->getPasswordHash());
                    } else {
                        $this->getOnepage()->saveCheckoutMethod('guest');
                    }
                }
            }
        }


        // Save billing address
        if ($this->getRequest()->isPost()) {
            $billingData = $this->_dataHelper->filterdata(
            	$this->getRequest()->getPost('billing', []),
            	false
            );

            if ($isLoggedIn) {
                $this->saveAddress('billing', $billingData);
            }

            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
            if ($this->getRequest()->getPost('billing_address_id') != ""
            	&& (!isset($billingData['save_in_address_book'])
            		|| (isset($billingData['save_in_address_book']) && $billingData['save_in_address_book']) == 0)
            ) {
                $customerAddressId = "";
            }

            if ($isLoggedIn
            	&& (isset($billingData['save_in_address_book']) && $billingData['save_in_address_book'] == 1)
            	&& !$this->_dataHelper->getStoreConfig('onestepcheckout/addfield/addressbook')
            ) {
                $customerAddressId = $this->getDefaultAddress('billing');
            }

            if (isset($billingData['email'])) {
                $billingData['email'] = trim($billingData['email']);
                if ($this->_dataHelper->isSubscriberByEmail($billingData['email'])) {
                    if ($this->getRequest()->getParam('subscribe_newsletter') == '1') {
                        if ($isLoggedIn) {
                            $customer = $this->_customerSession->getCustomer();
                            $this->_objectManager->get(
                                'Magento\Newsletter\Model\SubscriberFactory'
                            )->create()->subscribeCustomerById($customer->getId());
                        } else {
                            $this->saveSubscriber($billingData['email']);
                        }
                    }
                }
            }

            $address = $this->_objectManager->get('Magento\Quote\Model\Quote\Address');
            if ($customerAddressId) {
                $addressData = $this->_objectManager->get('Magento\Customer\Api\AddressRepositoryInterface')
                    ->getById($customerAddressId)
                    ->__toArray();
                $billingData = array_merge($billingData, $addressData);
            }

            $address->setData($billingData);
            $this->getOnepage()->getQuote()->setBillingAddress($address);
            $commentData = nl2br($this->getRequest()->getPost('onestepcheckout_comments'));
            $deliveryStatus = $this->getRequest()->getPost('deliverydate');
            $deliveryDate = $this->getRequest()->getPost('onestepcheckout_date');
            $deliveryTime = $this->getRequest()->getPost('onestepcheckout_time');

            // Get Delivery DDate
            $scheduled = $this->getRequest()->getPost('ddate');
            if ($scheduled || $deliveryDate) {
                if ($this->_dataHelper->isDDateRunning()) {
                    if ($scheduled && !$deliveryDate) {
                        $deliveryDateOfDdate = $scheduled['date'];
                        $deliveryTimeOfDdate = $scheduled['dtime'];
                        $deliveryTime = $scheduled['osc_dtime'];
                        $deliveryStatus = $scheduled['osc_deliverydate'];
                    } else {
                        $dateArray = explode('/', $deliveryDate);
                        $deliveryDateOfDdate = $dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0];
                        $deliveryTimeOfDdate = $this->_objectManager->get('MW\Ddate\Model\Dtime')->getDtimeIdFromTime($deliveryTime);
                    }

                    // Save in Ddate Database
                    $ddate = [];
                    $ddate['date'] = $deliveryDateOfDdate;
                    $ddate['dtime'] = $deliveryTimeOfDdate;
                    $ddate['ddate_comment'] = $commentData;
                    $this->_objectManager->get('MW\Ddate\Model\Type\Onepage')->saveDdate($ddate);
                }else{
                    $deliveryDateOfDdate = $deliveryDate;
                }

				// Save comment
                $deliveryInfo = [$commentData, $deliveryStatus, $deliveryDateOfDdate, $deliveryTime];
                $this->_sessionManager->setDeliveryInforOrder($deliveryInfo);
                $this->_sessionManager->setDeliveryInforEmail($deliveryInfo);
            } else {
                // Save comment
                $deliveryInfo = [$commentData, "", "", ""];
                $this->_sessionManager->setDeliveryInforOrder($deliveryInfo);
                $this->_sessionManager->setDeliveryInforEmail($deliveryInfo);
            }

            if (isset($billingData['save_into_account'])
            	&& intval($billingData['save_into_account']) == 1
            	&& $isLoggedIn
            ) {
                $this->setAccountInfoSession($billingData);
            }
        }

        // Save shipping address
        $isclick = $this->getRequest()->getPost('ship_to_same_address');
        $ship    = "billing";
        if ($isclick != '1') {
            $ship = "shipping";
        }
        if ($this->getRequest()->getPost()) {
            $shippingData = $this->_dataHelper->filterdata($this->getRequest()->getPost($ship, []), false);
            if ($isLoggedIn && !$isclick) {
                $this->saveAddress('shipping', $shippingData);
            }
            if ($isclick == '1') {
                $shippingData['same_as_billing'] = 1;
            }
            // Change address if user change infomation
            // Reassign customeraddressid and save to shipping
            $customeraddressid = $this->getRequest()->getPost($ship . '_address_id', false);

            // If user chage shipping, billing infomation but not save to database
            if ($isclick || ($this->getRequest()->getPost('shipping_address_id') != ""
            		&& (!isset($shippingData['save_in_address_book']) || (isset($shippingData['save_in_address_book']) && $shippingData['save_in_address_book'] == 0)))
            ) {
                $customeraddressid = "";
            }

            if (!$isclick && $isLoggedIn
            	&& (isset($shippingData['save_in_address_book']) && $shippingData['save_in_address_book'] == 1)
            	&& !$this->_dataHelper->getStoreConfig('onestepcheckout/addfield/addressbook')
            ) {
                $customeraddressid = $this->getDefaultAddress('shipping');
            }

            $this->getOnepage()->saveShipping($shippingData, $customeraddressid);
        }

        // Save shipping method
        if ($this->getRequest()->isPost()) {
            $shippingMethodData = $this->getRequest()->getPost('shipping_method', '');
            $resultSaveShippingMethod = $this->getOnepage()->saveShippingMethod($shippingMethodData);
            if (!$resultSaveShippingMethod) {
            	$eventManager = $this->_objectManager->get('Magento\Framework\Event\ManagerInterface');
                $eventManager->dispatch(
                    'checkout_controller_onepage_save_shipping_method',
                    [
                        'request' => $this->getRequest(),
                        'quote'   => $this->getOnepage()->getQuote()
                    ]
                );
            }
            $this->getOnepage()->getQuote()->collectTotals();
        }

        // Save payment method
        $this->getOnepage()->getQuote()->getPayment()->setMethodInstance(null);
        $dataSavePayment = $this->getRequest()->getPost('payment', []);

        try {
            $this->getOnepage()->savePayment($dataSavePayment);
        } catch (\Exception $e) {
            echo json_encode(
            	[
	                'error' => 1,
	                'msg' => $e->getMessage()
            	]
            );
            exit;
        }

        $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
        if (!empty($redirectUrl)) {
            if(!$quote->getCustomerId()){
                try{
                    $this->customerManagement->populateCustomerInfo($quote);
                    $quote->save();
                } catch(\Exception $e){
                    $d = $e->getMessage();
                }
            }
            echo json_encode(
            	[
	                'error' => 0,
	                'msg' => '',
	                'redirect' => $redirectUrl
                ]
            );
            exit;
        }

        if ($orderData = $this->getRequest()->getPost('payment', false)) {
            $this->getOnepage()->getQuote()->getPayment()->importData($orderData);
        }

        $paymentMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod();
        $this->_sessionManager->unsErrorpayment();

        if ($paymentMethod == "hosted_pro"
            || $paymentMethod == "payflow_link"
            || $paymentMethod == "payflow_advanced"
        ) {
            echo json_encode(
                [
                    'error' => 0,
                    'msg' => "hosted_pro"
                ]
            );
            exit;
        } else if ($paymentMethod == "authorizenet_directpost") {
            $this->_objectManager->get(
                'Magento\Authorizenet\Model\Directpost\Session'
            )->setQuoteId($this->_checkoutSession->getQuote()->getId());

            $this->_objectManager->get(
                'Magento\Checkout\Model\Type\Onepage'
            )->getCheckoutMethod();

            $result = new \Magento\Framework\DataObject();
            try {
                $this->_objectManager->get(
                    'Magento\Quote\Api\CartManagementInterface'
                )->placeOrder(
                    $this->_checkoutSession->getQuote()->getId()
                );

                $result->setData('success', true);

                $this->_objectManager->get(
                    'Magento\Framework\Event\ManagerInterface'
                )->dispatch(
                    'checkout_directpost_placeOrder',
                    [
                        'result' => $result,
                        'action' => $this
                    ]
                );
                $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            } catch (\Exception $e) {
                echo json_encode(
                    [
                        'error' => 1,
                        'msg'   => $e->getMessage()
                    ]
                );
                exit;
            }

            $directpostData = $result->getAuthorizenetDirectpost();

            if (isset($redirectUrl)) {
                echo json_encode(
                    [
                        'error'    => 0,
                        'msg'      => '',
                        'directpost' => $directpostData['fields'],
                        'redirect' => $redirectUrl
                    ]
                );
                exit;
            }

            $this->getOnepage()->getQuote()->save();
            echo json_encode(
                [
                    'error'    => 0,
                    'msg'      => '',
                    'directpost' => $directpostData['fields'],
                    'redirect' => $this->_url->getUrl('checkout/onepage/success')
                ]
            );
            exit;

            return;
        } else {
            $result = new \Magento\Framework\DataObject();
            try {
                $this->getOnepage()->saveOrder();
                $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
                $result->setData('success', true);
                $result->setData('error', false);
            } catch (\Exception $e) {
                $data = [
                    'error' => 1,
                    'msg'   => $e->getMessage(),
                ];
                $reloadcheckoutpage = $quote->getData('reloadcheckoutpage');
                if ($reloadcheckoutpage){
                    $data['redirect'] = $this->_url->getUrl('checkout');
                }
                echo json_encode(
                    $data
                );
                exit;
            }
            /**
             *
             * <tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Magento\Framework\Exception\NoSuchEntityException: No such entity with customerId =  in <a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Exception/NoSuchEntityException.php:45'>/new/mag/mag/vendor/magento/framework/Exception/NoSuchEntityException.php</a> on line <i>45</i></th></tr>
            <tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
            <tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
            <tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0026</td><td bgcolor='#eeeeec' align='right'>400208</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='/new/mag/mag/pub/index.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/pub/index.php:0'>.../index.php<b>:</b>0</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>2</td><td bgcolor='#eeeeec' align='center'>2.0624</td><td bgcolor='#eeeeec' align='right'>7568456</td><td bgcolor='#eeeeec'>Magento\Framework\App\Bootstrap->run( ??? )</td><td title='/new/mag/mag/pub/index.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/pub/index.php:86'>.../index.php<b>:</b>86</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>3</td><td bgcolor='#eeeeec' align='center'>2.0640</td><td bgcolor='#eeeeec' align='right'>7675144</td><td bgcolor='#eeeeec'>Magento\Framework\App\Http->launch(  )</td><td title='/new/mag/mag/vendor/magento/framework/App/Bootstrap.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/App/Bootstrap.php:258'>.../Bootstrap.php<b>:</b>258</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>4</td><td bgcolor='#eeeeec' align='center'>2.1957</td><td bgcolor='#eeeeec' align='right'>11023984</td><td bgcolor='#eeeeec'>Magento\Framework\App\FrontController\Interceptor->dispatch( ??? )</td><td title='/new/mag/mag/vendor/magento/framework/App/Http.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/App/Http.php:135'>.../Http.php<b>:</b>135</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>5</td><td bgcolor='#eeeeec' align='center'>2.1987</td><td bgcolor='#eeeeec' align='right'>11077016</td><td bgcolor='#eeeeec'>Magento\Framework\App\FrontController\Interceptor->___callPlugins( ???, ???, ??? )</td><td title='/new/mag/mag/var/generation/Magento/Framework/App/FrontController/Interceptor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/var/generation/Magento/Framework/App/FrontController/Interceptor.php:26'>.../Interceptor.php<b>:</b>26</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>6</td><td bgcolor='#eeeeec' align='center'>2.2044</td><td bgcolor='#eeeeec' align='right'>11147032</td><td bgcolor='#eeeeec'>Magento\PageCache\Model\App\FrontController\BuiltinPlugin->aroundDispatch( ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php:142'>.../Interceptor.php<b>:</b>142</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>7</td><td bgcolor='#eeeeec' align='center'>2.2079</td><td bgcolor='#eeeeec' align='right'>11172616</td><td bgcolor='#eeeeec'>Magento\Framework\App\FrontController\Interceptor->Magento\Framework\Interception\{closure}( ??? )</td><td title='/new/mag/mag/vendor/magento/module-page-cache/Model/App/FrontController/BuiltinPlugin.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-page-cache/Model/App/FrontController/BuiltinPlugin.php:73'>.../BuiltinPlugin.php<b>:</b>73</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>8</td><td bgcolor='#eeeeec' align='center'>2.2079</td><td bgcolor='#eeeeec' align='right'>11172992</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->invokeNext( ???, ???, ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php:138'>.../Interceptor.php<b>:</b>138</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>9</td><td bgcolor='#eeeeec' align='center'>2.2083</td><td bgcolor='#eeeeec' align='right'>11176224</td><td bgcolor='#eeeeec'>Magento\PageCache\Model\App\FrontController\VarnishPlugin->aroundDispatch( ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:67'>.../Chain.php<b>:</b>67</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>10</td><td bgcolor='#eeeeec' align='center'>2.2083</td><td bgcolor='#eeeeec' align='right'>11176224</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->Magento\Framework\Interception\Chain\{closure}( ??? )</td><td title='/new/mag/mag/vendor/magento/module-page-cache/Model/App/FrontController/VarnishPlugin.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-page-cache/Model/App/FrontController/VarnishPlugin.php:55'>.../VarnishPlugin.php<b>:</b>55</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>11</td><td bgcolor='#eeeeec' align='center'>2.2083</td><td bgcolor='#eeeeec' align='right'>11176600</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->invokeNext( ???, ???, ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:63'>.../Chain.php<b>:</b>63</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>12</td><td bgcolor='#eeeeec' align='center'>2.4574</td><td bgcolor='#eeeeec' align='right'>17527672</td><td bgcolor='#eeeeec'>Mirasvit\SearchAutocomplete\Plugin\ResponsePlugin->aroundDispatch( ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:67'>.../Chain.php<b>:</b>67</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>13</td><td bgcolor='#eeeeec' align='center'>2.4574</td><td bgcolor='#eeeeec' align='right'>17527672</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->Magento\Framework\Interception\Chain\{closure}( ??? )</td><td title='/new/mag/mag/vendor/mirasvit/module-search-autocomplete/src/SearchAutocomplete/Plugin/ResponsePlugin.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/mirasvit/module-search-autocomplete/src/SearchAutocomplete/Plugin/ResponsePlugin.php:153'>.../ResponsePlugin.php<b>:</b>153</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>14</td><td bgcolor='#eeeeec' align='center'>2.4575</td><td bgcolor='#eeeeec' align='right'>17528048</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->invokeNext( ???, ???, ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:63'>.../Chain.php<b>:</b>63</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>15</td><td bgcolor='#eeeeec' align='center'>2.4591</td><td bgcolor='#eeeeec' align='right'>17539808</td><td bgcolor='#eeeeec'>Magento\Framework\Module\Plugin\DbStatusValidator->aroundDispatch( ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:67'>.../Chain.php<b>:</b>67</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>16</td><td bgcolor='#eeeeec' align='center'>2.4593</td><td bgcolor='#eeeeec' align='right'>17539776</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->Magento\Framework\Interception\Chain\{closure}( ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Module/Plugin/DbStatusValidator.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Module/Plugin/DbStatusValidator.php:69'>.../DbStatusValidator.php<b>:</b>69</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>17</td><td bgcolor='#eeeeec' align='center'>2.4593</td><td bgcolor='#eeeeec' align='right'>17540152</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->invokeNext( ???, ???, ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:63'>.../Chain.php<b>:</b>63</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>18</td><td bgcolor='#eeeeec' align='center'>2.4601</td><td bgcolor='#eeeeec' align='right'>17577944</td><td bgcolor='#eeeeec'>Magento\Store\App\FrontController\Plugin\RequestPreprocessor->aroundDispatch( ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:67'>.../Chain.php<b>:</b>67</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>19</td><td bgcolor='#eeeeec' align='center'>2.4602</td><td bgcolor='#eeeeec' align='right'>17577944</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->Magento\Framework\Interception\Chain\{closure}( ??? )</td><td title='/new/mag/mag/vendor/magento/module-store/App/FrontController/Plugin/RequestPreprocessor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-store/App/FrontController/Plugin/RequestPreprocessor.php:94'>.../RequestPreprocessor.php<b>:</b>94</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>20</td><td bgcolor='#eeeeec' align='center'>2.4602</td><td bgcolor='#eeeeec' align='right'>17578320</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->invokeNext( ???, ???, ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:63'>.../Chain.php<b>:</b>63</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>21</td><td bgcolor='#eeeeec' align='center'>2.4605</td><td bgcolor='#eeeeec' align='right'>17579224</td><td bgcolor='#eeeeec'>Magento\Framework\App\FrontController\Interceptor->___callParent( ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:70'>.../Chain.php<b>:</b>70</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>22</td><td bgcolor='#eeeeec' align='center'>2.4605</td><td bgcolor='#eeeeec' align='right'>17579224</td><td bgcolor='#eeeeec'>Magento\Framework\App\FrontController->dispatch( ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php:74'>.../Interceptor.php<b>:</b>74</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>23</td><td bgcolor='#eeeeec' align='center'>2.5421</td><td bgcolor='#eeeeec' align='right'>20843320</td><td bgcolor='#eeeeec'>MW\Onestepcheckout\Controller\Index\Updateordermethod\Interceptor->dispatch( ??? )</td><td title='/new/mag/mag/vendor/magento/framework/App/FrontController.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/App/FrontController.php:55'>.../FrontController.php<b>:</b>55</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>24</td><td bgcolor='#eeeeec' align='center'>2.5457</td><td bgcolor='#eeeeec' align='right'>20946920</td><td bgcolor='#eeeeec'>MW\Onestepcheckout\Controller\Index\Updateordermethod\Interceptor->___callPlugins( ???, ???, ??? )</td><td title='/new/mag/mag/var/generation/MW/Onestepcheckout/Controller/Index/Updateordermethod/Interceptor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/var/generation/MW/Onestepcheckout/Controller/Index/Updateordermethod/Interceptor.php:143'>.../Interceptor.php<b>:</b>143</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>25</td><td bgcolor='#eeeeec' align='center'>2.5537</td><td bgcolor='#eeeeec' align='right'>21270984</td><td bgcolor='#eeeeec'>Magento\Tax\Model\App\Action\ContextPlugin->aroundDispatch( ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php:142'>.../Interceptor.php<b>:</b>142</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>26</td><td bgcolor='#eeeeec' align='center'>2.5538</td><td bgcolor='#eeeeec' align='right'>21270984</td><td bgcolor='#eeeeec'>MW\Onestepcheckout\Controller\Index\Updateordermethod\Interceptor->Magento\Framework\Interception\{closure}( ??? )</td><td title='/new/mag/mag/vendor/magento/module-tax/Model/App/Action/ContextPlugin.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-tax/Model/App/Action/ContextPlugin.php:91'>.../ContextPlugin.php<b>:</b>91</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>27</td><td bgcolor='#eeeeec' align='center'>2.5538</td><td bgcolor='#eeeeec' align='right'>21271360</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->invokeNext( ???, ???, ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php:138'>.../Interceptor.php<b>:</b>138</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>28</td><td bgcolor='#eeeeec' align='center'>2.5602</td><td bgcolor='#eeeeec' align='right'>21574136</td><td bgcolor='#eeeeec'>Magento\Weee\Model\App\Action\ContextPlugin->aroundDispatch( ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:67'>.../Chain.php<b>:</b>67</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>29</td><td bgcolor='#eeeeec' align='center'>2.5605</td><td bgcolor='#eeeeec' align='right'>21574456</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->Magento\Framework\Interception\Chain\{closure}( ??? )</td><td title='/new/mag/mag/vendor/magento/module-weee/Model/App/Action/ContextPlugin.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-weee/Model/App/Action/ContextPlugin.php:112'>.../ContextPlugin.php<b>:</b>112</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>30</td><td bgcolor='#eeeeec' align='center'>2.5605</td><td bgcolor='#eeeeec' align='right'>21574832</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->invokeNext( ???, ???, ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:63'>.../Chain.php<b>:</b>63</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>31</td><td bgcolor='#eeeeec' align='center'>2.5608</td><td bgcolor='#eeeeec' align='right'>21576472</td><td bgcolor='#eeeeec'>Magento\Store\App\Action\Plugin\StoreCheck->aroundDispatch( ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:67'>.../Chain.php<b>:</b>67</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>32</td><td bgcolor='#eeeeec' align='center'>2.5613</td><td bgcolor='#eeeeec' align='right'>21580592</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->Magento\Framework\Interception\Chain\{closure}( ??? )</td><td title='/new/mag/mag/vendor/magento/module-store/App/Action/Plugin/StoreCheck.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-store/App/Action/Plugin/StoreCheck.php:44'>.../StoreCheck.php<b>:</b>44</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>33</td><td bgcolor='#eeeeec' align='center'>2.5614</td><td bgcolor='#eeeeec' align='right'>21580968</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->invokeNext( ???, ???, ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:63'>.../Chain.php<b>:</b>63</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>34</td><td bgcolor='#eeeeec' align='center'>2.5617</td><td bgcolor='#eeeeec' align='right'>21583408</td><td bgcolor='#eeeeec'>Magento\Customer\Model\App\Action\ContextPlugin->aroundDispatch( ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:67'>.../Chain.php<b>:</b>67</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>35</td><td bgcolor='#eeeeec' align='center'>2.5624</td><td bgcolor='#eeeeec' align='right'>21609344</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->Magento\Framework\Interception\Chain\{closure}( ??? )</td><td title='/new/mag/mag/vendor/magento/module-customer/Model/App/Action/ContextPlugin.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-customer/Model/App/Action/ContextPlugin.php:61'>.../ContextPlugin.php<b>:</b>61</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>36</td><td bgcolor='#eeeeec' align='center'>2.5624</td><td bgcolor='#eeeeec' align='right'>21609720</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->invokeNext( ???, ???, ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:63'>.../Chain.php<b>:</b>63</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>37</td><td bgcolor='#eeeeec' align='center'>2.5629</td><td bgcolor='#eeeeec' align='right'>21613456</td><td bgcolor='#eeeeec'>Magento\Store\App\Action\Plugin\Context->aroundDispatch( ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:67'>.../Chain.php<b>:</b>67</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>38</td><td bgcolor='#eeeeec' align='center'>2.5999</td><td bgcolor='#eeeeec' align='right'>22456576</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->Magento\Framework\Interception\Chain\{closure}( ??? )</td><td title='/new/mag/mag/vendor/magento/module-store/App/Action/Plugin/Context.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-store/App/Action/Plugin/Context.php:106'>.../Context.php<b>:</b>106</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>39</td><td bgcolor='#eeeeec' align='center'>2.5999</td><td bgcolor='#eeeeec' align='right'>22456952</td><td bgcolor='#eeeeec'>Magento\Framework\Interception\Chain\Chain->invokeNext( ???, ???, ???, ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:63'>.../Chain.php<b>:</b>63</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>40</td><td bgcolor='#eeeeec' align='center'>3.4942</td><td bgcolor='#eeeeec' align='right'>26445016</td><td bgcolor='#eeeeec'>MW\Onestepcheckout\Controller\Index\Updateordermethod\Interceptor->___callParent( ???, ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Chain/Chain.php:70'>.../Chain.php<b>:</b>70</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>41</td><td bgcolor='#eeeeec' align='center'>3.4942</td><td bgcolor='#eeeeec' align='right'>26445016</td><td bgcolor='#eeeeec'>Magento\Checkout\Controller\Onepage->dispatch( ??? )</td><td title='/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/Interception/Interceptor.php:74'>.../Interceptor.php<b>:</b>74</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>42</td><td bgcolor='#eeeeec' align='center'>4.2884</td><td bgcolor='#eeeeec' align='right'>43951312</td><td bgcolor='#eeeeec'>Magento\Framework\App\Action\Action->dispatch( ??? )</td><td title='/new/mag/mag/vendor/magento/module-checkout/Controller/Onepage.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-checkout/Controller/Onepage.php:161'>.../Onepage.php<b>:</b>161</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>43</td><td bgcolor='#eeeeec' align='center'>4.3026</td><td bgcolor='#eeeeec' align='right'>44299408</td><td bgcolor='#eeeeec'>MW\Onestepcheckout\Controller\Index\Updateordermethod\Interceptor->execute(  )</td><td title='/new/mag/mag/vendor/magento/framework/App/Action/Action.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/framework/App/Action/Action.php:102'>.../Action.php<b>:</b>102</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>44</td><td bgcolor='#eeeeec' align='center'>4.3027</td><td bgcolor='#eeeeec' align='right'>44299408</td><td bgcolor='#eeeeec'>MW\Onestepcheckout\Controller\Index\Updateordermethod->execute(  )</td><td title='/new/mag/mag/var/generation/MW/Onestepcheckout/Controller/Index/Updateordermethod/Interceptor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/var/generation/MW/Onestepcheckout/Controller/Index/Updateordermethod/Interceptor.php:24'>.../Interceptor.php<b>:</b>24</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>45</td><td bgcolor='#eeeeec' align='center'>41.4631</td><td bgcolor='#eeeeec' align='right'>71636664</td><td bgcolor='#eeeeec'>Magento\Checkout\Model\Type\Onepage\Interceptor->saveOrder(  )</td><td title='/new/mag/mag/app/code/MW/Onestepcheckout/Controller/Index/Updateordermethod.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/app/code/MW/Onestepcheckout/Controller/Index/Updateordermethod.php:404'>.../Updateordermethod.php<b>:</b>404</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>46</td><td bgcolor='#eeeeec' align='center'>41.4631</td><td bgcolor='#eeeeec' align='right'>71636664</td><td bgcolor='#eeeeec'>Magento\Checkout\Model\Type\Onepage->saveOrder(  )</td><td title='/new/mag/mag/var/generation/Magento/Checkout/Model/Type/Onepage/Interceptor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/var/generation/Magento/Checkout/Model/Type/Onepage/Interceptor.php:154'>.../Interceptor.php<b>:</b>154</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>47</td><td bgcolor='#eeeeec' align='center'>47.3362</td><td bgcolor='#eeeeec' align='right'>71640256</td><td bgcolor='#eeeeec'>Magento\Checkout\Model\Type\Onepage->_prepareCustomerQuote(  )</td><td title='/new/mag/mag/vendor/magento/module-checkout/Model/Type/Onepage.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-checkout/Model/Type/Onepage.php:702'>.../Onepage.php<b>:</b>702</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>48</td><td bgcolor='#eeeeec' align='center'>47.3375</td><td bgcolor='#eeeeec' align='right'>71640256</td><td bgcolor='#eeeeec'>Magento\Customer\Model\ResourceModel\CustomerRepository\Interceptor->getById( ??? )</td><td title='/new/mag/mag/vendor/magento/module-checkout/Model/Type/Onepage.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-checkout/Model/Type/Onepage.php:627'>.../Onepage.php<b>:</b>627</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>49</td><td bgcolor='#eeeeec' align='center'>47.3376</td><td bgcolor='#eeeeec' align='right'>71640256</td><td bgcolor='#eeeeec'>Magento\Customer\Model\ResourceModel\CustomerRepository->getById( ??? )</td><td title='/new/mag/mag/var/generation/Magento/Customer/Model/ResourceModel/CustomerRepository/Interceptor.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/var/generation/Magento/Customer/Model/ResourceModel/CustomerRepository/Interceptor.php:50'>.../Interceptor.php<b>:</b>50</a></td></tr>
            <tr><td bgcolor='#eeeeec' align='center'>50</td><td bgcolor='#eeeeec' align='center'>47.3376</td><td bgcolor='#eeeeec' align='right'>71640256</td><td bgcolor='#eeeeec'>Magento\Customer\Model\CustomerRegistry->retrieve( ??? )</td><td title='/new/mag/mag/vendor/magento/module-customer/Model/ResourceModel/CustomerRepository.php' bgcolor='#eeeeec'><a style='color: black' href='phpstorm://open?/new/mag/mag/vendor/magento/module-customer/Model/ResourceModel/CustomerRepository.php:246'>.../CustomerRepository.php<b>:</b>246</a></td></tr>

             */
            if (isset($redirectUrl)) {
                $result->setData('redirect', $redirectUrl);
            }
            $this->_eventManager->dispatch(
                'checkout_controller_onepage_saveOrder',
                [
                    'result' => $result,
                    'action' => $this
                ]
            );

            if (isset($redirectUrl)) {
                echo json_encode(
                	[
                        'error'    => 0,
                        'msg'      => '',
                        'redirect' => $redirectUrl
                    ]
                );
                exit;
            }
            $this->getOnepage()->getQuote()->save();
            echo json_encode(
            	[
                    'error'    => 0,
                    'msg'      => '',
                    'redirect' => $this->_url->getUrl('checkout/onepage/success')
                ]
            );
            exit;

            return;
        }
	}

	/**
	 * Save billing and shipping address
	 *
	 * @param  string $type
	 * @param  array $data
	 */
	public function saveAddress($type, $data)
    {
        $addressId = $this->getRequest()->getPost($type . '_address_id');

        if (isset($data['save_in_address_book']) && $data['save_in_address_book'] == 1) {
            if ($addressId == "" && !$this->_dataHelper->getStoreConfig('onestepcheckout/addfield/addressbook')) {
                $addressId = $this->getDefaultAddress($type);
            }

            if ($addressId != "") {
                // Save data
                $customer = $this->_customerSession->getCustomer();
                $addressModel = $this->_objectManager->get('Magento\Customer\Model\Address');

                $existsAddress = $customer->getAddressById($addressId);
                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                    $addressModel->setId($existsAddress->getId());
                }

                $addressForm = $this->_objectManager->get('Magento\Customer\Model\Form');
                $addressData   = $this->getRequest()->getPost($type, []);

                try {
                	$addressForm->setFormCode('customer_address_edit')->setEntity($addressModel);
                	$addressForm->validateData($addressData);
                    $addressForm->compactData($addressData);
                    $addressModel->setCustomerId($customer->getId())
                        ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                        ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                    $addressModel->save();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                	$this->_objectManager->get('Magento\Framework\Logger\Monolog')->critical($e);
                }
            }
        }
    }

    /**
     * Retrive default billing or shipping address ID
     *
     * @param  string $type
     * @return int|string
     */
    public function getDefaultAddress($type)
    {
        $customer  = $this->_customerSession->getCustomer();
        if ($type == "billing") {
            $address   = $customer->getDefaultBillingAddress();
            $addressId = $address->getEntityId();
        } else {
            $address   = $customer->getDefaultShippingAddress();
            $addressId = $address->getEntityId();
        }

        return $addressId;
    }

    public function saveSubscriber($mail)
    {
        if ($mail) {
            $email = (string) $mail;

            try {
                if(!\Zend_Validate::is($email, 'EmailAddress')) {
                    throw new \Exception(__('Please enter a valid email address.'));
                }

                if($this->_dataHelper->getStoreConfig(Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1
                	&& !$this->_customerSession->isLoggedIn()
                ) {
                    throw new \Exception(__(
                        'Sorry, but the administrator denied subscription for guests. Please <a href="%1">register</a>.',
                        $this->_url->getUrl('customer/account/create/')
                    ));
                }

                $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
                $ownerId = $this->_objectManager->get('Magento\Customer\Model\CustomerFactory')->create()
                    ->setWebsiteId($storeManager->getWebsite()->getId())
                    ->loadByEmail($email)
                    ->getId();
                if ($ownerId !== null && $ownerId != $this->_customerSession->getId()) {
                    throw new \Exception(__('This email address is already assigned to another user.'));
                }

                $status = $this->_objectManager->get(
                    'Magento\Newsletter\Model\SubscriberFactory'
                )->create()->subscribe($email);
                if($status == Subscriber::STATUS_NOT_ACTIVE) {
                    $this->messageManager->addSuccess(__('The confirmation request has been sent.'));
                } else {
                    $this->messageManager->addSuccess(__('Thank you for your subscription.'));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError(
                    __('There was a problem with the subscription: %1', $e->getMessage())
                );
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong with the subscription.'));
            }
        }
    }

    /**
     * Set account information to session
     *
     * @param array $billingData
     */
    public function setAccountInfoSession($billingData)
    {
        if (!$this->getRequest()->getParam('dob')) {
            $dob = '';
        } else {
            $dob = $this->getRequest()->getParam('dob');
        }

        $gender = "";
        if (isset($billingData['gender'])) {
            $gender = $billingData['gender'];
        }

        $taxvat = "";
        if (isset($billingData['taxvat'])) {
            $taxvat = $billingData['taxvat'];
        }

        $suffix = "";
        if (isset($billingData['suffix'])) {
            $suffix = $billingData['suffix'];
        }

        $prefix = "";
        if (isset($billingData['prefix'])) {
            $prefix = $billingData['prefix'];
        }

        $middlename = "";
        if (isset($billingData['middlename'])) {
            $middlename = $billingData['middlename'];
        }

        $firstname = "";
        if (isset($billingData['firstname'])) {
            $firstname = $billingData['firstname'];
        }

        $lastname = "";
        if (isset($billingData['lastname'])) {
            $lastname = $billingData['lastname'];
        }

        $accountInfo = [$dob, $gender, $taxvat, $suffix, $prefix, $middlename, $firstname, $lastname];
        $this->_sessionManager->setAccountInfor($accountInfo);
    }

    private function loadLastRecordCreationTimestamp($remoteIp)
    {
        /** @var \Magedelight\Faqs\Model\ResourceModel\Faq\Collection $collection */
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter('remote_ip', $remoteIp)
            ->addAttributeToSort('created_at', 'DESC');
        $record = $collection->getFirstItem();
        return (int) strtotime($record->getCreatedAt());
    }
}
