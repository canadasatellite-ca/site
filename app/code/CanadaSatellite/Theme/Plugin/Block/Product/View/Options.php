<?php

namespace CanadaSatellite\Theme\Plugin\Block\Product\View;

use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Directory\Model\Currency;
use Magento\Catalog\Block\Product\View\Options as BaseOptions;
use Magento\Store\Model\StoreManagerInterface;
use CanadaSatellite\Theme\Plugin\Model\Currency as CasatCurrency;

class Options
{

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var Currency
     */
    protected $_currencyModel;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Configurable constructor.
     * @param DecoderInterface $jsonDecoder
     * @param EncoderInterface $jsonEncoder
     */
    function __construct(
        DecoderInterface $jsonDecoder,
        EncoderInterface $jsonEncoder,
        Currency $currencyModel,
        StoreManagerInterface $storeManager
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->_currencyModel = $currencyModel;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param BaseOptions $subject
     * @param $result
     * @return string
     */
    function afterGetJsonConfig(BaseOptions $subject, $result)
    {
        $config = $this->jsonDecoder->decode($result);
        $frontendCurrency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        foreach ($subject->getOptions() as $option) {
            $currencyCode = $option->getCurrencyCode();
            $optionCurrencyRate = $this->_currencyModel->getCurrencyRates($currencyCode, 'CAD')['CAD'];
            if ($option->getType() == 'drop_down') {
                $data = $config[$option->getId()];
                foreach ($data as $key => $datum) {
                    $prices = $datum['prices'];
                    if ($frontendCurrency !== $currencyCode) {
                        $config[$option->getId()][$key]['prices']['oldPrice']['amount'] = $prices['oldPrice']['amount'] * $optionCurrencyRate * CasatCurrency::EXCHANGE_RATE;
                        $config[$option->getId()][$key]['prices']['basePrice']['amount'] = $prices['basePrice']['amount'] * $optionCurrencyRate * CasatCurrency::EXCHANGE_RATE;
                        $config[$option->getId()][$key]['prices']['finalPrice']['amount'] = $prices['finalPrice']['amount'] * $optionCurrencyRate * CasatCurrency::EXCHANGE_RATE;
                    } else {
                        $config[$option->getId()][$key]['prices']['oldPrice']['amount'] = $prices['oldPrice']['amount'] * $optionCurrencyRate;
                        $config[$option->getId()][$key]['prices']['basePrice']['amount'] = $prices['basePrice']['amount'] * $optionCurrencyRate;
                        $config[$option->getId()][$key]['prices']['finalPrice']['amount'] = $prices['finalPrice']['amount'] * $optionCurrencyRate;
                    }

                }
            }
        }

        return $this->jsonEncoder->encode($config);
    }
}