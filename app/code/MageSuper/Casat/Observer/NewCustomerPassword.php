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
 * Class OrderSaveAfter
 * @package Magestore\OneStepCheckout\Observer
 */
class NewCustomerPassword implements ObserverInterface
{
    protected $_jsonHelper;

    protected $_dataObjectFactory;
    protected $_objectManager;
    protected $session;
    /**
     * OrderSaveAfter constructor.
     * @param \Magestore\OneStepCheckout\Model\DeliveryFactory $deliveryFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Customer\Model\Session $session,
        array $data = []
    )
    {
        $this->session = $session;
        $this->_jsonHelper = $jsonHelper;
        $this->_dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getData('request');
        $additionalData = $this->_dataObjectFactory->create([
            'data' => $this->_jsonHelper->jsonDecode($request->getContent()),
        ]);
        $this->session->setData('magesuper_registerPassword', $additionalData->getData('registerPassword'));
    }
}
