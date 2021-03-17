<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Service;

/**
 * Class Rating
 * @package Mageside\CanadaPostShipping\Model\Service
 * @documentation https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/rating/getrates/soap/default.jsf
 */
class Rating extends \Mageside\CanadaPostShipping\Model\Service\AbstractService
{
    /**
     * Prepare data to make request
     *
     * @param $rawRequest
     * @param null $rateCode
     * @param int $coverageAmount
     * @return array
     */
    public function createRateRequest($rawRequest, $rateCode = null, $coverageAmount = 0)
    {
        // get destination data
        if ($rawRequest->getDestCountry() == 'CA') {
            $destination = [
                'domestic' => [
                    'postal-code' => $this->formatPostCode($rawRequest->getDestPostal()),
                ]
            ];
        } elseif ($rawRequest->getDestCountry() == 'US') {
            $destination = [
                'united-states' => [
                    'zip-code' => $this->formatPostCode($rawRequest->getDestPostal()),
                ]
            ];
        } else {
            $destination = [
                'international' => [
                    'country-code' => $rawRequest->getDestCountry(),
                ]
            ];
        }

        $ratesRequest = [
            'quote-type' => $this->_carrierHelper->getConfigCarrier('quote_type'),
            'expected-mailing-date' => $this->getExpectedDays(),
            'parcel-characteristics' => [
                'weight' => $this->convertWeightToKg(
                    $rawRequest->getWeight(),
                    $this->getStoreWeightUnit($rawRequest->getStoreId())
                ),
                'unpackaged' => false,
                'mailing-tube'=> false
            ],
            'origin-postal-code' => $this->formatPostCode($rawRequest->getOrigPostal()),
            'destination' => $destination,
        ];

        if ($this->_carrierHelper->getConfigCarrier('quote_type') == 'commercial') {
            // set customer number
            $ratesRequest['customer-number'] = $this->_carrierHelper
                ->getConfigCarrier('customer_number');

            // set contract id
            if ($this->_carrierHelper->getConfigCarrier('contract_id')) {
                $ratesRequest['contract-id'] = $this->_carrierHelper
                    ->getConfigCarrier('contract_id');
            }
        }


        // set dimensions
        $length = $this->_carrierHelper->getConfigCarrier('default_box_length');
        $width = $this->_carrierHelper->getConfigCarrier('default_box_width');
        $height = $this->_carrierHelper->getConfigCarrier('default_box_height');
        if ($length || $width || $height) {
            $ratesRequest['parcel-characteristics']['dimensions'] = [
                'length'    => round($length, 1),
                'width'     => round($width, 1),
                'height'    => round($height, 1)
            ];
        }

        // set options
        $options = [];
        if ($officeId = $rawRequest->getCanadaPostOfficeId()) {
            $options[] = $options[] = [
                'option-code'           => 'D2PO',
                'option-qualifier-2'    => $officeId
            ];
        }
        $options = $this->getServiceOptions($rawRequest->getValue(), $coverageAmount, $rawRequest->getDestCountry(), $options);

        if (!empty($options)) {
            $ratesRequest['options']['option'] = $options;
        }

        if ($rateCode !== null) {
            $ratesRequest['services']['service-code'] = $rateCode;
        }

        return [
            'get-rates-request' => [
                'mailing-scenario'  => $ratesRequest
            ]
        ];
    }

    /**
     * Prepare rates data
     *
     * @param $ratesRequest
     * @return mixed
     */
    public function getRates($ratesRequest)
    {
        $response = $this->getRawRates($ratesRequest);

        $data = [];
        $allowedMethods = explode(",", $this->_carrierHelper->getConfigCarrier('allowed_methods'));
        if (isset($response->{'price-quotes'})) {
            $rateArr = [];
            foreach ($response->{'price-quotes'}->{'price-quote'} as $rate) {
                $serviceName = (string)$rate->{'service-code'};
                if (in_array($serviceName, $allowedMethods)) {
                    $rateArr[$serviceName] = [
                        'cost' => $this->getRateCost($rate),
                        'title' => $rate->{'service-name'}
                    ];
                    if (isset($rate->{'service-standard'}->{'expected-delivery-date'})) {
                        $rateArr[$serviceName]['estimated_delivery_date'] =
                            $rate->{'service-standard'}->{'expected-delivery-date'};
                    }
                }
            }

            asort($rateArr);
            $data['rates'] = $rateArr;
        } elseif (isset($response->{'messages'})) {
            $debugData = [];
            foreach ($response->{'messages'}->{'message'} as $message) {
                $debugData['result']['errors'][] = [
                    'error' => $message->description,
                    'code' => $message->code
                ];
            }
            $data['debug'] = $debugData;
        }

        if (empty($data['rates']) && in_array('NoRatesMethod', $allowedMethods)) {
            $data['rates']['NoRatesMethod'] = [
                'cost'  => $this->_carrierHelper->getConfigCarrier('norates_price'),
                'title' => __('No Rate Method')
            ];
        }

        return $data;
    }

