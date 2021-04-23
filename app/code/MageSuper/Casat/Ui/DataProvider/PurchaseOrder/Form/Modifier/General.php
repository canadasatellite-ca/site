<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageSuper\Casat\Ui\DataProvider\PurchaseOrder\Form\Modifier;

/**
 * Class General
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier
 */
class General extends \Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier\AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'general_information';
    protected $supplierCollectionFactory;
    protected $directory;

    function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory,
        \Magento\Directory\Helper\Data $directory
    )
    {
        $this->directory = $directory;
        $this->supplierCollectionFactory = $supplierCollectionFactory;
        parent::__construct($objectManager, $registry, $request, $urlBuilder);
    }

    /**
     * @var string
     */
    protected $groupLabel = 'General Information';

    /**
     * @var int
     */
    protected $sortOrder = 80;

    /**
     * modify data
     *
     * @return array
     */
    function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify purchase order form meta
     *
     * @param array $meta
     * @return array
     */
    function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'children' => $this->getGeneralChildren(),
                ],
            ]
        );
        return $meta;
    }

    /**
     * Add general form fields
     *
     * @return array
     */
    function getGeneralChildren()
    {
        $children['purchased_at']['arguments']['data']['config']['default'] = date('Y-m-d');
        $children['supplier_id']['arguments']['data']['config']['component'] = 'MageSuper_Casat/js/form/element/suplier';
        $supliers_currency_data = $this->getSuppliersCurrencyDataJson();
        $children['supplier_id']['arguments']['data']['config']['currency_data'] = $supliers_currency_data;
        return $children;
    }

    function getOpened()
    {
        if (!$this->request->getParam('id')) {
            return true;
        }
        return $this->opened;
    }


    function getSuppliersCurrencyDataJson()
    {
        $collection = $this->supplierCollectionFactory->create()
            ->addFieldToSelect('supplier_currrency')
            ->addFieldToSelect('supplier_id');
        foreach ($collection->getItems() as $supplier) {

            $rate = 1;
            if ($supplier->getData('supplier_currrency') && $supplier->getData('supplier_currrency') !== 'CAD') {
                $rate = $this->directory->currencyConvert(1, $supplier->getData('supplier_currrency'), 'CAD');
            }
            $options[$supplier->getId()] = [
                'currency' => $supplier->getData('supplier_currrency'),
                'rate' => $rate
            ];
        }
        return json_encode($options);
    }
}