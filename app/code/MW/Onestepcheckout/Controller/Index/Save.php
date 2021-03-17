<?php

namespace MW\Onestepcheckout\Controller\Index;

class Save extends \MW\Onestepcheckout\Controller\Checkout\Onepage
{
	protected $_notshiptype = 0;

    protected $_colspan = 5;

    protected $_htmlUpdatecart = "";

    /**
     * Save checkout information
     *
     * @return $this
     */
	public function execute()
	{
		$isAjax = (int) $this->getRequest()->getParam('isAjax');
		if (!$isAjax) {
			return $this->resultRedirectFactory->create()->setPath('noRoute');
		}

		$post = $this->getRequest()->getParams();
        if ($post) {
            try {
                $quote = $this->getOnepage()->getQuote();
                $html   = [];
                $layout = $this->layoutFactory->create();
                $update = $layout->getUpdate();
                $update->load('onestepcheckout_index_save');
                $layout->generateXml();
                $layout->generateElements();

                $updates = json_decode($post['updates']);
                $this->defineProperties();

                // Gift wrap
                if (isset($updates->updategiftwrap)) {
                    $iswrap = $updates->updategiftwrap;

                    if ($iswrap){
                        $this->_sessionManager->setIsWrap(true);
                    } else {
                        $this->_sessionManager->setIsWrap(false);
                    }
                    $quote->collectTotals()->save();
                }

                if (isset($updates->updaterewardpoints)) {
                    $rewardpointHelper = $this->_objectManager->get('MW\Rewardpoints\Helper\Data');

                    if(!$rewardpointHelper->moduleEnabled()) {
                        $html['rewardpoints'] = '';
                    } else {
                        $rewardpoints = $post['rewardpoints'];
                        if($rewardpoints < 0) {
                            $rewardpoints = -$rewardpoints;
                        }

                        $step = $rewardpointHelper->getPointStepConfig();
                        $rewardpoints = round(($rewardpoints/$step),0) * $step;

                        if($rewardpoints >= 0) {
                            $rewardpointHelper->setPointToCheckOut($rewardpoints);
                            $this->_checkoutSession->getQuote()->collectTotals()->save();
                        }
                    }
                }

                if (isset($updates->removeproduct)) {
                    $html['removed'] = $this->_removeproduct();
                    if ($html['removed']['error'] == 1 && $html['removed']['msg'] == __('EMPTY')) {
                        $html['empty_cart'] = 1;
                        echo json_encode($html);
                        exit;
                    }
                    /* $html['removed']['html_giftbox'], $html['removed']['html_link']  */
                }

                if (isset($updates->updatecart)) {
                    $html['cart'] = $this->_updateqty();
                    /** get message of cart */
                    /* $html['cart']['msg'], $html['cart']['error']  */
                }

                if (isset($updates->updatecouponcode)) {
                    $html['coupon'] = $this->_updatecoupon();
                    /** get html message of coupon */
                    /* $html['coupon']['html'] */
                }

                if (isset($updates->updatebillingaddress)) {
                    $html['billing'] = $this->_updatebillingform(isset($updates->changebycountry) ? false : true);
                    /** get html billing address */
                }

                if (isset($updates->updateshippingaddress)) {
                    $html['shipping'] = $this->_updateshippingform();
                    /** get html shipping address */
                }

                if (isset($updates->updateshippingtype)) {
                    $this->_updateshippingtype();
                    /** get html shipping medthod */
                    $shippingMethodBlock = $layout->createBlock(
                        'MW\Onestepcheckout\Block\Checkout\Onepage\Shipping\Method\Available'
                    )->setTemplate(
                        'MW_Onestepcheckout::dashboard/onepage/shipping_method/available.phtml'
                    );
                    $html['shipping_method'] = $shippingMethodBlock->toHtml();
                }

                if (isset($updates->updateshippingmethod)) {
                    if (isset($post['shipping_method']) && $post['shipping_method'] != '') {
                        $data   = $post['shipping_method'];
                        $result = $this->getOnepage()->saveShippingMethod($data);
                        if (!$result) {
                            $eventManager = $this->_objectManager->get('Magento\Framework\Event\ManagerInterface');
                            $eventManager->dispatch(
                                'checkout_controller_onepage_save_shipping_method',
                                [
                                    'request' => $this->getRequest(),
                                    'quote'   => $quote
                                ]
                            );
                            $quote->collectTotals();
                        }

                        $quote->collectTotals()->save();
                    }
                }

                // Save cart to calculate tax
                if (!$this->_customerSession->isLoggedIn()) {
                    $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
                    $cart->save();
                    $this->_checkoutSession->setCartWasUpdated(true);
                } else {
                    // If customer logged in, check using new address or not
                    if (isset($post['billing']) && isset($post['shipping'])) {
                        if (isset($post['ship_to_same_address']) && $post['ship_to_same_address'] == '1') {
                            $hasAddress = isset($post['billing_address_id']) ? $post['billing_address_id'] : '';
                        } else {
                            $hasAddress = isset($post['shipping_address_id']) ? $post['shipping_address_id'] : '';
                        }

                        if (!$hasAddress) {
                            $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
                            $cart->save();
                            $this->_checkoutSession->setCartWasUpdated(true);
                        }
                    }
                }

                if (isset($updates->updatepaymenttype)) {
                    /** get html all payment type */
                    $html['payment_method'] = $this->renderPaymentForm();
                }

                if (isset($updates->updatepaymentmethod)) {
                    if (isset($post['payment']['method']) && $post['payment']['method'] != '') {
                        $data = $post['payment'];
                        $this->getOnepage()->savePayment($data);
                    }
                }

                if (isset($updates->checkvat)) {
                    $html['VAT'] = $this->getVat();
                }

                $totalsBlock = $layout->createBlock('Magento\Checkout\Block\Cart\Totals');
                $totalsBlock->setTemplate('MW_Onestepcheckout::dashboard/onepage/review/totals.phtml');
                $this->getColspanTotal();

                $html = [
                    'vat'                 => (isset($html['VAT'])) ? $html['VAT'] : "",
                    'items'               => $this->_objectManager->get('Magento\Checkout\Model\Cart')->getItemsQty(),
                    'review_info'         => $this->renderReview(),
                    'totals'              => $this->_htmlUpdatecart . $totalsBlock->renderTotals(null, $this->_colspan),
                    'totals_footer'       => $totalsBlock->renderTotals('footer', $this->_colspan),
                    'earn_points'         => ($this->isRWPRunning()) ? $this->_objectManager->get(
                            'Magento\Config\Helper\Mwrewardpoints'
                        )->earnPointsOnepageReviewRewardPoints() : '',
                    'billing'             => (isset($html['billing'])) ? $html['billing'] : "",
                    'shipping'            => (isset($html['shipping'])) ? $html['shipping'] : "",
                    'coupon'              => (isset($html['coupon'])) ? $html['coupon'] : "",
                    'shipping_method'     => (isset($html['shipping_method'])) ? $html['shipping_method'] : "",
                    'payment_method'      => (isset($html['payment_method'])) ? $html['payment_method'] : "",
                    'item_payment_method' => (isset($html['item_payment_method'])) ? $html['item_payment_method'] : "",
                    'cart'                => (isset($html['cart'])) ? $html['cart'] : ""
                ];


            } catch (\Exception $e){
                $message = $e->getMessage();
                //mail('ddenisiy@gmail.com','Problem with saving quote',$message.$e->getTraceAsString().serialize($post));
                $html['cart']['error'] = 0;
                $html['cart']['msg'] = $message;
            }
            echo json_encode($html);
            exit;
        }
	}

