<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Currency;

class Currency extends \Magento\Directory\Model\Currency\Import\AbstractImport
{
    const CURRENCIES = [
        'AF' => 'AFA', 'AL' => 'ALL', 'DZ' => 'DZD', 'AS' => 'USD', 'AD' => 'EUR',
        'AO' => 'AOA', 'AI' => 'XCD', 'AQ' => 'NOK', 'AG' => 'XCD', 'AR' => 'ARA',
        'AM' => 'AMD', 'AW' => 'AWG', 'AU' => 'AUD', 'AT' => 'EUR', 'AZ' => 'AZM',
        'BS' => 'BSD', 'BH' => 'BHD', 'BD' => 'BDT', 'BB' => 'BBD', 'BY' => 'BYR',
        'BE' => 'EUR', 'BZ' => 'BZD', 'BJ' => 'XAF', 'BM' => 'BMD', 'BT' => 'BTN',
        'BO' => 'BOB', 'BA' => 'BAM', 'BW' => 'BWP', 'BV' => 'NOK', 'BR' => 'BRL',
        'IO' => 'GBP', 'BN' => 'BND', 'BG' => 'BGN', 'BF' => 'XAF', 'BI' => 'BIF',
        'KH' => 'KHR', 'CM' => 'XAF', 'CA' => 'CAD', 'CV' => 'CVE', 'KY' => 'KYD',
        'CF' => 'XAF', 'TD' => 'XAF', 'CL' => 'CLF', 'CN' => 'CNY', 'CX' => 'AUD',
        'CC' => 'AUD', 'CO' => 'COP', 'KM' => 'KMF', 'CD' => 'CDZ', 'CG' => 'XAF',
        'CK' => 'NZD', 'CR' => 'CRC', 'HR' => 'HRK', 'CU' => 'CUP', 'CY' => 'EUR',
        'CZ' => 'CZK', 'DK' => 'DKK', 'DJ' => 'DJF', 'DM' => 'XCD', 'DO' => 'DOP',
        'TP' => 'TPE', 'EC' => 'USD', 'EG' => 'EGP', 'SV' => 'USD', 'GQ' => 'XAF',
        'ER' => 'ERN', 'EE' => 'EEK', 'ET' => 'ETB', 'FK' => 'FKP', 'FO' => 'DKK',
        'FJ' => 'FJD', 'FI' => 'EUR', 'FR' => 'EUR', 'FX' => 'EUR', 'GF' => 'EUR',
        'PF' => 'XPF', 'TF' => 'EUR', 'GA' => 'XAF', 'GM' => 'GMD', 'GE' => 'GEL',
        'DE' => 'EUR', 'GH' => 'GHC', 'GI' => 'GIP', 'GR' => 'EUR', 'GL' => 'DKK',
        'GD' => 'XCD', 'GP' => 'EUR', 'GU' => 'USD', 'GT' => 'GTQ', 'GN' => 'GNS',
        'GW' => 'GWP', 'GY' => 'GYD', 'HT' => 'HTG', 'HM' => 'AUD', 'VA' => 'EUR',
        'HN' => 'HNL', 'HK' => 'HKD', 'HU' => 'HUF', 'IS' => 'ISK', 'IN' => 'INR',
        'ID' => 'IDR', 'IR' => 'IRR', 'IQ' => 'IQD', 'IE' => 'EUR', 'IL' => 'ILS',
        'IT' => 'EUR', 'CI' => 'XAF', 'JM' => 'JMD', 'JP' => 'JPY', 'JO' => 'JOD',
        'KZ' => 'KZT', 'KE' => 'KES', 'KI' => 'AUD', 'KP' => 'KPW', 'KR' => 'KRW',
        'KW' => 'KWD', 'KG' => 'KGS', 'LA' => 'LAK', 'LV' => 'LVL', 'LB' => 'LBP',
        'LS' => 'LSL', 'LR' => 'LRD', 'LY' => 'LYD', 'LI' => 'CHF', 'LT' => 'LTL',
        'LU' => 'EUR', 'MO' => 'MOP', 'MK' => 'MKD', 'MG' => 'MGF', 'MW' => 'MWK',
        'MY' => 'MYR', 'MV' => 'MVR', 'ML' => 'XAF', 'MT' => 'EUR', 'MH' => 'USD',
        'MQ' => 'EUR', 'MR' => 'MRO', 'MU' => 'MUR', 'YT' => 'EUR', 'MX' => 'MXN',
        'FM' => 'USD', 'MD' => 'MDL', 'MC' => 'EUR', 'MN' => 'MNT', 'MS' => 'XCD',
        'MA' => 'MAD', 'MZ' => 'MZM', 'MM' => 'MMK', 'NA' => 'NAD', 'NR' => 'AUD',
        'NP' => 'NPR', 'NL' => 'EUR', 'AN' => 'ANG', 'NC' => 'XPF', 'NZ' => 'NZD',
        'NI' => 'NIC', 'NE' => 'XOF', 'NG' => 'NGN', 'NU' => 'NZD', 'NF' => 'AUD',
        'MP' => 'USD', 'NO' => 'NOK', 'OM' => 'OMR', 'PK' => 'PKR', 'PW' => 'USD',
        'PA' => 'PAB', 'PG' => 'PGK', 'PY' => 'PYG', 'PE' => 'PEI', 'PH' => 'PHP',
        'PN' => 'NZD', 'PL' => 'PLN', 'PT' => 'EUR', 'PR' => 'USD', 'QA' => 'QAR',
        'RE' => 'EUR', 'RO' => 'ROL', 'RU' => 'RUB', 'RW' => 'RWF', 'KN' => 'XCD',
        'LC' => 'XCD', 'VC' => 'XCD', 'WS' => 'WST', 'SM' => 'EUR', 'ST' => 'STD',
        'SA' => 'SAR', 'SN' => 'XOF', 'CS' => 'EUR', 'SC' => 'SCR', 'SL' => 'SLL',
        'SG' => 'SGD', 'SK' => 'EUR', 'SI' => 'EUR', 'SB' => 'SBD', 'SO' => 'SOS',
        'ZA' => 'ZAR', 'GS' => 'GBP', 'ES' => 'EUR', 'LK' => 'LKR', 'SH' => 'SHP',
        'PM' => 'EUR', 'SD' => 'SDG', 'SR' => 'SRG', 'SJ' => 'NOK', 'SZ' => 'SZL',
        'SE' => 'SEK', 'CH' => 'CHF', 'SY' => 'SYP', 'TW' => 'TWD', 'TJ' => 'TJR',
        'TZ' => 'TZS', 'TH' => 'THB', 'TG' => 'XAF', 'TK' => 'NZD', 'TO' => 'TOP',
        'TT' => 'TTD', 'TN' => 'TND', 'TR' => 'TRY', 'TM' => 'TMM', 'TC' => 'USD',
        'TV' => 'AUD', 'UG' => 'UGS', 'UA' => 'UAH', 'SU' => 'SUR', 'AE' => 'AED',
        'GB' => 'GBP', 'US' => 'USD', 'UM' => 'USD', 'UY' => 'UYU', 'UZ' => 'UZS',
        'VU' => 'VUV', 'VE' => 'VEF', 'VN' => 'VND', 'VG' => 'USD', 'VI' => 'USD',
        'WF' => 'XPF', 'XO' => 'XOF', 'EH' => 'MAD', 'ZM' => 'ZMK', 'ZW' => 'USD'
    ];

