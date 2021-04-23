<?php
namespace MageSuper\Casat\Model\Supplier;

class Service
{

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory
     */
    protected $_supplierProductCollectionFactory;
    protected $productCollectionFactory;

    function __construct(
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory $supplierProductCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    )
    {
        $this->_supplierProductCollectionFactory = $supplierProductCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    function updateCost()
    {
        $supplierProductCollection = $this->_supplierProductCollectionFactory->create();
        $supplierProductCollection->load();
        $productIds = [];
        /** @var \Magestore\SupplierSuccess\Model\Supplier\Product $supplierProduct */
        foreach ($supplierProductCollection as $supplierProduct) {
            $productIds[] = $supplierProduct->getProductId();
        }
        $productIds = array_unique($productIds);
        if (!$productIds) {
            return;
        }
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToFilter('entity_id', ['in' => $productIds])->addAttributeToSelect(['cost']);
        /** @var \Magestore\SupplierSuccess\Model\Supplier\Product $supplierProduct */
        foreach ($supplierProductCollection as $supplierProduct) {
            foreach ($productCollection as $product) {
                if ($product->getEntityId() !== $supplierProduct->getProductId()) {
                    continue;
                }
                $newCost = $product->getCost();
                if ($newCost && $supplierProduct->getCost() != $newCost) {
                    $supplierProduct->setCost($newCost);
                    $supplierProduct->save();
                }
                break;
            }
        }
    }
}