<?php


namespace MageSuper\CustomProductCategoryUrl\Observer\Catalog;

use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\CatalogUrlRewrite\Model\Category\CanonicalUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\Category\ChildrenUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\Category\CurrentUrlRewritesRegenerator;
use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * Class CategorySaveBefore
 * @package MageSuper\CustomProductCategoryUrl\Observer\Catalog
 */
class CategorySaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Category
     */
    public $categoryModel;

    /**
     * CategorySaveBefore constructor.
     * @param Category $categoryModel
     */
    public function __construct(\Magento\Catalog\Model\Category $categoryModel)
    {
        $this->categoryModel = $categoryModel;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();

        if ($category->getParentId()) {
            $categoryModel = $this->categoryModel->load($category->getParentId());
            $category->setLevel($categoryModel->getLevel() + 1);
        }

        //if ($category->getData('volusion_url') && $category->getData('volusion_url')!==$category->getData('url_key')) {
        $category->setData('url_key_create_redirect', $category->getData('url_key'));
        //$category->setData('url_key',$category->getData('volusion_url'));
        $category->setData('save_rewrites_history', true);
        //}
    }
}
