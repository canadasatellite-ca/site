<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Service;

class AbstractService
{
    /**
     * @var \Mageside\CanadaPostShipping\Helper\Carrier
     */
    protected $_carrierHelper;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface
     */
    protected $_dateTimeFormatter;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var ArtifactFactory
     */
    protected $_artifactService;

    /**
     * @var \Magento\Shipping\Model\Tracking\Result\ErrorFactory
     */
    protected $_trackErrorFactory;

    /**
     * @var \Magento\Shipping\Model\Tracking\Result\StatusFactory
     */
    protected $_trackStatusFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Mageside\CanadaPostShipping\Model\Service\RatingFactory
     */
    protected $_rateClientFactory;

    /**
     * @var \Mageside\CanadaPostShipping\Model\Currency\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * Conflicting Options
     *
     * @see https://www.canadapost.ca/cpo/mc/business/productsservices/developers/messagescodetables.jsf
     * @var array
     */
    protected $conflictOptions = [
        'COD'   => ['D2PO', 'LAD'],
        'SO'    => ['LAD'],
        'D2PO'  => ['HFP', 'COD', 'LAD', 'DNS'],
        'DNS'   => ['PA19', 'HFP', 'PA18', 'LAD', 'D2PO'],
        'HFP'   => ['D2PO', 'DNS', 'LAD'],
        'LAD'   => ['COD', 'PA18', 'HFP', 'DNS', 'SO', 'D2PO', 'PA19'],
        'RASE'  => ['ABAN', 'RTS'],
        'ABAN'  => ['RASE', 'RTS'],
        'RTS'   => ['RASE', 'ABAN'],
        'PA18'  => ['PA19', 'LAD', 'DNS'],
        'PA19'  => ['PA18', 'LAD', 'DNS']
    ];

    /**
     * AbstractService constructor.
     * @param \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeData
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param ArtifactFactory $artifact
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param RatingFactory $ratingClientFactory
     * @param \Mageside\CanadaPostShipping\Model\Currency\CurrencyFactory $currencyFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeData,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Mageside\CanadaPostShipping\Model\Service\ArtifactFactory $artifact,
        \Magento\Framework\Registry $registry,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Mageside\CanadaPostShipping\Model\Service\RatingFactory $ratingClientFactory,
        \Mageside\CanadaPostShipping\Model\Currency\CurrencyFactory $currencyFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_carrierHelper = $carrierHelper;
        $this->_localeDate = $localeData;
        $this->_dateTimeFormatter = $dateTimeFormatter;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_registry = $registry;
        $this->_artifactService = $artifact;
        $this->_trackErrorFactory = $trackErrorFactory;
        $this->_trackStatusFactory = $trackStatusFactory;
        $this->_logger = $logger;
        $this->_rateClientFactory = $ratingClientFactory;
        $this->_currencyFactory = $currencyFactory;
    }

    /**
     * Create soap client with selected wsdl
     *
     * @param string $service
     * @return \SoapClient
     */
    public function createSoapClient($service)
    {
        $client = new SoapClient($this->_carrierHelper, $service);

        return $client;
    }

    /**
     * Prepare list of options desired for the shipment.
     *
     * @param int $amount
     * @param int $coverageAmount
     * @return array
     */
    /**
     * @param float $amount
     * @param float $coverageAmount
     * @param $country
     * @param array $options
     * @return array
     */
    public function getServiceOptions($amount, $coverageAmount, $country, $options = [])
    {
        if ($this->_carrierHelper->getConfigCarrier('collect_on_delivery')) {
            $options[] = [
                'option-code'           => 'COD',
                'option-amount'         => round($amount, 2),
                'option-qualifier-1'    => false
            ];
        }

        if ($option = $this->_carrierHelper->getConfigCarrier('shipment_options')) {
            $options[] = ['option-code' => $option];
        }

        if ($instruction = $this->_carrierHelper->getConfigCarrier('delivery_instructions')) {
            $options[] = ['option-code' => $instruction];
        }

        $signatureThreshold = $this->_carrierHelper->getConfigCarrier('signature_threshold');
        $signatureAmount = ($signatureThreshold <= $amount) ? $amount : 0;
        if ($signatureAmount) {
            $options[] = ['option-code' => 'SO'];
        }

        $coverageThreshold = $this->_carrierHelper->getConfigCarrier('coverage_threshold');
        $coverageAmount = ($coverageThreshold <= $coverageAmount) ? $coverageAmount : 0;
        if ($coverageAmount) {
            $options[] = [
                'option-code'   => 'COV',
                'option-amount' => round($coverageAmount, 2)
            ];
        }

        $options = $this->filterOptionsByCountry($options, $country);
        $options = $this->addRequireOption($options);

        return array_values($options);
    }

