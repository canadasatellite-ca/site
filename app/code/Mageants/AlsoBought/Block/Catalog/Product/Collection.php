<?php
/**
 * @category   Mageants AlsoBought
 * @package    Mageants_AlsoBought
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\AlsoBought\Block\Catalog\Product;

class Collection extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Sales\Model\Order\Item
     */
    protected $orderItem;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Mageants\AlsoBought\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $listProductBlock;

    protected $storeId;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Sales\Model\Order\Item $orderItem,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productFactory,
        \Mageants\AlsoBought\Helper\Data $helper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        array $data=[]
    ){
        $this->_registry = $context->getRegistry();
        $this->orderItem = $orderItem;
        $this->productFactory = $productFactory;
        $this->_helper = $helper;
        $this->categoryFactory = $categoryFactory;
        $this->listProductBlock = $listProductBlock;
        $this->storeId = $context->getStoreManager()->getStore()->getId();
        parent::__construct($context,$data);
    }

    public function getCatalogLayout()
    {
        return $this->layoutFactory->create();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCurrentProductId()
    {
        if($product = $this->_registry->registry('current_product')){
            return $product->getId();
        }
        return;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        $productIds = array();
        $products = $this->getAlsoBoughtProduct($this->getCurrentProductId());
        if(empty($products) && $this->getConfig('alsobought_section/alsobought_product/alsobought_product_catalog') && $this->_registry->registry('current_product')){
            if(!$this->getCustomProductCollection()){
                return;
            }
           return $this->_productCollection = $this->getCustomProductCollection();
        }

        foreach ($products as $product) {
            $productIds[] = $product['product_id'];
        }
        $productIds = implode($productIds, ",");
        return $this->productFactory->addAttributeToFilter('entity_id', array('in' => $productIds))->addAttributeToSelect('*');
    }

    /**
     * @return array
     */
    public function getAlsoBoughtProduct($productId)
    {
        $collection = $this->orderItem->getCollection()
            ->addFieldToSelect(['product_id'])
            ->removeFieldFromSelect('item_id');

        $select = (!$this->getConfig('alsobought_section/alsobought_product/alsobought_product_product_count') ? $collection->getSelect() : $collection->getSelect()->limit($this->getConfig('alsobought_section/alsobought_product/alsobought_product_product_count')));
        $connection = $collection->getConnection();
        $orderItemTable = $connection->getTableName('sales_order_item');

        $select->joinLeft(
            ['sub_table' => $orderItemTable],
            '`main_table`.`order_id` = `sub_table`.`order_id` AND `main_table`.`product_id` != `sub_table`.`product_id`',
            ['product_id']
        );
        $collection
            ->addFieldToFilter('main_table.product_id', ['eq' => $productId])
            ->addFieldToFilter('sub_table.parent_item_id', ['null' => true])
            ->addFieldToFilter('sub_table.store_id',$this->storeId)
            ->setOrder('frequency');
        $select->group('sub_table.product_id');
        $result = $connection->fetchAll($select);
        return $result;
    }

    /**
     * @return array
     */
    public function getCustomProductCollection() {
        $categoryIds = $this->_registry->registry('current_product')->getCategoryIds();
        $collection = $this->categoryFactory->create()->load($categoryIds[0])->getProductCollection()->addAttributeToSelect('*');
        return $collection->setPageSize($this->getConfig('alsobought_section/alsobought_product/alsobought_product_product_count'));
    }

    /**
     * @return string
     */
    public function getConfig($path) {
         return $this->_helper->getModuleConfig($path);
    }

    public function getAddToCartPostParams($product){
        return $this->listProductBlock->getAddToCartPostParams($product);
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getType()
    {
        return 'alsobought';
    }
}