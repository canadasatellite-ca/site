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
use Magento\Framework\DataObjectFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Customer\Model\Customer;

class Authorizecim
{
    /**
     * Change the constant values
     * as per payment method class
     */
    const SUBSCRIPTION_ID = 'md_payment_profile_id';
    const CARD_ID_COLUMN = 'payment_profile_id';
    const CARD_CC_NUMBER_COLUMN = 'cc_last_4';
    const SUCCESS_RESPONSE_CODE = 'I00001';
    const SUCCESS_RESULT_STRING = 'OK';

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
     * @var DataObjectFactory
     */
    private $dataObject;
    
    /**
     * @var Customer
     */
    private $customer;
    
    /**
     * @var $cardCollectionFactory
     */
    private $cardCollectionFactory;

    /**
     * @var array
     */
    private $data;

    /**
     * Authorizecim constructor
     * @param EncryptorInterface $encryptor
     * @param DataObjectFactory $dataObject
     * @param ObjectManagerInterface $objectManager
     * @param Customer $customer
     * @param object $cardCollectionFactory
     * @param object $order
     * @param array $data
     */
    public function __construct(
        EncryptorInterface $encryptor,
        DataObjectFactory $dataObject,
        ObjectManagerInterface $objectManager,
        Customer $customer,
        $cardCollectionFactory,
        $order,
        array $data = []
    ) {
    
        $this->encryptor = $encryptor;
        $this->dataObject = $dataObject;
        $this->objectManager = $objectManager;
        $this->customer = $customer;
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
     * Get Class Object
     * @return object
     */
    private function create($class, $array = [])
    {
        return $this->objectManager->create($class, $array);
    }

    /**
     * Get Card Information
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
     * Get Saved Cards
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
            'method' => 'md_authorizecim',
            'save_card' => 'true',
            'md_payment_profile_id' => $subscription->getPaymentToken(),
            'payment_profile_id' => $subscription->getPaymentToken()
        ];
    }
}
