<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Rule;

use Magento\CatalogRule\Model\Rule as CatalogRule;

/**
 * Class RuleOverride
 */
class RuleOverride extends CatalogRule
{
    /**
     * Adds a $flag argument to the method, and if set to true,
     * it adds a new argument to the callback function called
     * "channel" that allows custom rule engine functionality
     *
     * @param bool $flag
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     * @throws \Zend_Db_Statement_Exception
     */
    public function getMatchingProductIds($flag = false)
    {
        if (!$flag) {
            return parent::getMatchingProductIds();
        }

        if ($this->_productIds === null) {
            $this->_productIds = [];
            $this->setData('collected_attributes', []);

            if ($this->getWebsiteIds()) {
                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
                $productCollection = $this->_productCollectionFactory->create();
                $productCollection->addWebsiteFilter($this->getWebsiteIds());
                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }

                foreach ($this->getConditions()->getConditions() as $condition) {
                    /** @var Product|Combine $condition */
                    $condition->collectValidatedAttributes($productCollection, true);
                }

                $this->_resourceIterator->walk(
                    $productCollection->getSelect(),
                    [[$this, 'callbackValidateProduct']],
                    [
                        'attributes' => $this->getData('collected_attributes'),
                        'product' => $this->_productFactory->create(),
                        'is_channel' => true
                    ]
                );
            }
        }

        return $this->_productIds;
    }

    /**
     * Extends core to check for the "is_channel" flag,
     * and if true, it adds this value to the product object
     * to allow for custom rule engine functionality
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ((isset($args['is_channel'])) ? $args['is_channel'] : false) {
            $product->setData('is_channel', true);
        }

        $websites = $this->_getWebsitesMap();
        $results = [];

        foreach ($websites as $websiteId => $defaultStoreId) {
            $product->setStoreId($defaultStoreId);
            $results[$websiteId] = $this->getConditions()->validate($product);
        }
        $this->_productIds[$product->getId()] = $results;
    }
}
