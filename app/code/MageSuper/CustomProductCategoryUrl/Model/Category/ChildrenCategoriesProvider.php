<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\CustomProductCategoryUrl\Model\Category;

use Magento\Catalog\Model\Category;

class ChildrenCategoriesProvider extends \Magento\CatalogUrlRewrite\Model\Category\ChildrenCategoriesProvider
{
    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param boolean $recursive
     * @return \Magento\Catalog\Model\Category[]
     */
    public function getChildren(Category $category, $recursive = false)
    {
        return $category->isObjectNew() ? [] : $category->getResourceCollection()
            ->addAttributeToSelect('url_path')
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('volusion_url')
            ->addAttributeToSelect('name')
            ->addIdFilter($this->getChildrenIds($category, $recursive));
    }
}