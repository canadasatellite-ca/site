<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Ui\DataProvider\Supplier\Form\Modifier;

use Magento\Ui\Component\Form;
use Magento\Ui\Component\Form\Field;
use Magestore\SupplierSuccess\Model\Locator\LocatorInterface;
use Magestore\SupplierSuccess\Service\SupplierService;

/**
 * Data provider for Configurable panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Information extends \Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier\AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var SupplierService
     */
    protected $supplierService;

    /**
     * @return boolean
     */
    protected $opened = true;
    protected $listsInterface;

    /**
     * @var
     */
    protected $loadedData;

    public function __construct(
        LocatorInterface $locator,
        SupplierService $supplierService,
        \Magento\Framework\Locale\ListsInterface $listsInterface
    ) {
        $this->listsInterface = $listsInterface;
        $this->locator = $locator;
        $this->supplierService = $supplierService;
        $supplier = $this->locator->getSupplier();
        if ($supplier && $supplier->getId()) {
            $this->opened = false;
        }
    }

    /**
     * modify data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            $this->getSupplierInformation($meta)
        );
        return $meta;
    }

    /**
     * @param $meta
     * @return mixed
     */
    public function getSupplierInformation($meta)
    {
        $meta['information']['children'] = $this->getSupplierInformationChildren();
        return $meta;
    }

    /**
     * @return array
     */
    public function getSupplierInformationChildren()
    {
        $children = [
            'supplier_currrency' => $this->getField(__('Supplier Currency'), Field::NAME, true, 'text', 'select', [],null,$this->getCurrencyOptions()),
            'tax' => $this->getField(__('Tax (%)'), Field::NAME, true, 'text', 'input', [])
        ];
        return $children;
    }

    /**
     * get status options
     */
    public function getCurrencyOptions()
    {
        return $this->listsInterface->getOptionCurrencies();
    }

}
