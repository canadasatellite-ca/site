<?php

namespace MageSuper\Casat\Controller\Adminhtml\Product;


use \Magento\Catalog\Controller\Adminhtml\Product\Save as originalClass;

/**
 * Class Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends originalClass
{

    protected function copyToStores($data, $productId)
    {
        if (!empty($data['product']['copy_to_stores'])) {
            foreach ($data['product']['copy_to_stores'] as $websiteId => $group) {
                if (isset($data['product']['website_ids'][$websiteId])
                    && (bool)$data['product']['website_ids'][$websiteId]) {
                    foreach ($group as $store) {
                        $copyFrom = (isset($store['copy_from'])) ? $store['copy_from'] : 0;
                        $copyTo = (isset($store['copy_to'])) ? $store['copy_to'] : 0;
                        if ($copyTo) {
                            continue;
                            $this->_objectManager->create('Magento\Catalog\Model\Product')
                                ->setStoreId($copyFrom)
                                ->load($productId)
                                ->setStoreId($copyTo)
                                ->setCopyFromView(true)
                                ->save();
                        }
                    }
                }
            }
        }
    }
}
