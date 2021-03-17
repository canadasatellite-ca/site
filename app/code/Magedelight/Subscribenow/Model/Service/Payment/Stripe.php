<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Model\Service\Payment;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\ObjectManagerInterface;

class Stripe
{
    /**
     * Change the constant values
     * as per payment method class
     */
    const SUBSCRIPTION_ID = 'md_stripe_card_id';
    const CARD_ID_COLUMN = 'stripe_customer_id';
    const CARD_CC_NUMBER_COLUMN = 'cc_last_4';

    /**
     * @var Magento\Sales\Model\Order
     */
    private $order;
    
    /**
     * @var EncryptorInterface
     */
    private $encryptor;
    
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    
    /**
     * @var array
     */
    private $data;

    /**
     * Stripe constructor
     * @param EncryptorInterface $encryptor
     * @param object $cardCollectionFactory
     * @param object $order
     * @param array $data
     */
    public function __construct(
        EncryptorInterface $encryptor,
        $cardCollectionFactory,
        $order,
        array $data = []
    ) {
    
        $this->encryptor = $encryptor;
        $this->cardCollectionFactory = $cardCollectionFactory;
        $this->order = $order;
        $this->data = $data;
    }

    /**
     * Get Payment Token
     * @return string
     */
    public function getPaymentToken()
    {
        $additionalInfo = $this->getPaymentInformation()->getAdditionalInformation();
        return $this->encryptor->encrypt($additionalInfo[self::SUBSCRIPTION_ID]);
    }
    
    public function getTitle()
    {
        return $this->getPaymentInformation()->getAdditionalInformation('method_title');
    }

    public function getMethodCode()
    {
        return $this->getPaymentInformation()->getMethod();
    }

    private function getPaymentInformation()
    {
        return $this->order->getPayment();
    }
    
    private function getCollection($key, $value)
    {
        return $this->cardCollectionFactory->create()->addFieldToFilter($key, $value);
    }
    
    private function getCardNumber($row)
    {
        return 'XXXX-' . $row->getData(self::CARD_CC_NUMBER_COLUMN);
    }
    
    public function getCardInfo()
    {
        $cardDigit = null;
        $token = $this->encryptor->decrypt($this->data['token']);
        $collection = $this->getCollection(SELF::CARD_ID_COLUMN, $token);
        
        if ($collection->getSize()) {
            /** Due to MEQP2 getFirstItem warning we use foreach */
            foreach ($collection as $row) {
                $cardDigit = $this->getCardNumber($row);
                break;
            }
        }
        
        return $cardDigit;
    }

    public function getSavedCards()
    {
        $cards = [];
        $customerId = $this->data['customer_id'];
        $collection = $this->getCollection('customer_id', $customerId);
        $decryptedToken = $this->encryptor->decrypt($this->data['token']);
        
        if ($collection->getSize()) {
            foreach ($collection as $card) {
                $token = $card->getData(SELF::CARD_ID_COLUMN);
                $cardInfo = $this->getCardNumber($card);
                
                $isCurrentCard = ($decryptedToken == $token) ? true : false;
                
                $cards[$this->encryptor->encrypt($token)] = [
                    'is_current' => $isCurrentCard,
                    'label' => $cardInfo
                ];
            }
        }
        
        return $cards;
    }

    public function getImportData()
    {
        /** @var \Magedelight\Subscribenow\Model\ProductSubscribers $subscription */
        $subscription = $this->data['subscription_instance'];
        
        return [
            'method' => 'md_stripe_cards',
            'md_stripe_card_id' => $this->encryptor->decrypt($subscription->getPaymentToken()),
            'save_card' => 'true'
        ];
    }
}
