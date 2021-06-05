<?php
namespace Interactivated\Price\Plugin;

class Product
{
    protected $_storeManager;
    protected $priceCurrency;
    const EXCHANGE_RATE = 1.03;

    function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency)
    {
        $this->_storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
    }

    function aroundGetData(\Magento\Catalog\Model\Product $product, \Closure $process, $key = '', $index = null)
    {
        if ($key == 'price' && $product->getTypeId() !== 'bundle') {
            $result = $this->getPrice($product);
            if ($result !== false) {
                return $result;
            }
        }
        if ($key == 'special_price' && $product->getTypeId() !== 'bundle') {
            $result = $this->getSpecialPrice($product);
            if ($result !== false) {
                return $result;
            }
        }
        $price = $process($key, $index);
        return $price;
    }

    function aroundGetSpecialPrice(\Magento\Catalog\Model\Product $product, \Closure $process)
    {
        $result = $this->getSpecialPrice($product);
        if ($result !== false) {
            return $result;
        }
        return $process();
    }

    protected function getSpecialPrice($product)
    {
        $storeCode = $this->_storeManager->getStore()->getCode();
        if($storeCode == 'admin' && !$product->getData('force_adminprices') ){
            return false;
        }
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        if ($currency == 'USD' && $price = $product->getData('special_price_usd')) {
            $price = str_replace(',', '', $price);
            $rate = $this->priceCurrency->convert(1000000);
            //$rate = $rate / self::EXCHANGE_RATE; //add 3% exchange
            $price = $price * 1000000 / $rate;
            return $price;
        }
        if (in_array($currency, ['CAD', 'AUD', 'EUR']) && $product->getData('usd_is_base_price') && $price = $product->getData('special_price_usd')) {
            $price = str_replace(',', '', $price);
            $rate = $this->priceCurrency->convert(1000000, null, 'USD');
            $rate = $rate / self::EXCHANGE_RATE; //add 3% exchange
            $price = $price * 1000000 / $rate;
            return $price;
        }
        return false;
    }

    protected function getPrice($product)
    {
        $storeCode = $this->_storeManager->getStore()->getCode();
        if($storeCode == 'admin' && !$product->getData('force_adminprices') ){
            return false;
        }
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        if ($currency == 'USD' && $price = $product->getData('price_usd')) {
            $price = str_replace(',', '', $price);
            $rate = $this->priceCurrency->convert(1000000);
            //$rate = $rate / self::EXCHANGE_RATE; //add 3% exchange
            $price = $price * 1000000 / $rate;
            return $price;
        }
        if (in_array($currency, ['CAD', 'AUD', 'EUR']) && $product->getData('usd_is_base_price') && $price = $product->getData('price_usd')) {
            $price = str_replace(',', '', $price);
            $rate = $this->priceCurrency->convert(1000000, null, 'USD');
            $rate = $rate / self::EXCHANGE_RATE; //add 3% exchange
            $price = $price * 1000000 / $rate;
            return $price;
        }
        return false;
    }

}