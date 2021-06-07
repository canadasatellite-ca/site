<?php

/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Model\Service\Payment;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Vault\Api\PaymentTokenManagementInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;

class Adyen
{
    /**
     * @var Magento\Sales\Model\Order
     */
    private $order;
    
    /**
     * @var EncryptorInterface
     */
    private $encryptor;
    
    /**
     * @var array
     */
    private $data;
    
    /**
     * PaymentTokenManagementInterface
     */
    private $tokenManagement;
    
    /**
     *
     * @param EncryptorInterface $encryptor
     * @param PaymentTokenManagementInterface $paymentToken
     * @param type $order
     * @param array $data
     */
    public function __construct(
        EncryptorInterface $encryptor,
        PaymentTokenManagementInterface $paymentToken,
        $order,
        array $data = []
    ) {
    
        $this->encryptor = $encryptor;
        $this->tokenManagement = $paymentToken;
        $this->order = $order;
        $this->data = $data;
    }
    
    /**
     * Return Payment token
     *
     * @return string
     */
    public function getPaymentToken()
    {
        if ($this->getMethodCode() == 'adyen_cc') {
            $payment = $this->order->getPayment();
            $extensionAttributes = $payment->getExtensionAttributes();
            $paymentToken = $extensionAttributes->getVaultPaymentToken();
            $publichash = "";
            if ($paymentToken !== null) {
                $paymentToken->setCustomerId($this->order->getCustomerId());
                $paymentToken->setIsActive(true);
                $paymentToken->setPaymentMethodCode($payment->getMethod());
                $publichash = $this->generatePublicHash($paymentToken);
            }
            return $publichash;
        } elseif ($this->getMethodCode() == 'adyen_cc_vault') {
            $additionalInfo = $this->getPaymentInformation()->getAdditionalInformation();
            return $this->encryptor->encrypt($additionalInfo['token_metadata']['public_hash']);
        }
        return;
    }
    
    public function getTitle()
    {
        return $this->getPaymentInformation()->getAdditionalInformation('method_title');
    }
    
    /**
     * Return Payment Method code
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->getPaymentInformation()->getMethod();
    }
    
    /**
     * Return payment Details
     *
     * @return object
     */
    private function getPaymentInformation()
    {
        return $this->order->getPayment();
    }
    
    /**
     * Generate vault payment public hash
     *
     * @param PaymentTokenInterface $paymentToken
     * @return string
     */
    protected function generatePublicHash(PaymentTokenInterface $paymentToken)
    {
        $hashKey = $paymentToken->getGatewayToken();
        if ($paymentToken->getCustomerId()) {
            $hashKey = $paymentToken->getCustomerId();
        }

        $hashKey .= $paymentToken->getPaymentMethodCode()
                . $paymentToken->getType()
                . $paymentToken->getTokenDetails();

        return $this->encryptor->getHash($hashKey);
    }

    /**
     * Get Active Card Number
     * @return string
     */
    public function getCardInfo()
    {
        $publicHash = $this->data['token'];
        $customerId = $this->data['customer_id'];
        
        $paymentToken = $this->tokenManagement->getByPublicHash($publicHash, $customerId);
       
        if ($paymentToken) {
            $activeflag = $paymentToken->getIsActive();
            if ($activeflag) {
                 
                $details = json_decode($paymentToken->getDetails(), true);
                return 'XXXX-' . $details['maskedCC'];
            }
        }
        
        return null;
    }
    
    /**
     * Get Saved Cards
     * @return array
     */
    public function getSavedCards()
    {
        $cards = [];
        $customerId = $this->data['customer_id'];
        
        if ($customerId) {
            $customers = $this->tokenManagement->getListByCustomerId($customerId);
            foreach ($customers as $card) {
                if ($card->getPaymentMethodCode() == 'adyen_cc' && $card->getIsActive()) {
                    $detail = json_decode($card->getDetails(), true);
                    $token = $card->getPublicHash();
                    $isCurrentCard = ($this->data['token'] == $token) ? 1 : 0;
                    $cardInfo = 'XXXX-' . $detail['maskedCC'];
                    $cards[$token] = [
                        'is_current' => $isCurrentCard,
                        'label' => $cardInfo
                    ];
                }
            }
        }
        return $cards;
    }
    
    public function getImportData()
    {
        /** @var \Magedelight\Subscribenow\Model\ProductSubscribers $subscription */
        $subscription = $this->data['subscription_instance'];
        
        return [
            'method' => 'adyen_cc_vault',
            'is_active_payment_token_enabler' => 'true',
            'public_hash' => $subscription->getPaymentToken()
        ];
    }
}