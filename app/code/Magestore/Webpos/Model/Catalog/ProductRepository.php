<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Model\Catalog;

use Magento\Framework\Api\SortOrder;
use Magento\Catalog\Api\Data\ProductExtension;
use \Magento\CatalogInventory\Model\Stock as Stock;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ProductRepository extends \Magento\Catalog\Model\ProductRepository
    implements \Magestore\Webpos\Api\Catalog\ProductRepositoryInterface
{
    /** @var */
    protected $_productCollection;

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $this->prepareCollection($searchCriteria);
        $storeId = $this->storeManager->getStore()->getId();
        $this->_productCollection->setStoreId($storeId)->addStoreFilter($storeId);
        $this->_productCollection->setCurPage($searchCriteria->getCurrentPage());
        $this->_productCollection->setPageSize($searchCriteria->getPageSize());
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($this->_productCollection->getItems());
        $searchResult->setTotalCount($this->_productCollection->getSize());
        return $searchResult;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductsWithoutOptions(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $this->prepareCollection($searchCriteria);
        $storeId = $this->storeManager->getStore()->getId();
        $this->_productCollection->setStoreId($storeId)->addStoreFilter($storeId);
        $this->_productCollection->setCurPage($searchCriteria->getCurrentPage());
        $this->_productCollection->setPageSize($searchCriteria->getPageSize());
        $this->_productCollection->addAttributeToSelect('*')
            ->getSelect()->joinLeft(
                array('stock_item'=>$this->_productCollection->getTable('cataloginventory_stock_item')),
                'e.entity_id = stock_item.product_id AND stock_item.stock_id = "'.Stock::DEFAULT_STOCK_ID.'"' ,
                array('qty', 'manage_stock', 'backorders', 'min_sale_qty', 'max_sale_qty', 'is_in_stock',
                    'enable_qty_increments', 'qty_increments', 'is_qty_decimal')
            );
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($this->_productCollection->getItems());
        $searchResult->setTotalCount($this->_productCollection->getSize());
        return $searchResult;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareCollection($searchCriteria)
    {
        if (empty($this->_productCollection)) {
            $collection = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection'
            );

            /** Integrate webpos **/
            $eventManage = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Framework\Event\ManagerInterface'
            );
            $permissionHelper = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magestore\Webpos\Helper\Permission'
            );
            $eventManage->dispatch('webpos_catalog_product_getlist', ['collection' => $collection, 'location' => $permissionHelper->getCurrentLocation()]);
            /** End integrate webpos **/

            $this->extensionAttributesJoinProcessor->process($collection);
            $collection->addAttributeToSelect('*');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            //$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
            //$visibleInSite = \Magento\Framework\App\ObjectManager::getInstance()->get(
            //    '\Magento\Catalog\Model\Product\Visibility'
            //)->getVisibleInSiteIds();
            $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            //$collection->addAttributeToFilter('visibility', ['in' => $visibleInSite]);
            //Add filters from root filter group to the collection
            foreach ($searchCriteria->getFilterGroups() as $group) {
                $this->addFilterGroupToCollection($group, $collection);
            }
            /** @var SortOrder $sortOrder */
            foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
            $collection->addAttributeToFilter('type_id', ['in' => $this->getProductTypeIds()]);
            $collection->addVisibleFilter();
            $this->_productCollection = $collection;
        }
    }

    /**
     * get product attributes to select
     * @return array
     */
    public function getSelectProductAtrributes()
    {
        return [
            self::TYPE_ID,
            self::NAME,
            self::PRICE,
            self::SPECIAL_PRICE,
            self::SPECIAL_FROM_DATE,
            self::SPECIAL_TO_DATE,
            self::SKU,
            self::SHORT_DESCRIPTION,
            self::DESCRIPTION,
            self::IMAGE,
            self::FINAL_PRICE
        ];
    }

    /**
     * get product type ids to support
     * @return array
     */
    public function getProductTypeIds()
    {
        $types = [
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
            \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE
        ];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $moduleManager = $objectManager->create('\Magento\Framework\Module\Manager');
        if ($moduleManager->isEnabled('Magestore_Customercredit')) {
            $types[] = 'customercredit';
        }
        return $types;
    }

    /**
     * Get info about product by product SKU
     *
     * @param string $id
     * @param bool $editMode
     * @param int|null $storeId
     * @param bool $forceReload
     * @return \Magestore\Webpos\Api\Data\Catalog\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($id, $editMode = false, $storeId = null, $forceReload = false)
    {

        $cacheKey = $this->getCacheKey([$editMode, $storeId]);
        if (!isset($this->instancesById[$id][$cacheKey]) || $forceReload) {
            $productFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magestore\Webpos\Model\Catalog\ProductFactory'
            );
            $product = $productFactory->create();
            if ($editMode) {
                $product->setData('_edit_mode', true);
            }
            if ($storeId !== null) {
                $product->setData('store_id', $storeId);
            }
            $product->load($id);
            if (!$product->getId()) {
                throw new NoSuchEntityException(__('Requested product doesn\'t exist'));
            }
            $this->instancesById[$id][$cacheKey] = $product;
            $this->instances[$product->getSku()][$cacheKey] = $product;
        }
        return $this->instancesById[$id][$cacheKey];
    }

    /**
     * Get product options
     *
     * @param string $id
     * @param bool $editMode
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptions($id, $editMode = false, $storeId = null)
    {
        $product = $this->getProductById($id, $editMode, $storeId);
        $data = array();
        $data['custom_options'] = $this->getCustomOptions($product);
        if($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $data['bundle_options'] = $product->getBundleOptions();
        }
        if($product->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $data['grouped_options'] = $product->getGroupedOptions();
        }
        if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $data['configurable_options'] = $product->getConfigOptions();
            $data['json_config'] = $product->getJsonConfig();
            $data['price_config'] = $product->getPriceConfig();
        }
        return \Zend_Json::encode($data);
    }


    /**
     * get custom options
     * @params \Magestore\Webpos\Api\Data\Catalog\ProductInterface $product
     * @return array
     */
    public function getCustomOptions($product)
    {
        $customOptions = $product->getOptions();
        $options = array();
        foreach ($customOptions as $child) {
            $values = array();
            if($child->getValues()){
                foreach ($child->getValues() as $value) {
                    $values[] =  $value->getData();
                }
                $child['values'] = $values;
            }
            $options[] = $child->getData();
        }
        return $options;
    }
}