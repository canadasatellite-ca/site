<?php
/**
 * @category   Mageants AlsoBought
 * @package    Mageants_AlsoBought
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\AlsoBought\Block\Catalog\Cart;

class Collection extends \Mageants\AlsoBought\Block\Catalog\Product\Collection
{

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;


    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Sales\Model\Order\Item $orderItem,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productFactory,
        \Mageants\AlsoBought\Helper\Data $helper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        array $data=[]
    ){
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        parent::__construct(
            $context,
            $orderItem,
            $productFactory,
            $helper,
            $categoryFactory,
            $listProductBlock,
            $data
        );
    }

    /**
     * @return array
     */
    public function getProductCollection()
    {
        $productIds = [];
        $items = $this->cart->getQuote()->getAllItems();
        if(!$items){
            return;
        }
        foreach ($items as $item) {
            $itemIds[] = $item->getProductId();
        }
        $itemIds = implode($itemIds, ",");

        $products = $this->getAlsoBoughtProduct($itemIds);
     
        if(empty($products) && $this->getConfig('alsobought_section/alsobought_cart/alsobought_cart_catalog')){
            if(!$this->getCartCustomProductCollection($itemIds)){
                return;
            }
           return $this->getCartCustomProductCollection($itemIds);
        }

        foreach ($products as $product) {
            $productIds[] = $product['product_id'];
        }
        $productIds = implode($productIds, ",");
        
        $filterProducts = array_diff(array_merge(array($itemIds), array($productIds)),array($itemIds));
        $filterProducts = implode($filterProducts, ",");
        $this->productFactory->clear()->getSelect()->reset('where');
        return $this->productFactory->addAttributeToFilter('entity_id', array('in' => $filterProducts))->addAttributeToSelect('*');
    }

    /**
     * @return array
     */
    public function getAlsoBoughtProduct($productId)
    {

        $sum = new \Zend_Db_Expr('(SUM(`main_table`.`product_id`)/`main_table`.`product_id`)');
        $collection = $this->orderItem->getCollection()
            ->addFieldToSelect(['product_id', 'frequency' => $sum])
            ->removeFieldFromSelect('item_id');
        $select = (!$this->getConfig('alsobought_section/alsobought_cart/alsobought_cart_product_count') ? $collection->getSelect() : $collection->getSelect()->limit($this->getConfig('alsobought_section/alsobought_cart/alsobought_cart_product_count')));
        $connection = $collection->getConnection();
        $orderItemTable = $connection->getTableName('sales_order_item');

        $select->joinLeft(
            ['sub_table' => $orderItemTable],
            '`main_table`.`order_id` = `sub_table`.`order_id` AND `main_table`.`product_id` != `sub_table`.`product_id`',
            ['product_id']
        );
        $collection
            ->addFieldToFilter('main_table.product_id', array('in' => $productId))
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
    public function getCartCustomProductCollection($itemIds) {
        if(is_array($itemIds)){
            $itemIds = end($itemIds);
        }
        $categoryIds = $this->productRepository->getById($itemIds)->getCategoryIds();
        $collection = $this->categoryFactory->create()->load($categoryIds[0])->getProductCollection()->addAttributeToSelect('*');
        return $collection->setPageSize($this->getConfig('alsobought_section/alsobought_cart/alsobought_cart_product_count'));
    }

    /**
     * @return string
     */
    public function getConfig($path) {
         return $this->_helper->getModuleConfig($path);
    }

    public function getType()
    {
        return 'alsoboughtcart';
    }
}