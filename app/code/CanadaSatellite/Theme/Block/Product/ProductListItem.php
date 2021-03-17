<?php

namespace CanadaSatellite\Theme\Block\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\CacheInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\AutoRelated\Helper\Data;
use Mageplaza\AutoRelated\Block\Product\ProductList\ProductList as ParentProductList;

class ProductListItem extends ParentProductList
{
    const CACHE_LIFETIME = 86400;

    public $cache;

    private $storeConfig;

    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        CollectionFactory $productCollectionFactory,
        ResourceConnection $resource,
        Stock $stockHelper,
        Data $helperData,
        CacheInterface $cache,
        StoreManagerInterface $storeConfig,
        array $data = [])
    {
        $this->cache = $cache;
        $this->storeConfig = $storeConfig;
        parent::__construct(
            $context,
            $productFactory,
            $productCollectionFactory,
            $resource,
            $stockHelper,
            $helperData,
            $data);
    }

    public function setRule($rule)
    {
        $this->rule = $rule;
        return parent::setRule($rule);
    }

    public function _toHtml()
    {
        $identifier = $this->_getCacheIdentifier();
        $content = $this->cache->load($identifier);
        if (!$content){
            $content = parent::_toHtml();
            $this->cache->save($content, $identifier, array(\Magento\Framework\App\Cache\Type\Block::CACHE_TAG), self::CACHE_LIFETIME);
        }

        return $content;
    }

    protected function _getCacheIdentifier()
    {
        $currentCurrency = $this->storeConfig->getStore()->getCurrentCurrencyCode();
        $productId = $this->getProduct()->getId();
        return 'product_item_'.$productId. '_' . $currentCurrency;
    }

}
