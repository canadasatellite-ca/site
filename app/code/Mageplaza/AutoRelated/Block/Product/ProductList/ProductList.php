<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AutoRelated
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AutoRelated\Block\Product\ProductList;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Framework\App\ResourceConnection;
use Magento\Widget\Block\BlockInterface;
use Mageplaza\AutoRelated\Helper\Data;
use Mageplaza\AutoRelated\Model\Config\Source\AddProductTypes;
use Mageplaza\AutoRelated\Model\Config\Source\DisplayStyle;

/**
 * Class ProductList
 * @package Mageplaza\AutoRelated\Block\Product\ProductList
 */
class ProductList extends AbstractProduct implements BlockInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var array
     */
    protected $rule;

    /**
     * @var Stock
     */
    protected $stockHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Mageplaza\AutoRelated\Helper\Data
     */
    protected $helperData;

    /**
     * @var array
     */
    protected $displayTypes;

    /**
     * ProductList constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\CatalogInventory\Helper\Stock $stockHelper
     * @param \Mageplaza\AutoRelated\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        CollectionFactory $productCollectionFactory,
        ResourceConnection $resource,
        Stock $stockHelper,
        Data $helperData,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->productFactory           = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resource                 = $resource;
        $this->stockHelper              = $stockHelper;
        $this->helperData               = $helperData;
    }

    /**
     * Get heading label
     *
     * @return string
     */
    public function getTitleBlock()
    {
        return $this->rule['block_name'];
    }

    /**
     * Get rule id
     *
     * @return int
     */
    public function getRuleId()
    {
        return (int)$this->rule['rule_id'];
    }

    /**
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        $this->setRequestDefault($this->getRequestDefault());
        $collection = [];
        $this->rule = $this->getRule();
        if (!empty($this->rule)) {
            $productIds = $this->getProductIds();

            if ($this->rule['add_ruc_product']) {
                $productIds = $this->getProductTypeIds($productIds);
            }
            if (!empty($productIds)) {
                $collection = $this->productCollectionFactory->create()->addIdFilter($productIds);
                $this->_addProductAttributesAndPrices($collection);
            }
            if ($this->rule['display_out_of_stock']) {
                $collection->setFlag('has_stock_status_filter', true);
            }
            if ($this->rule['limit_number']) {
                $collection->setPageSize($this->rule['limit_number']);
            }
            switch ($this->rule['sort_order_direction']) {
                case 1:
                    $collection->getSelect()->joinLeft(
                        ['soi' => $collection->getTable('sales_order_item')],
                        'e.entity_id = soi.product_id',
                        ['qty_ordered' => 'SUM(soi.qty_ordered)']
                    )
                        ->group('e.entity_id')
                        ->order('qty_ordered DESC');
                    break;
                case 2:
                    $collection->addAttributeToSort('price', 'ASC');
                    break;
                case 3:
                    $collection->addAttributeToSort('price', 'DESC');
                    break;
                case 4:
                    $collection->getSelect()->order('e.created_at DESC');
                    break;
                default:
                    $collection->getSelect()->order('rand()');
                    break;
            }
            $collection->addStoreFilter();
        }

        return $collection;
    }

    /**
     * Get layout config
     *
     * @return int
     */
    public function getLayoutSlider()
    {
        $layout = $this->rule['product_layout'];
        if ($layout && $layout == 1) {
            return false;
        }

        return true;
    }

    /**
     * @param $type
     * @return bool
     */
    public function getShowList($type)
    {
        if (is_null($this->displayTypes)) {
            if ($this->rule['display_additional']) {
                try {
                    $this->displayTypes = $this->helperData->unserialize($this->rule['display_additional']);
                } catch (\Exception $e) {
                    $this->displayTypes = [];
                }
            } else {
                $this->displayTypes = [];
            }
        }

        return in_array($type, $this->displayTypes);
    }

    /**
     * Set request default page
     *
     * @param array $params
     * @return void
     */
    protected function setRequestDefault($params)
    {
        if (!empty($params)) {
            $request = $this->getRequest();
            $request->setRouteName($params['route']);
            $request->setModuleName($params['module']);
            $request->setControllerName($params['controller']);
            $request->setActionName($params['action']);
            $request->setRequestUri($params['uri']);
        }
    }

    /**
     * @param $productIds
     * @return array
     */
    protected function getProductTypeIds($productIds)
    {
        try {
            $addProductTypes = $this->helperData->unserialize($this->rule['add_ruc_product']);
        } catch (\Exception $e) {
            $addProductTypes = [];
        }

        if (!sizeof($addProductTypes)) {
            return $productIds;
        }

        $request = $this->getRequest();

        /** @var Product $product */
        if ($this->helperData->getConfigDisplay() == DisplayStyle::TYPE_AJAX) {
            $productId = $request->getParam('product_id');
            $product   = $this->productFactory->create()->load($productId);
        } else {
            $product = $this->_coreRegistry->registry('current_product');
        }

        if (in_array(AddProductTypes::RELATED_PRODUCT, $addProductTypes)) {
            $relatedProductIds = $product ? $product->getRelatedProductIds() : '';
            $productIds        = $relatedProductIds ? array_unique(array_merge($productIds, $relatedProductIds)) : $productIds;
        }
        if (in_array(AddProductTypes::UP_SELL_PRODUCT, $addProductTypes)) {
            $upSellProductIds = $product ? $product->getUpSellProductIds() : '';
            $productIds       = $upSellProductIds ? array_unique(array_merge($productIds, $upSellProductIds)) : $productIds;
        }
        if (in_array(AddProductTypes::CROSS_SELL_PRODUCT, $addProductTypes)) {
            $crossSellProductIds = $product ? $product->getCrossSellProductIds() : '';
            $productIds          = $crossSellProductIds ? array_unique(array_merge($productIds, $crossSellProductIds)) : $productIds;
        }

        return $productIds;
    }
}
