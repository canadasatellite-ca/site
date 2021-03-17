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
 * @package Magedelight_Faqs
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 * 
 */
namespace MageSuper\Faq\Block;

class Category extends \Magedelight\Faqs\Block\Category
{
    protected $faqCategoryCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magedelight\Faqs\Model\Category $categoryModel,
        \Magedelight\Faqs\Model\ResourceModel\Faq $faqResource,
        \Magedelight\Faqs\Helper\Data $faqsHelper,
        \Magedelight\Faqs\Model\ResourceModel\Faq\CollectionFactory $collection,
        \Magento\Framework\Filesystem\Io\File $systemFile,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magedelight\Faqs\Model\ResourceModel\Category\CollectionFactory $faqCategoryCollectionFactory,
        array $data = []
    )
    {
        $this->faqCategoryCollectionFactory = $faqCategoryCollectionFactory;
        parent::__construct($context, $categoryModel, $faqResource, $faqsHelper, $collection, $systemFile, $filterProvider, $data);
    }


    public function getChildrenCategories($categoryId, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        if ($categoryId) {
            $categories = $this->faqCategoryCollectionFactory->create();
            $categories
                ->addFieldToSelect(['category_id','title','path','parent_id'])
                ->addFieldToFilter(
                    'parent_id',
                    [
                        ['eq' => $categoryId]
                    ]
                )->addFieldToFilter(
                    ['store_id', 'store_id'],
                    [
                        ["finset" => [$storeId]],
                        ["finset" => [0]]
                    ]
                )
                ->addFieldToFilter('status', ['eq' => \Magedelight\Faqs\Model\Faq::STATUS_ENABLED])
                ->setOrder('position', 'ASC')
                ->load();
            return $categories;
        }
    }
}
