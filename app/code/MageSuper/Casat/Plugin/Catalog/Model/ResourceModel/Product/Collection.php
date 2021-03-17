<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace MageSuper\Casat\Plugin\Catalog\Model\ResourceModel\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Adding product custom options to result collection
     *
     * @return $this
     */
    public function addOptionsToResult()
    {
        $productsByLinkId = [];

        foreach ($this as $product) {
            $productId = $product->getData(
                $product->getResource()->getLinkField()
            );

            $productsByLinkId[$productId] = $product;
        }

        if (!empty($productsByLinkId)) {
            $options = $this->_productOptionFactory->create()->getCollection()->addTitleToResult(
                $this->_storeManager->getStore()->getId()
            )->addDescriptionToResult(
                $this->_storeManager->getStore()->getId()
            )
                ->addPriceToResult(
                $this->_storeManager->getStore()->getId()
            )->addProductToFilter(
                array_keys($productsByLinkId)
            )->addValuesToResult();

            foreach ($options as $option) {
                if (isset($productsByLinkId[$option->getProductId()])) {
                    $productsByLinkId[$option->getProductId()]->addOption($option);
                }
            }
        }

        return $this;
    }
}
