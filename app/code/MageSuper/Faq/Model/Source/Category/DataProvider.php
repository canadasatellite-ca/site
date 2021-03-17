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
namespace MageSuper\Faq\Model\Source\Category;

use Magedelight\Faqs\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 *
 * @package Magedelight\Giftcard\Model\Code
 */
class DataProvider extends \Magedelight\Faqs\Model\Source\Category\DataProvider
{
    protected $productCollectionFactory;
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $codeCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \MageSuper\Faq\Model\ResourceModel\Pcategory\CollectionFactory $productCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $codeCollectionFactory, $dataPersistor, $meta, $data);
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
            $categoryData = $page->getData();
            $categoryData['customer_group_ids'] = explode(',', $categoryData['customer_groups']);
            if (isset($categoryData['image'])) {
                unset($categoryData['image']);
                $categoryData['image'][0]['name'] = $page->getImage();
                $categoryData['image'][0]['url'] = $page->getImageUrl();
            }
            $categoryData['pcategory_ids'] = $this->getPcategories($page);
            $this->loadedData[$page->getId()] = $categoryData;
        }

        $data = $this->dataPersistor->get('md_faq_category');
        if (!empty($data)) {
            $page = $this->collection->getNewEmptyItem();
            $page->setData($data);
            $this->loadedData[$page->getId()] = $page->getData();
            $this->dataPersistor->clear('md_faq_category');
        }

        return $this->loadedData;
    }

    public function getPcategories($item)
    {
        $vProducts = $this->productCollectionFactory->create()
            ->addFieldToFilter('category_id', $item->getCategoryId())
            ->addFieldToSelect('pcategory_id');
        $products = array();
        foreach ($vProducts as $pdct) {
            $products[] = $pdct->getData('pcategory_id');
        }
        return $products;
    }
}
