<?php
namespace Brsw\MagentoDirectoryEx\Model\Rewrite\Currency\Import;
/****
 * Class FixerIo
 * @package Brsw\MagentoDirectoryEx\Model\Override\Currency\Import
 */
class FixerIo extends \Magento\Directory\Model\Currency\Import\FixerIo
{
    /****
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /****
     * @var
     */
    private $currencyConvertUrl;

    /****
     * FixerIo constructor.
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     */
    public function __construct(
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
    )
    {
        parent::__construct($currencyFactory, $scopeConfig, $httpClientFactory);
        $this->scopeConfig = $scopeConfig;
    }

    /****
     * Overridden method
     * @return array
     */
    public function fetchRates()
    {
        $data = [];
        $currencies = $this->_getCurrencyCodes();
        $defaultCurrencies = $this->_getDefaultCurrencyCodes();

        $data = $this->convertBatch($data, 'EUR', $currencies);
        foreach ($currencies as $currencyFrom) {
            ksort($data[$currencyFrom]);
        }

        return $data;
    }

    /****
     * Overridden method
     * @param array $data
     * @param string $currencyFrom
     * @param array $currenciesTo
     * @return array
     */
    private function convertBatch($data, $currencyFrom, $currencies)
    {
        $currenciesStr = implode(',', $currencies);
        $url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, $this->getCurrencyConverterUrl());
        $url = str_replace('{{CURRENCY_TO}}', $currenciesStr, $url);

        set_time_limit(0);
        try {
            $response = $this->getServiceResponse($url);
        } finally {
            ini_restore('max_execution_time');
        }

        foreach ($currencies as $currencyFrom) {
            foreach ($currencies as $currencyTo) {
                if ($currencyFrom == $currencyTo) {
                    $data[$currencyFrom][$currencyTo] = $this->_numberFormat(1);
                } else {
                    if (array_key_exists($currencyTo, $response['rates']) && array_key_exists($currencyFrom,$response['rates'])) {
                        $data[$currencyFrom][$currencyTo] = $this->_numberFormat(
                            (double)$response['rates'][$currencyTo] / (double)$response['rates'][$currencyFrom]
                        );
                    }
                }
            }
        }
        return $data;
    }

    /****
     * Overridden method
     * @param string $url
     * @param int $retry
     * @return array|mixed
     */
    private function getServiceResponse($url, $retry = 0)
    {
        /** @var \Magento\Framework\HTTP\ZendClient $httpClient */
        $httpClient = $this->httpClientFactory->create();
        $response = [];

        try {
            $jsonResponse = $httpClient->setUri(
                $url
            )->setConfig(
                [
                    'timeout' => $this->scopeConfig->getValue(
                        'currency/fixerio/timeout',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ),
                ]
            )->request(
                'GET'
            )->getBody();

            $response = json_decode($jsonResponse, true);
        } catch (\Exception $e) {
            if ($retry == 0) {
                $response = $this->getServiceResponse($url, 1);
            }
        }
        return $response;
    }

    /****
     * @return mixed
     */
    private function getAccessKey()
    {
        return $this->scopeConfig->getValue(
            'fixerio/configuration/access_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /***
     * @return string
     */
    private function getCurrencyConverterUrl()
    {
        if (!$this->currencyConvertUrl){
            $access_key = ($key = $this->getAccessKey()) ? 'access_key='.$key.'&' : '';
            $this->currencyConvertUrl = 'http://data.fixer.io/api/latest?'.$access_key.'base={{CURRENCY_FROM}}&symbols={{CURRENCY_TO}}';
        }
        return $this->currencyConvertUrl;
    }
}