	/**
	 * Remove product items
	 *
	 * @return array
	 */
	protected function _removeproduct()
    {
        $id         = (int) $this->getRequest()->getParam('id');
        $hasGiftBox = $this->getRequest()->getParam('hasgiftbox');
        if ($id) {
            try {
            	$checkoutCartModel = $this->_objectManager->get('Magento\Checkout\Model\Cart');
                $checkoutCartModel->removeItem($id)->save();

                $qty  = $checkoutCartModel->getItemsQty();
                $link = $this->getQtyAfterMyCart($qty);
            } catch (\Exception $e) {
                return [
                    'error' => 1,
                    'msg'   => __('Cannot remove the item.'),
                ];
            }
        } else {
            $link = '';
        }

        if (!$this->_getQuote()->getItemsCount()) {
            return [
                'error' => 1,
                'msg'   => __('EMPTY'),
            ];
        } else {
            if ($hasGiftBox) {
                return [
                    'error'        => 1,
                    'msg'          => '',
                    'html_giftbox' => json_encode($this->renderGiftbox()),
                    'html_link'    => $link,
                ];
            } else {
                return [
                    'error'        => 1,
                    'msg'          => '',
                    'html_giftbox' => '',
                    'html_link'    => $link,
                ];
            }
        }
    }

    protected function getQtyAfterMyCart($qty)
    {
        if ($qty == 0) {
            return __("My Cart");
        } else if ($qty == 1) {
            return __("My Cart (%1 item)", $qty);
        } else if ($qty > 1) {
            return __("My Cart (%1 items)", $qty);
        }

        return '';
    }

