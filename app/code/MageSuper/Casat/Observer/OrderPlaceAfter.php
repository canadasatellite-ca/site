<?php

/**
 * *
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *  
 */

namespace MageSuper\Casat\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class OrderPlaceAfter
 *
 * @category Magestore
 * @package  Magestore_OneStepCheckout
 * @module   OneStepCheckout
 * @author   Magestore Developer
 */
class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     * @var \Magestore\OneStepCheckout\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $_sender;
    protected $customerManagement;

    /**
     * OrderPlaceAfter constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $sender
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magestore\OneStepCheckout\Helper\Data $helper
     */
    function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \MageSuper\Casat\Model\Order\CustomerManagement $customerManagement
    )
    {
        $this->_customerSession = $customerSession;
        $this->customerManagement = $customerManagement;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getQuote();
        $connection = $quote->getResource()->getConnection();
        $customerId = $quote->getCustomerId();
        if($customerId){
            $hash = $connection->fetchOne("SELECT password_hash FROM customer_entity WHERE entity_id={$customerId}");
            if ($quote->getPasswordHash() && !$hash) {
                $connection = $quote->getResource()->getConnection();
                $connection->update($quote->getResource()->getTable('customer_entity'), array('password_hash' => $quote->getPasswordHash()), array('entity_id=?' => $order->getCustomerId()));
            }
        }
    }
}
