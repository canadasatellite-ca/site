<?php

namespace CanadaSatellite\Theme\Controller\Quote;

use MW\Onestepcheckout\Controller\Index\Save as ParentSave;

class Save extends ParentSave
{
    /**
     * Save checkout information
     *
     * @return $this
     */
    function execute()
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


                        // to avoid not-freeshippingcustom with amount=0
                        if ($quote->getShippingAddress()->getShippingAmount() == 0 && $quote->getShippingAddress()->getShippingMethod() !== "freeshippingcustom_freeshippingcustom") {
                            throw new \Exception ("This shipping method is unavailable, please select another!");
                        }

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

}
