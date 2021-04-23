<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageSuper\Casat\Plugin\SupplierSuccess\Service\Supplier;

use Magestore\SupplierSuccess\Service\AbstractService;

class ProductService extends AbstractService
{
    protected $supplierCollectionFactory;
    protected $productFactory;
    function __construct(
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->supplierCollectionFactory = $supplierCollectionFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * @param $data
     */
    function beforeAssignProductToSupplier(\Magestore\SupplierSuccess\Service\Supplier\ProductService $productService, $data)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create();
        foreach ($data as $key => $item) {
            $supplierId = $item['supplier_id'];
            if (isset($item['product_id'])){
                $product_id = $item['product_id'];
                $data[$key]['product_supplier_sku'] = $product->getResource()->getAttributeRawValue($product_id,'attr_vendor_part',0);
                $data[$key]['cost'] = $product->getResource()->getAttributeRawValue($product_id,'cost',0);
                $supplierCollection = $this->supplierCollectionFactory->create();
                $supplierCollection->addFieldToFilter('supplier_id', array('eq' => $supplierId))->addFieldToSelect('tax');
                $supplierCollection->load();
                if($supplierCollection->count()){
                    $suplier = $supplierCollection->getFirstItem();
                    if($suplier->getTax()){
                        $data[$key]['tax'] = $suplier->getTax();
                    }
                }
            }
        }
        return [$data];
    }
}