<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Model\Catalog;

/**
 * Catalog Category model
 *
 * @method \Magento\Catalog\Model\ResourceModel\Category\Collection getCollection()
 */
class Category extends \Magento\Catalog\Model\Category
    implements \Magestore\Webpos\Api\Data\Catalog\CategoryInterface
{

    /** root categoty id   */
    protected $rootCategory;

    public function getRootCategoryId()
    {
        if (!$this->rootCategory) {
            $storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Store\Model\StoreManagerInterface'
            );
            $this->rootCategory = $storeManager->getStore()->getRootCategoryId();
        }
        return $this->rootCategory;
    }

    /**
     * Get category image
     *
     * @return string/null
     */
    public function getImage()
    {
        $storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Store\Model\StoreManagerInterface'
        );
        $url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if ($this->getData('image', null)) {
            return $url. 'catalog/category/'. ltrim(str_replace('\\', '/', $this->getData('image')), '/');
        }
        $block = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magestore\Webpos\Block\Webpos'
        );
        return $block->getViewFileUrl('Magestore_Webpos::images/category/image.jpg');
    }

    /**
     * Retrieve children ids
     *
     * @param boolean $recursive
     * @param boolean $isActive
     * @param boolean $sortByPosition
     * @return string
     */
    public function getChildren($recursive = false, $isActive = true, $sortByPosition = false)
    {
        return $this->getResource()->getChildren($this, $recursive, $isActive, $sortByPosition);
    }


    /**
     * is first category
     * @return int
     */
    public function isFirstCategory()
    {
        $rootCategoryId = $this->getRootCategoryId();
        if ($this->getParentId() == $rootCategoryId) {
            return 1;
        }
        return 0;
    }

}
