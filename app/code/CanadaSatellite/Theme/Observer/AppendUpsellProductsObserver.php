<?php

namespace CanadaSatellite\Theme\Observer;

class AppendUpsellProductsObserver extends \Magento\Bundle\Observer\AppendUpsellProductsObserver
{
    /**
     * Append bundles in upsell list for current product
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();

        /**
         * Check is current product type is allowed for bundle selection product type
         */
        if (!in_array($product->getTypeId(), $this->bundleData->getAllowedSelectionTypes())) {
            return $this;
        }

        /* @var $collection \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection */
        $collection = $observer->getEvent()->getCollection();
        $limit = $observer->getEvent()->getLimit();
        if (is_array($limit)) {
            if (isset($limit['upsell'])) {
                $limit = $limit['upsell'];
            } else {
                $limit = 0;
            }
        }

        /* @var $resource \Magento\Bundle\Model\ResourceModel\Selection */
        $resource = $this->bundleSelection;

        $productIds = array_keys($collection->getItems());
        if ($limit !== null && $limit <= count($productIds)) {
            return $this;
        }

        // retrieve bundle product ids
        $bundleIds = $resource->getParentIdsByChild($product->getId());
        // exclude up-sell product ids
        $bundleIds = array_diff($bundleIds, $productIds);

        if (!$bundleIds) {
            return $this;
        }

        /* @var $bundleCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $bundleCollection = $product->getCollection()->addAttributeToSelect(
            $this->config->getProductAttributes()
        )->addStoreFilter()->addMinimalPrice()->addFinalPrice()->addTaxPercents()->setVisibility(
            $this->productVisibility->getVisibleInCatalogIds()
        );

        if ($limit !== null) {
            $bundleCollection->setPageSize($limit);
        }
        $bundleCollection->addFieldToFilter(
            'entity_id',
            ['in' => $bundleIds]
        )->setFlag(
            'do_not_use_category_id',
            true
        );

        if ($collection instanceof \Magento\Framework\Data\Collection) {
            foreach ($bundleCollection as $item) {
                if (!$collection->getItemById($item->getId())) {
                    //not add alsobought products to upsell products
                    //$collection->addItem($item);
                }
            }
        } elseif ($collection instanceof \Magento\Framework\DataObject) {
            $items = $collection->getItems();
            foreach ($bundleCollection as $item) {
                $items[$item->getEntityId()] = $item;
            }
            $collection->setItems($items);
        }

        return $this;
    }
}
