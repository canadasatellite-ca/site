<?php

namespace CanadaSatellite\Theme\Block\Html;

use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

class TopmenuWithCacheKeyInfo extends \Magento\Theme\Block\Html\Topmenu
{

    private $registry;

    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        Registry $registry,
        array $data = [])
    {
        $this->registry = $registry;
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
    }

    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        $topCategoryId = $this->getLevel2Category();
        $cacheKeyInfo['top_category_id'] = $topCategoryId;
        return $cacheKeyInfo;
    }

    public function getLevel2Category(){
        if($this->getCurrentCategory()){
            if($this->getCurrentCategory()->getParentCategories()){
                foreach ($this->getCurrentCategory()->getParentCategories() as $parent) {
                    if ($parent->getLevel() == 2) {
                        return $parent->getId();
                    }
                }
            }
        }
        return '5501';
    }

    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

}
