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


namespace Magedelight\Subscribenow\Setup;

class Installer {
    
    private $shippingMethodArray = [];
    
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magedelight\Subscribenow\Model\ProductSubscribersFactory $productSubscriberFactory,
        \Magedelight\Subscribenow\Helper\Data $helper,
        \Magento\Shipping\Model\Config\Source\Allmethods $shipping,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Serialize\Serializer\Json $json
    ) {
        $this->appState = $appState;
        $this->productFactory = $product;
        $this->productSubscriberFactory = $productSubscriberFactory;
        $this->helper = $helper;
        $this->shippingMethods = $shipping;
        $this->logger = $logger;
        $this->serializer = $json;
    }
    
    public function upgradeData($setup) {
        $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        
        $collections = $this->productSubscriberFactory->create()->getCollection();
        if(!$collections->getSize()) {
            return null;
        }
        
        foreach ($collections as $collection) {
            try {
                $this->processCollection($collection);
            } catch (\Exception $ex) {
                $this->logger->info($ex->getMessage());
            }
        }
        
        return true;
    }
    
    
    private function getInfo($string = null) {
        if ($string && $string != 'null') {
            return $this->serializer->unserialize($string);
        }
        return [];
    }
    
    private function processCollection($collection) {
        $productData = $this->getProductData($collection->getProductId());
        $paymentTitle = $this->helper->getPaymentTitle($collection->getPaymentMethodCode());
        $shippingTitle = $this->getShippingTitle($collection->getShippingMethodCode());
        $additionalInfo = $this->getInfo($collection->getAdditionalInfo());
        
        $dataChanged = false;
        $subscription = $this->productSubscriberFactory->create()->load($collection->getId());
        
        if(!empty($productData)) {
            $dataChanged = true;
            $subscription->setProductName($productData[1]);
            $additionalInfo['product_sku'] = trim($productData[0]);
        }

        if($paymentTitle) {
            $dataChanged = true;
            $subscription->setPaymentTitle($paymentTitle);
        }

        if($shippingTitle) {
            $dataChanged = true;
            $additionalInfo['shipping_title'] = trim($shippingTitle);
        }
        
        if($dataChanged) {
            $subscription->setAdditionalInfo($additionalInfo)->save();
        }
    }
    
    public function getShippingTitle($code) {
        if(!$this->shippingMethodArray) {
            $shippingMethods = $this->shippingMethods->toOptionArray();
            $methods = [];
            foreach ($shippingMethods as $method) {
                if (!empty($method['value']) && is_array($method['value'])) {
                    foreach ($method['value'] as $key => $value) {
                        $subtitle = $this->deleteAllBetweenStr('[',']',$value['label']);
                        $title = $subtitle ? $method['label'] . " - " . $subtitle : $method['label'];
                        $methods[$value['value']] = $title;
                    }
                }
            }
            
            $this->shippingMethodArray = $methods;
        }
        
        return isset($this->shippingMethodArray[$code]) ? $this->shippingMethodArray[$code] : null;
    }
    
    private function getProductData($productId = 0) {
        if($productId) {
            $product = $this->productFactory->create()->load($productId);
            if($product->getId()) {
                return [$product->getSku(),$product->getName()];
            }
        }
        return null;
    }
    
    private function deleteAllBetweenStr($beginning, $end, $string) {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
          return $string;
        }

        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

        // recursion to ensure all occurrences are replaced
        return trim(
            $this->deleteAllBetweenStr(
                $beginning, 
                $end, 
                str_replace($textToDelete, '', $string)
            )
        );
    }
}
