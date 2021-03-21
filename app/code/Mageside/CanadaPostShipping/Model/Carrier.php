<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model;

use Magento\Framework\Xml\Security;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Tracking\Result as TrackingResult;

/**
 * Canada Post shipping implementation
 */
class Carrier extends AbstractCarrierOnline implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * Code of the carrier
     *
     * @var string
     */
    const CODE = 'canadapost';

    const HANDLING_TYPE_FIXED_PERCENT = 'FP';

    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Carrier helper
     *
     * @var \Mageside\CanadaPostShipping\Helper\Carrier
     */
    protected $_carrierHelper;

    /**
     * Rate request data
     *
     * @var RateRequest|null
     */
    protected $_request = null;

    /**
     * Rate result data
     *
     * @var Result|TrackingResult
     */
    protected $_result = null;

    /**
     * @inheritdoc
     */
    protected $_debugReplacePrivateDataKeys = [
        'mailed-by', 'contract-id'
    ];

    /**
     * @var \Mageside\CanadaPostShipping\Model\Service\Rating
     */
    protected $_rateClient;

    /**
     * @var \Mageside\CanadaPostShipping\Model\Service\RatingFactory
     */
    protected $_rateClientFactory;

    /**
     * @var Service\TrackingFactory
     */
    protected $_trackingClientFactory;

    /**
     * @var Service\Shipment
     */
    protected $_shipmentClientFactory;

    /**
     * @var Service\PostofficeFactory
     */
    protected $_postOfficeClientFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Carrier constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Security $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param TrackingResult\ErrorFactory $trackErrorFactory
     * @param TrackingResult\StatusFactory $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param Service\RatingFactory $ratingClientFactory
     * @param Service\TrackingFactory $trackingClientFactory
     * @param Service\ShipmentFactory $shipmentClientFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateTime
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Mageside\CanadaPostShipping\Model\Service\RatingFactory $ratingClientFactory,
        \Mageside\CanadaPostShipping\Model\Service\TrackingFactory $trackingClientFactory,
        \Mageside\CanadaPostShipping\Model\Service\ShipmentFactory $shipmentClientFactory,
        \Mageside\CanadaPostShipping\Model\Service\PostofficeFactory $postOfficeClientFactory,
        \Magento\Framework\Registry $registry,
        \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateTime,
        array $data = []
    ) {
        $this->_rateClientFactory = $ratingClientFactory;
        $this->_trackingClientFactory = $trackingClientFactory;
        $this->_shipmentClientFactory = $shipmentClientFactory;
        $this->_postOfficeClientFactory = $postOfficeClientFactory;
        $this->_registry = $registry;
        $this->_carrierHelper = $carrierHelper;
        $this->_localeDate = $dateTime;

        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
    }

    /**
     * Processing additional validation to check if carrier applicable.
     *
     * @param \Magento\Framework\DataObject $request
     * @return $this|bool|\Magento\Framework\DataObject
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function proccessAdditionalValidation(\Magento\Framework\DataObject $request)
    {
        //Skip by item validation if there is no items in request
        if (!count($this->getAllItems($request))) {
            return $this;
        }

        $maxAllowedWeight = (double)$this->getConfigData('max_package_weight');
        $maxAllowedWeight = $this->_carrierHelper
            ->convertMeasureWeight(
                $maxAllowedWeight,
                \Zend_Measure_Weight::KILOGRAM,
                $this->_carrierHelper->getStoreWeightUnit($request->getStoreId())
            );

        $errorMsg = '';
        $configErrorMsg = $this->getConfigData('specificerrmsg');
        $defaultErrorMsg = __('The shipping module is not available.');
        $showMethod = $this->getConfigData('showmethod');
        $nonMailableAttribute = $this->getConfigData('non_mailable_attribute');

        /** @var $item \Magento\Quote\Model\Quote\Item */
        foreach ($this->getAllItems($request) as $item) {
            $product = $item->getProduct();
            if ($product && $product->getId()) {
                $weight = $product->getWeight();
                $stockItemData = $this->stockRegistry->getStockItem(
                    $product->getId(),
                    $item->getStore()->getWebsiteId()
                );
                $doValidation = true;

                if ($stockItemData->getIsQtyDecimal() && $stockItemData->getIsDecimalDivided()) {
                    if ($stockItemData->getEnableQtyIncrements() && $stockItemData->getQtyIncrements()
                    ) {
                        $weight = $weight * $stockItemData->getQtyIncrements();
                    } else {
                        $doValidation = false;
                    }
                } elseif ($stockItemData->getIsQtyDecimal() && !$stockItemData->getIsDecimalDivided()) {
                    $weight = $weight * $item->getQty();
                }

                $doValidation = $maxAllowedWeight ? $doValidation : false;

                if ($doValidation && $weight > $maxAllowedWeight) {
                    $errorMsg = $configErrorMsg ? $configErrorMsg : $defaultErrorMsg;
                    break;
                }

                if ($nonMailableAttribute !== 'none' && $product->getData($nonMailableAttribute)) {
                    $errorMsg = $configErrorMsg ? $configErrorMsg : $defaultErrorMsg;
                    break;
                }
            }
        }

        if (!$errorMsg && !$request->getDestPostcode() && $this->isZipCodeRequired($request->getDestCountryId())) {
            $errorMsg = __('This shipping method is not available. Please specify the zip code.');
        }

        $address = $this->getAllItems($request)[0]->getQuote()->getShippingAddress();
        if ($officeId = $address->getData('canada_dpo_id')) {
            $officePostcode = $this->getOfficePostCodeById($officeId);
            if (strtolower($officePostcode) !== strtolower(trim(str_replace(' ', '', $request->getDestPostcode())))) {
                $errorMsg = __('This shipping address is incorrect. The office id does not match the zip code.');
            }
        }

        if ($errorMsg && $showMethod) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($errorMsg);

            return $error;
        } elseif ($errorMsg) {
            return false;
        }

        return $this;
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     * @return Result|bool|null
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates()) {
            return $this->getErrorMessage();
        }

        $this->setRequest($request);
        $this->_getQuotes();
        $this->_updateFreeMethodQuote($request);

        return $this->getResult();
    }

    /**
     * Do remote request for and handle errors
     *
     * @return Result
     */
    protected function _getQuotes()
    {
        $this->_result = $this->_rateFactory->create();
        $response = $this->_doRatesRequest();
        if (isset($response['rates'])) {
            $amount = $this->_rawRequest->getValue();
            if ($this->getRateClient()->isCoverageEnabled($amount)) {
                $response = $this->getRateClient()
                    ->updateRatesWithCoverage($response['rates'], $amount, $this->_rawRequest);
            }
        }
        $preparedGeneral = $this->_prepareRateResponse($response);
        if (!$preparedGeneral->getError()
            || $this->_result->getError() && $preparedGeneral->getError()
            || empty($this->_result->getAllRates())
        ) {
            $this->_result->append($preparedGeneral);
        }

        return $this->_result;
    }

    /**
     * Prepare and set request to this instance
     *
     * @param RateRequest $request
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function setRequest(RateRequest $request)
    {
        $this->_request = $request;
        $r = new \Magento\Framework\DataObject();
        $r->setStoreId($request->getStoreId());
        if ($request->getLimitMethod()) {
            $r->setService($request->getLimitMethod());
        }
        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = $this->_scopeConfig->getValue(
                \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStoreId()
            );
        }
        $r->setOrigCountry($this->_countryFactory->create()->load($origCountry)->getData('iso2_code'));
        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStoreId()
                )
            );
        }
        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }
        $r->setDestCountry($this->_countryFactory->create()->load($destCountry)->getData('iso2_code'));
        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        }
        if ($request->getDestCity()) {
            $r->setDestCity($request->getDestCity());
        }
        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $r->setWeight($weight);
        if ($request->getFreeMethodWeight() != $request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }
        $r->setValue($request->getPackagePhysicalValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());
        $r->setIsReturn($request->getIsReturn());
        $r->setBaseSubtotalExclTax($request->getPackageValue());
        $r->setBaseSubtotalInclTax($request->getBaseSubtotalInclTax());

        $address = $this->getAllItems($request)[0]->getQuote()->getShippingAddress();
        if ($officeId = $address->getData('canada_dpo_id')) {
            $r->setCanadaPostOfficeId($officeId);
        }

        $this->setRawRequest($r);

        return $this;
    }

    /**
     * @param $officeId
     * @return bool
     */
    private function getOfficePostCodeById($officeId)
    {
        $postOffices = $this->_postOfficeClientFactory->create();
        $office = $postOffices->getPostOfficeDetail($officeId);
        if (!$office['error'] && !empty($office['office']['postcode'])) {
            return $office['office']['postcode'];
        }

        return false;
    }

    /**
     * Get result of request
     *
     * @return Result|TrackingResult
     */
    public function getResult()
    {
        if (!$this->_result) {
            $this->_result = $this->_rateFactory->create();
        }
        return $this->_result;
    }

    /**
     * @return \Mageside\CanadaPostShipping\Model\Service\Rating
     */
    private function getRateClient()
    {
        if (!$this->_rateClient) {
            $this->_rateClient = $this->_rateClientFactory->create();
        }
        return $this->_rateClient;
    }

    /**
     * @return array
     */
    protected function _formRateRequest()
    {
        return $this->getRateClient()->createRateRequest($this->_rawRequest);
    }

    /**
     * Makes remote request to the carrier and returns a response
     *
     * @return mixed
     */
    protected function _doRatesRequest()
    {
        $ratesRequest = $this->getRateClient()->createRateRequest($this->_rawRequest);
        $requestString = serialize($ratesRequest);
        $response = $this->_getCachedQuotes($requestString);
        $debugData = ['request' => $this->filterDebugData($ratesRequest)];

        if ($response === null) {
            try {
                $response = $this->getRateClient()->getRates($ratesRequest);
                $this->_setCachedQuotes($requestString, serialize($response));
                $debugData['result'] = $response;
            } catch (\Exception $e) {
                $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
                $this->_logger->critical($e);
            }
        } else {
            $response = unserialize($response);
            $debugData['result'] = $response;
        }
		/**
		 * 2021-03-21 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * "Prevent the Mageside_CanadaPostShipping module from logging successful rate requests to shipping.log":
		 * https://github.com/canadasatellite-ca/site/issues/25
		 * @see \Mageside\CanadaPostShipping\Model\Service\Rating::getRates()
		 */
        if (!($r = dfa($response, 'rates')) || dfa($r, 'NoRatesMethod')) {
			df_log_l($this, $debugData);
		}
        return $response;
    }

    /**
     * Prepare shipping rate result based on response
     *
     * @param mixed $response
     * @return Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareRateResponse($response)
    {
        $rateArr = [];
        $errorTitle = 'For some reason we can\'t retrieve tracking info right now.';

        if (!empty($response)) {
            if (isset($response['rates'])) {
                foreach ($response['rates'] as $code => $rate) {
                    $rate['price'] = $this->getMethodPrice($rate['cost'], $code);
                    $rateArr[$code] = $rate;
                }
            }

            if (isset($response['debug'])) {
                $this->_debug($response['debug']);
            }
        }

        $result = $this->_rateFactory->create();
        if (empty($rateArr)) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($rateArr as $method => $rateItem) {
                $rate = $this->_rateMethodFactory->create();
                $rate->setCarrier($this->_code);
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $title = $this->getMethodTitle($method) ? $this->getMethodTitle($method) : $rateItem['title'];
                if ($this->_carrierHelper->getConfigCarrier('estimated_delivery_date')
                    && isset($rateItem['estimated_delivery_date'])
                ) {
                    $title .= ' - ' .
                        __(
                            'Est. Delivery %1',
                            $this->_localeDate->formatDateTime(
                                $rateItem['estimated_delivery_date'],
                                \IntlDateFormatter::MEDIUM,
                                \IntlDateFormatter::NONE
                            )
                        );
                }
                $rate->setMethodTitle($title);
                $rate->setCost($rateItem['cost']);
                $rate->setPrice($rateItem['price']);
                $result->append($rate);
            }
        }

        return $result;
    }

    /**
     * @param string $cost
     * @param string $method
     * @return float|string
     */
    public function getMethodPrice($cost, $method = '')
    {
        $cost = max($this->getConfigData('rates_min_price'), $cost);

        $freeMethods = $this->getConfigData('free_methods');
        $freeMethods = !empty(trim($freeMethods)) ? unserialize($freeMethods) : [];
        $freeMethod = '';
        $threshold = 99999999;
        foreach ($freeMethods as $item) {
            if (!empty($item['method']) && $item['method'] == $method) {
                $freeMethod = $method;
                $threshold = !empty($item['threshold']) ? $item['threshold'] : $threshold;
                break;
            }
        }

        $thresholdPrice = $this->getConfigData('free_methods_threshold_price')
            ? $this->_rawRequest->getBaseSubtotalInclTax()
            : $this->_rawRequest->getBaseSubtotalExclTax();

        return $method == $freeMethod
            && $threshold <= $thresholdPrice ? '0.00' : $this->getFinalPriceWithHandlingFee($cost);
    }

    /**
     * @param $method
     * @return array|false
     */
    public function getMethodTitle($method)
    {
        $labels = $this->_carrierHelper->getConfigCarrier('shipping_methods_labels');
        if ($labels) {
            $labels = unserialize($labels);
        }

        $key = strtolower(str_replace('.', '_', $method));
        if (isset($labels[$key])) {
            return !empty(trim($labels[$key]['renamed_label'])) ? $labels[$key]['renamed_label'] : $labels[$key]['default_label'];
        }

        return $title = $this->getCode('method', $method);
    }

    /**
     * @param float $cost
     * @return float
     */
    public function getFinalPriceWithHandlingFee($cost)
    {
        $handlingFee = [
            'fixed'     => $this->getConfigData('handling_fee_fixed'),
            'percent'   => $this->getConfigData('handling_fee_percent')
        ];
        $handlingType = $this->getConfigData('handling_type');
        if (!$handlingType) {
            $handlingType = self::HANDLING_TYPE_FIXED;
        }
        $handlingAction = $this->getConfigData('handling_action');
        if (!$handlingAction) {
            $handlingAction = self::HANDLING_ACTION_PERORDER;
        }

        return $handlingAction == self::HANDLING_ACTION_PERPACKAGE ? $this->_getPerpackagePrice(
            $cost,
            $handlingType,
            $handlingFee
        ) : $this->_getPerorderPrice(
            $cost,
            $handlingType,
            $handlingFee
        );
    }

    /**
     * Get final price for shipping method with handling fee per package
     *
     * @param float $cost
     * @param string $handlingType
     * @param float $handlingFee
     * @return float
     */
    protected function _getPerpackagePrice($cost, $handlingType, $handlingFee)
    {
        if ($handlingType == self::HANDLING_TYPE_PERCENT) {
            return ($cost + $cost * $handlingFee['percent'] / 100) * $this->_numBoxes;
        }

        if ($handlingType == self::HANDLING_TYPE_FIXED_PERCENT) {
            return ($cost + $handlingFee['fixed'] + $cost * $handlingFee['percent'] / 100) * $this->_numBoxes;
        }

        return ($cost + $handlingFee['fixed']) * $this->_numBoxes;
    }

    /**
     * Get final price for shipping method with handling fee per order
     *
     * @param float $cost
     * @param string $handlingType
     * @param float $handlingFee
     * @return float
     */
    protected function _getPerorderPrice($cost, $handlingType, $handlingFee)
    {
        if ($handlingType == self::HANDLING_TYPE_PERCENT) {
            return $cost * $this->_numBoxes + $cost * $this->_numBoxes * $handlingFee['percent'] / 100;
        }

        if ($handlingType == self::HANDLING_TYPE_FIXED_PERCENT) {
            return $cost * $this->_numBoxes + $handlingFee['fixed'] + $cost * $this->_numBoxes * $handlingFee['percent'] / 100;
        }

        return $cost * $this->_numBoxes + $handlingFee['fixed'];
    }

    /********************* Shipping Tracking Processing **************************/

    /**
     * Get tracking
     *
     * @param string|string[] $trackings
     * @return Result|null
     */
    public function getTracking($trackings)
    {
        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }

        foreach ($trackings as $tracking) {
            $this->_result = $this->_trackFactory->create();
            /** @var \Mageside\CanadaPostShipping\Model\Service\Tracking $trackingClient */
            $response = $this->_getCachedQuotes($tracking);
            $trackingClient = $this->_trackingClientFactory->create();
            if ($response === null) {
                $debugData = $trackingClient->getTrackingData($tracking, $this->_result);
                $response = $debugData['result'];
                if (isset($debugData['to_cache'])) {
                    $this->_setCachedQuotes($tracking, serialize($response));
                }
            } else {
                $response = unserialize($response);
                $debugData['result'] = $response;
            }
            $this->_debug($debugData);
            $trackingClient->parseTrackingResponse($tracking, $response);
        }

        return $this->_result;
    }

    /********************* Shipments and Labels Processing **************************/

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param \Magento\Framework\DataObject $request
     * @return \Magento\Framework\DataObject
     */
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $result = new \Magento\Framework\DataObject();

        /**
         * Check is Canada Post shipment didn't created
         */
        if ($cpShipment = $this->_registry->registry('canadapost_shipment')) {
            $cpShipmentId = $cpShipment->getId();
            if ($cpShipmentId) {
                $result->setErrors(__(
                    'Unable to create new shipping label, because Canada Post\'s shipment already created (id: %1).',
                    $cpShipmentId
                ));

                return $result;
            }
        }

        /**
         * Check is Canada Post manifest has group id for contract shipment
         */
        if (!empty($this->getConfigData('contract_id'))) {
            $groupId = null;
            if ($manifest = $this->_registry->registry('canadapost_manifest')) {
                $groupId = $manifest->getGroupId();
            }
            if (!$groupId) {
                $result->setErrors(__('Something went wrong while getting manifest group id.'));

                return $result;
            }
        }

        $this->_prepareShipmentRequest($request);
        $response = $this->_shipmentClientFactory
            ->create()
            ->getShipmentData($request);

        if (!isset($response['error'])) {
            $result->setShippingLabelContent($response['label_content']);
            $result->setTrackingNumber($response['tracking_number']);
            $this->_debug($response['debug']);
        } else {
            $debugData = $response['debug'];
            $this->_debug($debugData);
            if (isset($debugData['result']['error'])) {
                $result->setErrors($debugData['result']['error']);
            } else {
                $result->setErrors(__('Something went wrong while creating shipment.'));
            }
        }
        $result->setGatewayResponse($response['debug']['response']);

        return $result;
    }

    /********************* Other Methods **************************/

    /**
     * Recursive replace sensitive fields in debug data by the mask
     *
     * @param array $data
     * @return array
     */
    protected function filterDebugData($data)
    {
        foreach (array_keys($data) as $key) {
            if (is_array($data[$key])) {
                $data[$key] = $this->filterDebugData($data[$key]);
            } elseif (in_array($key, $this->_debugReplacePrivateDataKeys)) {
                $data[$key] = self::DEBUG_KEYS_MASK;
            }
        }

        return $data;
    }

    /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     * @return array|false
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'method' => [
                'DOM.RP'        => __('Regular Parcel'),
                'DOM.EP'        => __('Expedited Parcel'),
                'DOM.XP'        => __('Xpresspost'),
                'DOM.XP.CERT'   => __('Xpresspost Certified'),
                'DOM.PC'        => __('Priority'),
                'DOM.DT'        => __('Delivered Tonight'),
                'DOM.LIB'       => __('Library Materials'),
                'USA.EP'        => __('Expedited Parcel USA'),
                'USA.PW.ENV'    => __('Priority Worldwide Envelope USA'),
                'USA.PW.PAK'    => __('Priority Worldwide pak USA'),
                'USA.PW.PARCEL' => __('Priority Worldwide Parcel USA'),
                'USA.SP.AIR'    => __('Small Packet USA Air'),
                'USA.TP'        => __('Tracked Packet – USA'),
                'USA.TP.LVM'    => __('Tracked Packet – USA (LVM)'),
                'USA.XP'        => __('Xpresspost USA'),
                'INT.XP'        => __('Xpresspost International'),
                'INT.IP.AIR'    => __('International Parcel Air'),
                'INT.IP.SURF'   => __('International Parcel Surface'),
                'INT.PW.ENV'    => __('Priority Worldwide Envelope Int’l'),
                'INT.PW.PAK'    => __('Priority Worldwide pak Int’l'),
                'INT.PW.PARCEL' => __('Priority Worldwide parcel Int’l'),
                'INT.SP.AIR'    => __('Small Packet International Air'),
                'INT.SP.SURF'   => __('Small Packet International Surface'),
                'INT.TP'        => __('Tracked Packet – International'),
                'NoRatesMethod' => __('No Rate Method'),
            ],
            'packaging' => [
                'DOCUMENTS_AND_PARCELS' => __('Documents and Parcels')
            ],
            'nonDeliveryOptions' => [
                'DOM.RP'        => [],
                'DOM.EP'        => [],
                'DOM.XP'        => [],
                'DOM.XP.CERT'   => [],
                'DOM.PC'        => [],
                'DOM.DT'        => [],
                'DOM.LIB'       => [],
                'USA.EP'        => [],
                'USA.PW.ENV'    => [],
                'USA.PW.PAK'    => [],
                'USA.PW.PARCEL' => [],
                'USA.SP.AIR'    => [],
                'USA.TP'        => [],
                'USA.TP.LVM'    => [],
                'USA.XP'        => [],
                'INT.XP'        => [],
                'INT.IP.AIR'    => [],
                'INT.IP.SURF'   => [],
                'INT.PW.ENV'    => [],
                'INT.PW.PAK'    => [],
                'INT.PW.PARCEL' => [],
                'INT.SP.AIR'    => [],
                'INT.SP.SURF'   => [],
                'INT.TP'        => [],
                'NoRatesMethod' => [],
            ],
            'nonDeliveryLabels' => [
                'RASE'          => __('Return at Sender’s Expense'),
                'RTS'           => __('Return to Sender'),
                'ABAN'          => __('Abandon')
            ]
        ];

        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $allowed = explode(',', $this->getConfigData('allowed_methods'));
        $arr = [];
        foreach ($allowed as $k) {
            $arr[$k] = $this->getCode('method', $k);
        }

        return $arr;
    }

    /**
     * Return container types of carrier
     *
     * @param \Magento\Framework\DataObject|null $params
     * @return array|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getContainerTypes(\Magento\Framework\DataObject $params = null)
    {
        return $this->_getAllowedContainers($params);
    }

    /**
     * Return all container types of carrier
     *
     * @return array|bool
     */
    public function getContainerTypesAll()
    {
        return $this->getCode('packaging');
    }

    /**
     * Get tracking response
     *
     * @return string
     */
    public function getResponse()
    {
        $statuses = '';
        if ($this->_result instanceof \Magento\Shipping\Model\Tracking\Result) {
            if ($trackings = $this->_result->getAllTrackings()) {
                foreach ($trackings as $tracking) {
                    if ($data = $tracking->getAllData()) {
                        if (!empty($data['status'])) {
                            $statuses .= __($data['status']) . "\n<br/>";
                        } else {
                            $statuses .= __('Empty response') . "\n<br/>";
                        }
                    }
                }
            }
        }
        if (empty($statuses)) {
            $statuses = __('Empty response');
        }

        return $statuses;
    }

    /**
     * Set free method request
     *
     * @param string $freeMethod
     * @return void
     */
    protected function _setFreeMethodRequest($freeMethod)
    {
        $r = $this->_rawRequest;
        $weight = $this->getTotalNumOfBoxes($r->getFreeMethodWeight());
        $r->setWeight($weight);
        $r->setService($freeMethod);
    }

    /**
     * For multi package shipments. Delete requested shipments if the current shipment
     * request is failed
     *
     * @param array $data
     * @return bool
     */
    public function rollBack($data)
    {
        return true;
    }

    /**
     * Return structured data of containers witch related with shipping methods
     *
     * @return array|bool
     */
    public function getContainerTypesFilter()
    {
        return $this->getCode('containers_filter');
    }

    /**
     * Return delivery confirmation types of carrier
     *
     * @param \Magento\Framework\DataObject|null $params
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getDeliveryConfirmationTypes(\Magento\Framework\DataObject $params = null)
    {
        return $this->getCode('delivery_confirmation_types');
    }
}
