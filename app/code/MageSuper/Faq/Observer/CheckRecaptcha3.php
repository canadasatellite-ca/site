<?php

namespace MageSuper\Faq\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;

class CheckRecaptcha3 implements ObserverInterface
{

    private $_scopeConfig;

    private $_request;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_request = $request;
    }

    public function execute(Observer $observer)
    {
        $result = $this->_checkRecaptchaCurl();
        if ($result && $result->score && $result->score > 0.49){
            $this->_request->setParams(['success_v3'=>true, 'score_v3'=>$result->score]);
        } else {
            $this->_request->setParams(['success_v3'=>false]);
        }
        return $this;
    }

    private function _checkRecaptchaCurl() {
        $Url = "https://www.google.com/recaptcha/api/siteverify";

        switch ($this->_request->getModuleName()) {
            case 'faqs':
                $recaptchaResponse = $this->_request->getParam('recaptcha_response_v3');
                break;
            case 'aw_advanced_reviews':
                if ($this->_request->getActionName() == "submit"){
                    $recaptchaResponse = $this->_request->getParam('recaptcha_response_v3_product_review');
                } elseif ($this->_request->getActionName() == "comment") {
                    $recaptchaResponse = $this->_request->getParam('comment_recap');
                }
                break;
            case 'newsletter':
                $recaptchaResponse = $this->_request->getParam('recaptcha_response_v3_news');
                break;
            case 'customer':
                $recaptchaResponse = $this->_request->getParam('recaptcha_response_v3_reg');
                break;
            case 'onestepcheckout':
                $recaptchaResponse = $this->_request->getParam('recaptcha_response_v3_onestepcheckout');
                break;
            default:
                $recaptchaResponse = $this->_request->getParam('recaptcha_response_v3');
        }

        $secretKey = $this->_scopeConfig->getValue('msp_securitysuite_recaptcha/general_V3/private_key_v3');
        if(isset($recaptchaResponse) && !empty($recaptchaResponse)) {
            //get verified response data
            $data = array('secret' => $secretKey, 'response' => $recaptchaResponse);

            $ch = curl_init($Url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $verifyResponse = curl_exec($ch);
            curl_close($ch);

            $responseData = json_decode($verifyResponse);

            return $responseData;
        } else {
            return (bool) false;
        }

    }
}