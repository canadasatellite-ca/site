<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Payment;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\Logger;

/**
 * Class Method (3rd party channel payment)
 */
class Method extends AbstractMethod
{
    /** @var string $_code */
    protected $_code = 'amazonpayment';
    /** @var bool $_canUseCheckout */
    protected $_canUseCheckout = false;
    /** @var bool $_canUseInternal */
    protected $_canUseInternal = false;
    /** @var string $_formBlockType */
    protected $_formBlockType = \Magento\Amazon\Block\Adminhtml\Payment\Form\Marketplaces::class;
    /** @var string $_infoBlockType */
    protected $_infoBlockType = \Magento\Amazon\Block\Adminhtml\Payment\Info\Marketplaces::class;
    /** @var DataPersistorInterface $dataPersistor */
    private $dataPersistor;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param DataPersistorInterface $dataPersistor
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Data $paymentData
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger|Logger $logger
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataPersistorInterface $dataPersistor,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Assign data to info model instance
     *
     * @param DataObject|mixed $data
     * @return $this
     * @throws LocalizedException
     */
    public function assignData(DataObject $data)
    {
        if ($marketplaceOrderId = $this->dataPersistor->get('marketplace_order_id')) {
            $this->getInfoInstance()->setPoNumber($marketplaceOrderId);
        }

        return $this;
    }
}
