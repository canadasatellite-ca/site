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

namespace Magedelight\Subscribenow\Block\Adminhtml\ProductSubscribers\View\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magedelight\Subscribenow\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Serialize\Serializer\Json;

class ProfileInfo extends Template
{
    
    /**
     * @var Context
     */
    private $context;

    /**
     * @var Registry
     */
    private $coreRegistry;
    
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    
    /**
     * @var ProductRepositoryInterface
     */
    private $product;
    
    /**
     * @var Data
     */
    public $helper;
    
    /**
     * @var TimezoneInterface
     */
    public $timezone;
    
    /**
     * Constructor
     * @param Context $context
     * @param Registry $registry
     * @param ProductRepositoryInterface $productRepository
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        Data $helper,
        TimezoneInterface $timezone,
        DataObjectFactory $dataObject,
        Json $serialize,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->productRepository = $productRepository;
        $this->helper = $helper;
        $this->timezone = $timezone;
        $this->context = $context;
        $this->dataObjectFactory = $dataObject;
        $this->serialize = $serialize;
        parent::__construct($context, $data);
    }

    /**
     * Get Subscription Details
     * @return object
     */
    public function getSubscription()
    {
        return $this->coreRegistry->registry('md_subscribenow_product_subscriber');
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSubscriptionProduct()
    {
        if ($this->product == null) {
            $productId = $this->getSubscription()->getProductId();
            try{
                $this->product = $this->productRepository->getById($productId);
                $this->setBuyRequest();
            } catch (\Exception $ex) {
                $this->product = null;
            }
        }
        return $this->product;
    }
    
    private function setBuyRequest() {
        if(!$this->subscribeHelper->useDynamicPrice()) {
            return false;
        }
        
        $dataObject = $this->dataObjectFactory->create();
        $orderItemInfo = $this->getSubscription()->getOrderItemInfo();

        $options = ['info_buyRequest' => $dataObject->setData(['value' => $this->serialize->serialize($orderItemInfo)])];
        $superAttributes = !empty($orderItemInfo['super_attribute']) ? $orderItemInfo['super_attribute'] : null;
        
        if($superAttributes) {
            $options['attributes'] = $this->serialize->serialize($superAttributes);
        }
        
        if($this->product->getTypeId() == 'configurable' && $superAttributes) {
            $child = $this->product->getTypeInstance()->getProductByAttributes($superAttributes, $this->product);
            $options['simple_product'] = $dataObject->setData(['product' => $child]);
        }
        
        $this->product->setCustomOptions($options);
        
        $customOptions = $orderItemInfo['options'];
        if ($customOptions) {
            unset($customOptions['_1']);
            
            $optionIds = array_keys($customOptions);
            $this->product->addCustomOption('option_ids', implode(',', $optionIds));
            foreach ($customOptions as $optionId => $optionValue) {
                $this->product->addCustomOption('option_' . $optionId, $optionValue);
            }
        }
        
        return $options;
    }
    
    /**
     * @return array
     */
    public function getOrderItemInfo()
    {
        return $this->getSubscription()->getOrderItemInfo();
    }
    
    /**
     * @return bool
     */
    public function isEditMode()
    {
        $editParam = $this->context->getRequest()->getParam('edit');
        return (bool) ($editParam === 'editable');
    }
    
    /**
     * Form Submit URL
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('subscribenow/productsubscribers/save');
    }
}
