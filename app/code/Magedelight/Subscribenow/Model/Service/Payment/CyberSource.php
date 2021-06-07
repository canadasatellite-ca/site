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

class CyberSource
{
    /**
     * Change the constant values
     * as per payment method class
     */
    const SUBSCRIPTION_ID = 'magedelight_cybersource_subscription_id';
    const CARD_ID_COLUMN = 'subscription_id';
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
     * @var $cardCollectionFactory
     */
    private $cardCollectionFactory;

    /**
     * @var array
     */
    private $data;

    /**
     * CyberSource constructor
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

    public function getTitle()
    {
        return $this->getPaymentInformation()->getAdditionalInformation('method_title');
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

    /**
     * @return mixed
     */
    public function getMethodCode()
    {
        return $this->getPaymentInformation()->getMethod();
    }

    /**
     * @return mixed
     */
    private function getPaymentInformation()
    {
        return $this->order->getPayment();
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    private function getCollection($key, $value)
    {
        return $this->cardCollectionFactory->create()->addFieldToFilter($key, $value);
    }

    /**
     * @param $row
     * @return string
     */
    private function getCardNumber($row)
    {
        return 'XXXX-' . $row->getData(self::CARD_CC_NUMBER_COLUMN);
    }

    /**
     * @return null|string
     */
    public function getCardInfo()
    {
        $token = $this->encryptor->decrypt($this->data['token']);
        $collection = $this->getCollection(self::CARD_ID_COLUMN, $token);
        
        if ($collection->getSize()) {
            return $this->getCardNumber($collection->getFirstItem());
        }
        
        return null;
    }

    /**
     * @return array
     */
    public function getSavedCards()
    {
        $cards = [];
        $customerId = $this->data['customer_id'];
        $collection = $this->getCollection('customer_id', $customerId);
        
        if ($collection->getSize()) {
            foreach ($collection as $card) {
                $token = $this->encryptor->encrypt($card->getData(SELF::CARD_ID_COLUMN));
                $cardInfo = $this->getCardNumber($card);
        
                $label = $cardInfo . ', ' .  $card->getData('firstname') . ' ' . $card->getData('lastname');
                $isCurrentCard = ($this->encryptor->decrypt($this->data['token']) == $card->getData(SELF::CARD_ID_COLUMN)) ? 1 : 0;
                
                $cards[$token] = [
                    'is_current' => $isCurrentCard,
                    'label' => $label
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
            'method' => $subscription->getPaymentMethodCode(),
            'subscription_id' => $subscription->getPaymentToken(),
            'save_card' => 'true'
        ];
    }
}
