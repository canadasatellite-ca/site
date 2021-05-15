<?php

namespace Interactivated\Quotecheckout\Controller\Index;

class UpdateRefferal extends \Interactivated\Quotecheckout\Controller\Checkout\Onepage
{
	function execute()
	{
        /**
         * TODO: Check code
         */
		Mage::getSingleton('checkout/session')->unsReferralError();
        $referral_code = $this->getRequest()->getParam('referral_code');
        if($this->getRequest()->getParam('remove') == 1) {
            Mage::getSingleton('checkout/session')->unsetData('referral_code');
            $referral_code = '';
        }
        if($referral_code != '') {
            $check = Mage::helper('affiliate')->checkReferralCodeCart($referral_code);
            if($check == 0) {
                Mage::getSingleton('checkout/session')->setReferralError($this->__('The referral code is invalid.'));
                echo '{"r":"0","coupon":' . json_encode($this->renderReferral()) . ',"view":' . json_encode($this->renderReview()) . '}';
                die;

                return;
            }
        }
        try {
            if($referral_code != '') {
                Mage::getSingleton('checkout/session')->setReferralCode($referral_code); // set session
                Mage::getSingleton('checkout/session')->setReferralSuccess($this->__('The referral code was applied successfully.'));
            } else {
                Mage::getSingleton('checkout/session')->setReferralSuccess($this->__('The referral code has been cancelled successfully.'));
            }
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->setReferralError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->setReferralError($this->__('Can not apply the referral code.'));
        }
        $this->_initLayoutMessages('checkout/session');
        $success = 1;
        echo '{"r":"' . $success . '","coupon":' . json_encode($this->renderReferral()) . ',"view":' . json_encode($this->renderReview()) . '}';
        die;

        return;
	}
}
