<?php
namespace CanadaSatellite\Theme\Plugin\Model;

class ProductSetCurrency
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    const EXCHANGE_RATE = 1.03;

    /**
     * ProductSetCurrency constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency)
    {
        $this->_storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Closure $process
     * @param string $key
     * @param null $index
     * @return bool|float|int|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGetData(\Magento\Catalog\Model\Product $product, \Closure $process, $key = '', $index = null)
    {
        if ($key == 'price' && $product->getTypeId() !== 'bundle') {
            $result = $this->getPrice($product, 'price_usd');
            if ($result !== false) {
                return $result;
            }
        }
        if ($key == 'special_price' && $product->getTypeId() !== 'bundle') {
            $result = $this->getPrice($product, 'special_price_usd');
            if ($result !== false) {
                return $result;
            }
        }
        $price = $process($key, $index);
        return $price;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Closure $process
     * @return bool|float|int|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGetSpecialPrice(\Magento\Catalog\Model\Product $product, \Closure $process)
    {
        $result = $this->getPrice($product, 'special_price_usd');
        if ($result !== false) {
            return $result;
        }
        return $process();
    }

    /**
     * @param $product
     * @param $priceKey
     * @return bool|float|int|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getPrice($product, $priceKey)
    {
        $storeCode = $this->_storeManager->getStore()->getCode();
        if($storeCode == 'admin' && !$product->getData('force_adminprices') ){
            return false;
        }
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        if ($currency == 'USD' && $price = $product->getData($priceKey)) {
            $price = str_replace(',', '', $price);
            $rate = $this->priceCurrency->convert(1000000);
            $price = $price * 1000000 / $rate;
            return $price;
        }
        return false;
    }

}