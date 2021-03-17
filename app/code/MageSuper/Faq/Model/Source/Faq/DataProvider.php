<?php
/* Magedelight
 * Copyright (C) 2016 Magedelight <info@magedelight.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category Magedelight
 * @package Magedelight_Giftcard
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
namespace MageSuper\Faq\Model\Source\Faq;

use Magedelight\Faqs\Model\ResourceModel\Faq\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 *
 * @package Magedelight\Giftcard\Model\Code
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magedelight\Giftcard\Model\ResourceModel\Code\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;
    
    public $faqResource;

    protected $productCollectionFactory;
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $pageCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $codeCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Magedelight\Faqs\Model\ResourceModel\Faq $faqResource,
        \MageSuper\Faq\Model\ResourceModel\PcategoryQuestion\CollectionFactory $productCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $codeCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->faqResource = $faqResource;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        
        foreach ($items as $page) {
            $page->setStores(explode(',', $page->getStoreId()));
            $categoriesIds = $this->faqResource->getCategoriesIds($page->getId());
            $page->setCategoryId($categoriesIds);
            $page->setData('pcategory_ids',$this->getPcategories($page));
            $this->loadedData[$page->getId()] = $page->getData();
        }

        $data = $this->dataPersistor->get('md_faqs_question');
        if (!empty($data)) {
            $page = $this->collection->getNewEmptyItem();
            $page->setData($data);
            $this->loadedData[$page->getId()] = $page->getData();
            $this->dataPersistor->clear('md_faqs_question');
        }

        return $this->loadedData;
    }
    public function getPcategories($item)
    {
        $vProducts = $this->productCollectionFactory->create()
            ->addFieldToFilter('question_id', $item->getId())
            ->addFieldToSelect('pcategory_id');
        $products = array();
        foreach ($vProducts as $pdct) {
            $products[] = $pdct->getData('pcategory_id');
        }
        return $products;
    }
}