    /**
	 * Update quantity for product items
	 *
	 * @return array
	 */
    protected function _updateqty()
    {
        $qty = 0;
        $errorMessage = "";

        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                        $qty += $cartData[$index]['qty'];
                    }
                }

                $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
                if(!$cart->getCustomerSession()->getCustomer()->getId()
                	&& $cart->getQuote()->getCustomerId()
                ) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)->save();

                foreach ($this->getQuote()->getAllItems() as $item) {
                    if ($item->getMessage() && trim($item->getMessage())) {
                        $errorMessage = __('There are some errors when update quantity product.');
                        break;
                    }
                }
            }
            $this->_checkoutSession->setCartWasUpdated(true);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return [
                'error' => 0,
                'msg'   => $e->getMessage()
            ];
            exit;
        } catch (\Exception $e) {
            return [
                'error' => 0,
                'msg'   => __('Cannot update shopping cart.')
            ];
            exit;
        }

        return [
            'error' => 1,
            'msg'   => $errorMessage
        ];

        exit;
    }

    /**
     * Update coupon code
     *
     * @return array
     */
    protected function _updatecoupon()
    {
        $message = '';
        $cartQuote = $this->_getQuote();
        if (!$cartQuote->getItemsCount()) {
            return [
                'html' => $this->renderCoupon(),
                'msg' => $message
            ];
            exit;
        }

        $couponCode = (string) trim($this->getRequest()->getParam('coupon_code'));
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }

        $oldCouponCode = $cartQuote->getCouponCode();
        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            return [
                'html' => $this->renderCoupon(),
                'msg' => $message
            ];
            exit;
        }

        try {
            $cartQuote->getShippingAddress()->setCollectShippingRates(true);
            $cartQuote->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

            if ($couponCode != '') {
                $escaper = $this->_objectManager->get('Magento\Framework\Escaper');
                $coupon = $this->_objectManager->get('Magento\SalesRule\Model\CouponFactory')->create();
                $coupon->load($couponCode, 'code');
                if ($coupon->getId() && $couponCode == $cartQuote->getCouponCode()) {
                    $message = __('Coupon code "%1" was applied.', $escaper->escapeHtml($couponCode));
                } else {
                    $message = __('Coupon code "%1" is not valid.', $escaper->escapeHtml($couponCode));
                }
            } else {
                $message = __('Coupon code was canceled.');
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = __('Cannot apply the coupon code.');
        }

        return [
            'html' => $this->renderCoupon(),
            'msg' => $message
        ];
        exit;
    }

    /**
     * Retrive billing form
     *
     * @param  boolean $render
     * @return string
     */
    protected function _updatebillingform($render = true)
    {
        $this->clearBillingSession();

        $postData          = $this->getRequest()->getParams();
        $customerAddressId = isset($postData['billing_address_id']) ? $postData['billing_address_id'] : '';

        if (intval($customerAddressId) != 0) {
            $postData = $this->_dataHelper->filterdata($postData);
            if (isset($postData['email'])) {
                $postData['email'] = trim($postData['email']);
            }
            $address = $this->_objectManager->get('Magento\Quote\Model\Quote\Address');
            if ($customerAddressId) {
                $postData = $this->_objectManager->get('Magento\Customer\Api\AddressRepositoryInterface')
                    ->getById($customerAddressId)
                    ->__toArray();
            }

            $address->setData($postData);
            $this->getOnepage()->getQuote()->setBillingAddress($address);

            if ($render) {
            	return $this->renderBillingForm();
            }
        }

        return '';
    }

    /**
     * Retrive shipping form
     *
     * @return string
     */
    protected function _updateshippingform()
    {
        $this->clearBillingSession();

        $postData          = $this->getRequest()->getParams();
        $customerAddressId = isset($postData['shipping_address_id']) ? $postData['shipping_address_id'] : '';
        $postData = $this->_dataHelper->filterdata($postData);

        $this->getOnepage()->saveShipping($postData, $customerAddressId);
        if (intval($customerAddressId) != 0) {
            return $this->renderShippingForm();
        }

        return '';
    }

    /**
     * Update shipping type
     *
     * @return void
     */
    protected function _updateshippingtype()
    {
        $this->clearBillingSession();
        $this->_notshiptype = 1;

        if ((int) $this->getRequest()->getParam('ship_to_same_address') == 1) {
            $isbilling = 'billing';
        } else {
            $isbilling = 'shipping';
        }
        $postData          = $this->getRequest()->getPost($isbilling, []);
        $customerAddressId = $this->getRequest()->getPost($isbilling . '_address_id', false);

        if ($this->getRequest()->getPost('withoutaddressselect') == "true") {
            if ($this->getRequest()->getPost($isbilling . '_address_id') != "") {
                $customerAddressId = "";
            }
        }

        if ((isset($postData['country_id']) && $postData['country_id'] != '') || $customerAddressId) {
            $postData = $this->_dataHelper->filterdata($postData);
            $postData['use_for_shipping'] = '1';

            if (isset($postData['email'])) {
                $postData['email'] = trim($postData['email']);
            }
            if ($isbilling == 'billing') {
                $address = $this->_objectManager->get('Magento\Quote\Model\Quote\Address');
                if ($customerAddressId) {
                    $postData = $this->_objectManager->get('Magento\Customer\Api\AddressRepositoryInterface')
                        ->getById($customerAddressId)
                        ->__toArray();
                }

                $address->setData($postData);
                $this->getOnepage()->getQuote()->setBillingAddress($address);
            }

            $this->getOnepage()->saveShipping($postData, $customerAddressId);
            if(isset($address)){
                $email = $address->getEmail();
                if (function_exists('filter_var') && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return;
                }
            }

            if (!$customerAddressId) {
                $this->_objectManager->get('Magento\Checkout\Model\Cart')->save();
            }
        } else {
            $this->_getQuote()->getShippingAddress()
                ->setCountryId('')
                ->setPostcode('')
                ->setCollectShippingRates(true);
            $this->_getQuote()->save();
        }
    }

    /**
     * Validate Tax/VAT number
     *
     * @return string
     */
    public function getVat()
    {
        $vat = $this->getRequest()->getParam('vatnumber');
        if (empty($vat)) {
            return '';
        }

        $countrycode = $this->getRequest()->getParam('countrycode');
        $result = @file_get_contents('http://vatid.eu/check/' . $countrycode . '/' . $vat);
        if ($result) {
            return $result;
        } else {
            $urlcheckvat = 'http://isvat.appspot.com/' . $countrycode . '/' . $vat;
            $result      = @file_get_contents($urlcheckvat);
            if ((string)$result == "true" || (string)$result == "false") {
                return '<?xml version="1.0" encoding="UTF-8"?>
					<response>
					  <country-code>' . $countrycode . '</country-code>
					  <vat-number>' . $vat . '</vat-number>
					  <valid>' . $result . '</valid>
					  <name>--ds-</name>
					  <address>---</address>
					</response>';
            } else {
                return '<?xml version="1.0" encoding="UTF-8"?>
					<response>
					  <country-code>' . $countrycode . '</country-code>
					  <vat-number>' . $vat . '</vat-number>
					  <valid>false</valid>
					  <name>---</name>
					  <address>---</address>
					</response>';
            }
        }
    }

    public function getColspanTotal()
    {
        $numcol = 3;
        if ($this->_dataHelper->showImageProduct()) {
            $numcol = $numcol + 1;
        }

        if ($this->_dataHelper->getStoreConfig('onestepcheckout/general/allowremoveproduct')) {
            $numcol = $numcol + 1;
        }

        if ($this->_dataHelper->getStoreConfig('onestepcheckout/general/updateqtyproduct')) {
            $this->_htmlUpdatecart = "
                <tr class='first'>
                    <td class=\"a-right\" colspan=\"" . ($numcol + 1) . "\">
                    <button class=\"button btn-update-cart\" title=\"" . __('Update Shopping Cart') . "\" type=\"button\"><span><span>" . __('Update Cart') . "</span></span></button>
                    </td>
                </tr>";
        }

        if ($this->_objectManager->get('Magento\Tax\Helper\Data')->displayCartBothPrices()) {
        	$this->_colspan = 5;
        } else {
        	$this->_colspan = $numcol;
        }
    }

    /**
     * Check the reward point module is enabled
     *
     * @return boolean
     */
    protected function isRWPRunning()
    {
        if ($this->_objectManager->get('Magento\Catalog\Helper\Data')->isModuleOutputEnabled('MW_RewardPoints')) {
            if ($this->_dataHelper->getStoreConfig('rewardpoints/general/enabled')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrive coupon block
     *
     * @return string
     */
    public function renderCoupon()
    {
        $layout = $this->layoutFactory->create();
        $update = $layout->getUpdate();
        $update->load('onestepcheckout_index_save');
        $layout->generateXml();
        $layout->generateElements();
        $output = $layout->getBlock('checkout.onepage.coupon')->toHtml();

        return $output;
    }

    /**
     * Retrive billing form
     *
     * @return string
     */
    public function renderBillingForm()
    {
        $layout = $this->layoutFactory->create();
        $output = $layout->createBlock('MW\Onestepcheckout\Block\Checkout\Onepage\Billing\Sortbilling')
        	->setTemplate('MW_Onestepcheckout::dashboard/onepage/billing/sortbilling.phtml')
        	->toHtml();

        return $output;
    }

    /**
     * Retrive shipping form
     *
     * @return string
     */
    public function renderShippingForm()
    {
        $layout = $this->layoutFactory->create();
        $output = $layout->createBlock('MW\Onestepcheckout\Block\Checkout\Onepage\Shipping\Sortshipping')
        	->setTemplate('MW_Onestepcheckout::dashboard/onepage/shipping/sortshipping.phtml')
        	->toHtml();

        return $output;
    }

    /**
     * Retrive payment form
     *
     * @return string
     */
    public function renderPaymentForm()
    {
        $layout = $this->layoutFactory->create();
        $output = $layout->createBlock('MW\Onestepcheckout\Block\Checkout\Onepage\Payment\Methods')
                    ->setTemplate('MW_Onestepcheckout::dashboard/onepage/payment/methods.phtml')
                    ->toHtml();

        return $output;
    }

    /**
     * Retrive review info block
     *
     * @return string
     */
    public function renderReview()
    {
        $layout = $this->layoutFactory->create();
        $update = $layout->getUpdate();
        $update->load('onestepcheckout_index_save');
        $layout->generateXml();
        $layout->generateElements();
        $output = $layout->getBlock('info')->toHtml();

        return $output;
    }

    /**
     * Retrive gift message block
     *
     * @return string
     */
    public function renderGiftbox()
    {
        $layout = $this->layoutFactory->create();
        $update = $layout->getUpdate();
        $update->load('onestepcheckout_index_save');
        $layout->generateXml();
        $layout->generateElements();
        $output = $layout->getBlock('onestepcheckout.onepage.shipping_method.additional')->toHtml();

        return $output;
    }
}