    /**
     * @param $rate
     * @return mixed
     */
    private function getRateCost($rate)
    {
        if ($this->_carrierHelper->getConfigCarrier('rates_price_type')) {
            $cost = $rate->{'price-details'}->{'due'};
        } else {
            $cost = $rate->{'price-details'}->{'base'};
            if (isset($rate->{'price-details'}->{'adjustments'})) {
                foreach ($rate->{'price-details'}->{'adjustments'}->{'adjustment'} as $adjustment) {
                    $cost += $adjustment->{'adjustment-cost'};
                }
            }
        }

        return $cost;
    }

    /**
     * Get raw response data from service
     *
     * @param $ratesRequest
     * @return mixed
     */
    public function getRawRates($ratesRequest)
    {
        $client = $this->createSoapClient('rating');

        return $client->__soapCall('GetRates', $ratesRequest, null, null);
    }

    /**
     * @param $serviceCode
     * @param $countryCode
     * @return mixed
     */
    public function getService($serviceCode, $countryCode)
    {
        $client = $this->createSoapClient('rating');
        $optionsRequest = [
            'get-service-request' => [
                'locale'                    => 'EN',
                'service-code'              => $serviceCode,
                'destination-country-code'  => $countryCode
            ]
        ];

        return $client->__soapCall('GetService', $optionsRequest, null, null);
    }

    /**
     * Get available max coverage amount depends by service and country
     *
     * @param $rateCode
     * @param $countryCode
     * @return null
     */
    public function getCoverageMaxAmount($rateCode, $countryCode)
    {
        $coverageMaxAmount = null;
        $service = $this->getService($rateCode, $countryCode);
        if (isset($service->{'service'}->{'options'}->{'option'})) {
            foreach ($service->{'service'}->{'options'}->{'option'} as $option) {
                if ($option->{'option-code'} == 'COV') {
                    $coverageMaxAmount = $option->{'qualifier-max'};
                    break;
                }
            }
        }

        return $coverageMaxAmount;
    }

    /**
     * Update rates with coverage option
     *
     * @param $rates
     * @param $packageAmount
     * @param $rawRequest
     * @return array
     */
    public function updateRatesWithCoverage($rates, $packageAmount, $rawRequest)
    {
        foreach ($rates as $rateCode => &$rate) {
            $coverageMaxAmount = $this->getCoverageMaxAmount($rateCode, $rawRequest->getDestCountry());
            if ($coverageMaxAmount === null) {
                unset($rates[$rateCode]);
                continue;
            }
            $coverageAmount = min($packageAmount, $coverageMaxAmount);
            $ratesRequest = $this->createRateRequest($rawRequest, $rateCode, $coverageAmount);
            $newRate = $this->getRates($ratesRequest);
            if (isset($newRate['rates'][$rateCode])) {
                $rate = $newRate['rates'][$rateCode];
            } else {
                unset($rates[$rateCode]);
            }
        }

        if (empty($rates)) {
            $debugData['result']['errors'][] = [
                'error' => 'Services don\'t support coverage.'
            ];

            return ['debug' => $debugData];
        }

        return ['rates' => $rates];
    }

    /**
     * @return false|string
     */
    private function getExpectedDays()
    {
        $expectedDate = date_create();
        $left = $this->_carrierHelper->getConfigCarrier('lead_days')
            ? $this->_carrierHelper->getConfigCarrier('lead_days')
            : 0;
        $workingDays = explode(',', $this->_carrierHelper->getConfigCarrier('working_days'));
        $i = 0;
        while ($left > 0) {
            $i++;
            $expectedDate = date_create("+{$i} days");
            if (in_array(date_format($expectedDate,"N"), $workingDays)) {
                $left--;
            }
        }

        return date_format($expectedDate,"Y-m-d");
    }
}
