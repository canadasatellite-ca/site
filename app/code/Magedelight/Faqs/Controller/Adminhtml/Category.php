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
namespace Magedelight\Faqs\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magedelight\Faqs\Model\CategoryFactory;
use Magento\Framework\Registry;

abstract class Category extends Action
{
    /**
     * Category factory
     *
     * @var CategoryFactory
     */
    public $categoryFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @param Registry $registry
     * @param CategoryFactory $CategoryFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        CategoryFactory $categoryFactory,
        Context $context
    ) {
    
        $this->coreRegistry = $registry;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magedelight\Categorys\Model\Category
     */
    public function initCategory()
    {
        $categoryId  = (int) $this->getRequest()->getParam('id');
        $category = $this->categoryFactory->create();
        if ($categoryId) {
            $category->load($categoryId);
        }
        $this->coreRegistry->register('faqs_category', $category);
        return $category;
    }

    public function filterData($data)
    {
        return $data;
    }
}
