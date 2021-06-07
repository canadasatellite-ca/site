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

namespace Magedelight\Subscribenow\Model\Service;

use Magento\Framework\ObjectManagerInterface;

class PaymentService
{
    const MODEL_PATH = "Magedelight\Subscribenow\Model\Service\Payment\\";

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public $classInfo = [
        'magedelight_cybersource' => 'CyberSource',
        'md_authorizecim' => 'Authorizecim',
        'cashondelivery' => 'COD',
        'md_stripe_cards' => 'Stripe',
        'payflowpro' => 'Payflowpro',
        'payflowpro_cc_vault' => 'Payflowpro',
        'md_firstdata' => 'Firstdata',
        'md_moneris' => 'Moneris',
        'md_monerisca' => 'Monerisca',
        'adyen_cc' => 'Adyen',
        'adyen_oneclick' => 'Adyen',
        'ops_cc' => 'Ingenico',
        'braintree' => 'Braintree',
        'braintree_cc_vault' => 'Braintree',
        'magedelight_ewallet' => 'Ewallet'
    ];

    public $cardCollectionClass = [
        'magedelight_cybersource' => \Magedelight\Cybersource\Model\ResourceModel\Cards\CollectionFactory::class,
        'md_firstdata' => \Magedelight\Firstdata\Model\ResourceModel\Cards\CollectionFactory::class,
        'md_moneris' => \Magedelight\Moneris\Model\ResourceModel\Cards\CollectionFactory::class,
        'md_monerisca' => \Magedelight\Monerisca\Model\ResourceModel\Cards\CollectionFactory::class,
        'md_authorizecim' => \Magedelight\Authorizecim\Model\ResourceModel\Cards\CollectionFactory::class,
        'md_stripe_cards' => \Magedelight\Stripe\Model\ResourceModel\Cards\CollectionFactory::class,
        'ops_cc' => \Netresearch\OPS\Model\ResourceModel\Alias\CollectionFactory::class
    ];
    
    /**
     * PaymentService constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create Class
     * @param $class
     * @param array $array
     * @return object
     */
    private function create($class, $array = [])
    {
        return $this->objectManager->create($class, $array);
    }

    /**
     * @param $order
     * @return mixed
     */
    public function get($order)
    {
        $paymentMethod = $order->getPayment()->getMethod();
        if ($order->hasBaseUsedCheckoutWalletAmout() && $order->getBaseUsedCheckoutWalletAmout() > 0) {
            $paymentMethod = 'magedelight_ewallet';
        }
        if (isset($this->classInfo[$paymentMethod])) {
            return $this->create(
                $this->getClassByCode($paymentMethod),
                ['order' => $order, 'cardCollectionFactory' => null, 'data' => []]
            );
        }
    }

    /**
     * Get Current Method Payment Service Object
     * @param string $code
     * @param string $token
     * @param int|null $customerId
     * @return object
     */
    public function getByPaymentCode($code, $token, $customerId = null)
    {
        if (isset($this->classInfo[$code])) {
            return $this->create(
                $this->getClassByCode($code),
                [
                    'cardCollectionFactory' => $this->getCardCollectionFactory($code),
                    'order' => null,
                    'data' => ['token' => $token, 'customer_id' => $customerId],
                ]
            );
        }
    }

    /**
     * @param \Magedelight\Subscribenow\Model\ProductSubscribers $subscription
     * @return object|null
     */
    public function getBySubscription($subscription)
    {
        $code = $subscription->getPaymentMethodCode();
        if (isset($this->classInfo[$code])) {
            return $this->create(
                $this->getClassByCode($code),
                [
                    'cardCollectionFactory' => $this->getCardCollectionFactory($code),
                    'order' => null,
                    'data' => ['subscription_instance' => $subscription],
                ]
            );
        }
    }

    /**
     * @param $paymentMethod
     * @return string
     */
    private function getClassByCode($paymentMethod)
    {
        return self::MODEL_PATH . $this->classInfo[$paymentMethod];
    }

    /**
     * Get Card Collection Factory Object
     * @param string $code
     * @return object|null
     */
    private function getCardCollectionFactory($code)
    {
        if (array_key_exists($code, $this->cardCollectionClass)) {
            return $this->create($this->cardCollectionClass[$code]);
        }
        return null;
    }
}