    /**
     * @var \Magento\Directory\Helper\Data
     */
    private $_directoryData;

    /**
     * @var array
     */
    private $_services = [];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $_objectManager;

    /**
     * Currency constructor.
     *
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param array $services
     */
    public function __construct(
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $services = []
    ) {
        $this->_directoryData = $directoryData;
        $this->_objectManager = $objectManager;
        $this->_services = $services;

        parent::__construct($currencyFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function _convert($currencyFrom, $currencyTo)
    {
    }

    /**
     * @param $countryCode
     * @return mixed|string
     */
    public function getCurrencyCodeByCountry($countryCode)
    {
        return !empty(self::CURRENCIES[$countryCode]) ? self::CURRENCIES[$countryCode] : 'USD';
    }

    /**
     * @param $currencyFrom
     * @param $currencyTo
     * @return float|int|null
     */
    public function getCurrencyRate($currencyFrom, $currencyTo)
    {
        $rate = null;
        $allowedCurrencies = $this->_getCurrencyCodes();
        try {
            if (in_array($currencyTo, $allowedCurrencies) && in_array($currencyFrom, $allowedCurrencies)) {
                $rate = $this->_directoryData->currencyConvert(1, $currencyFrom, $currencyTo);
            }
            if (!$rate && !empty($this->_services)) {
                foreach ($this->_services as $name => $service) {
                    $currencyService = $this->_objectManager->create($service);
                    $rate = $currencyService->getCurrencyRate($currencyFrom, $currencyTo);
                    if ($rate) {
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            $rate = null;
        }

        return $rate;
    }
}
