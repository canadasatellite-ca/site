<?php
namespace Emizentech\Featuredwidget\Block\Widget;

class Featuredproduct extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'widget/featuredproduct.phtml';

    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 10;
    /**
     * Products count
     *
     * @var int
     */
    protected $_productsCount;
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Image helper
     *
     * @var Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    protected $collection;
    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->_imageHelper = $context->getImageHelper();
        $this->_cartHelper = $context->getCartHelper();
        parent::__construct(
            $context,
            $data
        );
    }

    protected function getCacheLifetime()
    {
        return null;
    }

    /**
     * Image helper Object
     */
    public function imageHelperObj()
    {
        return $this->_imageHelper;
    }

    /**
     * get featured product collection
     */
    public function getFeaturedProduct()
    {
        $limit = $this->getProductLimit();
        if (!$this->collection) {
            $collection = $this->_productCollectionFactory->create();
            $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->setPageSize($limit)
                ->addAttributeToSelect('*');

            $collection->addAttributeToFilter('et_featured', '1');
            $collection->getSelect()->orderRand();
            $this->collection = $collection;
        }

        return $this->collection;
    }

    /**
     * Get the configured limit of products
     * @return int
     */
    public function getProductLimit()
    {
        if ($this->getData('productcount') == '') {
            return self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->getData('productcount');
    }

    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->_cartHelper->getAddUrl($product, $additional);
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }

    public function toHtml()
    {
        $this->setCacheLifetime(10000);
        return parent::toHtml();
    }

    public function getCacheKeyInfo()
    {
        return [
            'BLOCK_TPL',
            $this->_storeManager->getStore()->getCode(),
            $this->getTemplateFile(),
            'base_url' => $this->getBaseUrl(),
            'template' => $this->getTemplate(),
            'rand' => rand(0,3)
        ];
    }
}