    /**
     * Removing incompatible options for international shipments
     *
     * @param $options
     * @param $country
     * @return mixed
     */
    protected function filterOptionsByCountry($options, $country)
    {
        $optionsToRemove = ['COD', 'D2PO', 'PA19', 'PA18', 'LAD', 'DNS', 'HFP'];
        if ($country != 'CA') {
            foreach ($options as $key => $option) {
                if (in_array($option['option-code'], $optionsToRemove)) {
                    unset($options[$key]);
                }
            }
        }

        return $options;
    }

    /**
     * Adding require options (DC, SO)
     *
     * @param $options
     * @return array
     */
    protected function addRequireOption($options)
    {
        $requireDC = ['COV', 'COD', 'SO'];
        $requireSO = ['PA18', 'PA19'];

        $isNeedDC = false;
        $isPresentDC = false;
        $isNeedSO = false;
        $isPresentSO = false;
        foreach ($options as $option) {
            if (in_array($option['option-code'], $requireDC)) {
                $isNeedDC = true;
            }
            if ($option['option-code'] == 'DC') {
                $isPresentDC = true;
            }
            if (in_array($option['option-code'], $requireSO)) {
                $isNeedSO = true;
            }
            if ($option['option-code'] == 'SO') {
                $isPresentSO = true;
            }
        }

        if (!$isPresentSO && $isNeedSO) {
            $options[] = ['option-code' => 'SO'];
            $isNeedDC = true;
        }
        if (!$isPresentDC && $isNeedDC) {
            $options[] = ['option-code' => 'DC'];
        }

        return $options;
    }

    /**
     * @param $amount
     * @return bool
     */
    public function isCoverageEnabled($amount)
    {
        $coverageThreshold = $this->_carrierHelper->getConfigCarrier('coverage_threshold');
        $coverageAmount = ($coverageThreshold <= $amount) ? $amount : 0;

        return !!$coverageAmount;
    }

    /**
     * Convert weight to kilogram
     *
     * @param $weight
     * @param $weightUnit
     * @param int $roundPrecision
     * @return float
     */
    public function convertWeightToKg($weight, $weightUnit, $roundPrecision = 3)
    {
        return round(
            $this->_carrierHelper->convertMeasureWeight(
                $weight,
                $weightUnit,
                \Zend_Measure_Weight::KILOGRAM
            ),
            $roundPrecision
        );
    }

    /**
     * Convert dimensions to centimeter
     *
     * @param $dimension
     * @param $dimensionUnit
     * @param int $roundPrecision
     * @return float
     */
    public function convertDimensionToCm($dimension, $dimensionUnit, $roundPrecision = 1)
    {
        return round(
            $this->_carrierHelper->convertMeasureDimension(
                $dimension,
                $dimensionUnit,
                \Zend_Measure_Length::CENTIMETER
            ),
            $roundPrecision
        );
    }

    /**
     * @param $price
     * @param $fromCurrency
     * @param $toCurrency
     * @return mixed
     */
    public function convertPrice($price, $fromCurrency = null, $toCurrency = null, $roundPrecision = 2)
    {
        return round($price, $roundPrecision);
    }

    /**
     * Get store weight unit setting
     *
     * @param $storeId
     * @return string
     */
    public function getStoreWeightUnit($storeId)
    {
        return $this->_scopeConfig->getValue(
            'general/locale/weight_unit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        ) == 'lbs' ? 'LBS' : 'KILOGRAM';
    }

    /**
     * Format given date in current locale without changing timezone
     *
     * @param string $date
     * @return string
     */
    public function formatDate($date)
    {
        $format = $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);

        return $this->_dateTimeFormatter->formatObject($this->_localeDate->date($date), $format);
    }

    /**
     * Format given time [+ date] in current locale without changing timezone
     *
     * @param $date
     * @return string
     */
    public function formatTime($date)
    {
        $format = $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT);

        return $this->_dateTimeFormatter->formatObject($this->_localeDate->date($date), $format);
    }

    /**
     * @param $postcode
     * @return mixed
     */
    public function formatPostCode($postcode)
    {
        return strtoupper(preg_replace('/[\s]+/', '', $postcode));
    }
}
