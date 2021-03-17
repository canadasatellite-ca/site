<?php

namespace CanadaSatellite\Theme\Block\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\AutoRelated\Block\Product\ProductList\ProductList as ParentProductList;
use Mageplaza\AutoRelated\Helper\Data;

class ProductList extends ParentProductList
{

    private $storeConfig;

    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        CollectionFactory $productCollectionFactory,
        ResourceConnection $resource,
        Stock $stockHelper,
        Data $helperData,
        StoreManagerInterface $storeConfig,
        array $data = [])
    {
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

    public function getCacheLifetime()
    {
        return 86400;
    }

    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        $cacheKeyInfo['ruleId'] = (is_array($this->getRule())) ? $this->getRule()['rule_id'] : '0';
        $cacheKeyInfo['product_ids'] = implode(',',$this->getProductIds());
        $cacheKeyInfo['currency'] = $this->storeConfig->getStore()->getCurrentCurrencyCode();;
        $params = $this->getRequestDefault();
        unset($params['_']);
        $cacheKeyInfo['params'] = implode(',',$params);
        return $cacheKeyInfo;
    }

    public function showItem($_item) {
        $html = $this->getLayout()
            ->createBlock('CanadaSatellite\Theme\Block\Product\ProductListItem')
            ->setTemplate('CanadaSatellite_Theme::product/list/item.phtml')
            ->setProduct($_item)
            ->setRule($this->rule)
            ->toHtml();
        return $html;
    }
}
