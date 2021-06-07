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

namespace Magedelight\Subscribenow\Block\Customer\Account;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magedelight\Subscribenow\Helper\Data as subscribeHelper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Serialize\Serializer\Json;

class View extends AbstractSubscription
{

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\Data\ProductInterface
     */
    protected $product;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serialize;
    
    /**
     * Constructor
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        subscribeHelper $subscribeHelper,
        TimezoneInterface $timezone,
        ProductRepositoryInterface $productRepository,
        DataObjectFactory $dataObject,
        Json $serialize,
        array $data = []
    ) {
    
        parent::__construct($context, $registry, $subscribeHelper, $timezone, $data);
        $this->productRepository = $productRepository;
        $this->dataObjectFactory = $dataObject;
        $this->serialize = $serialize;
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Subscription Profile # %1', $this->getSubscription()->getProfileId()));
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSubscriptionProduct()
    {
        if ($this->product == null) {
            $productId = $this->getSubscription()->getProductId();
            try {
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
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/profile/');
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl('*/*/edit/', ['_current' => true]);
    }

    /**
     * @return bool
     */
    public function isEditMode()
    {
        return (bool) $this->getRequest()->getParam('edit', false);
    }
}
