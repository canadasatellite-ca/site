<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Service;

/**
 * Class Shipment
 * @package Mageside\CanadaPostShipping\Model\Service
 * @documentation https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/shippingmanifest/soap/createshipment.jsf
 * @documentation https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/onestepshipping/soap/createshipment.jsf
 */
class Shipment extends \Mageside\CanadaPostShipping\Model\Service\AbstractService
{
    /**
     * @param $request
     * @return array
     */
    public function getShipmentData($request)
    {
        $client = $this->createSoapClient('shipment');
        $requestClient = $this->formShipmentRequest($request);
        $operation = $this->_carrierHelper->isContractShipment() ? 'CreateShipment' : 'CreateNCShipment';
        $response = $client->__soapCall($operation, $requestClient, null, null);

        $result = [];
        $result['client'] = $client;
        if (isset($response->{'messages'})) {
            $debugData = [
                'request'   => $client->__getLastRequest(),
                'response'  => $client->__getLastResponse(),
                'result'    => ['error' => '', 'code' => '', 'xml' => $client->__getLastResponse()],
            ];
            if (is_array($response->{'messages'}->{'message'})) {
                foreach ($response->{'messages'}->{'message'} as $message) {
                    $debugData['result']['code'] .= $message->code . '; ';
                    $debugData['result']['error'] .= $message->description . '; ';
                }
            }
            $result['error'] = true;
            $result['debug'] = $debugData;
        } else {
            $responseData = null;
            if (isset($response->{'non-contract-shipment-info'})) {
                $responseData = $response->{'non-contract-shipment-info'};
            } elseif (isset($response->{'shipment-info'})) {
                $responseData = $response->{'shipment-info'};
            }
            if ($responseData) {
                $artifacts = [];
                foreach ($responseData->{'artifacts'}->{'artifact'} as $artifact) {
                    $artifacts[] = [
                        'artifact_id' => $artifact->{'artifact-id'},
                        'page_index' => $artifact->{'page-index'}
                    ];
                }

                if ($cpShipment = $this->_registry->registry('canadapost_shipment')) {
                    $cpShipment->setShipmentId($responseData->{'shipment-id'});
                    $cpShipment->setCost($this->getShipmentPrice($cpShipment->getShipmentId()));
                    if (isset($responseData->{'shipment-status'})) {
                        $cpShipment->setStatus($responseData->{'shipment-status'});
                    }
                }

                $result['label_content'] = $this->_artifactService->create()->getArtifacts($artifacts);
                if (isset($responseData->{'tracking-pin'})) {
                    $result['tracking_number'] = $responseData->{'tracking-pin'};
                } else {
                    $result['tracking_number'] = __('unknown');
                }

                if ($cpShipment) {
                    $cpShipment->setTrackingNumber($result['tracking_number']);
                }
            }

            $result['debug'] = [
                'request'   => $client->__getLastRequest(),
                'response'  => $client->__getLastResponse()
            ];
        }

        return $result;
    }

    /**
     * Form array with appropriate structure for shipment request
     *
     * @param \Magento\Framework\DataObject $request
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function formShipmentRequest(\Magento\Framework\DataObject $request)
    {
        if ($request->getReferenceData()) {
            $referenceData = $request->getReferenceData() . $request->getPackageId();
        } else {
            $referenceData = 'Order #' .
                $request->getOrderShipment()->getOrder()->getIncrementId() .
                ' P' .
                $request->getPackageId();
        }

        $packageParams = $request->getPackageParams();
        $weight = $this->convertWeightToKg($request->getPackageWeight(), $packageParams->getWeightUnits());
        $height = $this->convertDimensionToCm($packageParams->getHeight(), $packageParams->getDimensionUnits());
        $width = $this->convertDimensionToCm($packageParams->getWidth(), $packageParams->getDimensionUnits());
        $length = $this->convertDimensionToCm($packageParams->getLength(), $packageParams->getDimensionUnits());

        $deliveryRequest = [
            'service-code' => $request->getShippingMethod(),
            'sender' => [
                'company' => substr($request->getShipperContactCompanyName(), 0, 44),
                'contact-phone' => $request->getShipperContactPhoneNumber(),
                'address-details' => [
                    'address-line-1' => substr($request->getShipperAddressStreet1(), 0, 44),
                    'address-line-2' => $request->getShipperAddressStreet2() ?
                        substr($request->getShipperAddressStreet2(), 0, 44) :
                        '',
                    'city' => substr($request->getShipperAddressCity(), 0, 40),
                    'prov-state' => $request->getShipperAddressStateOrProvinceCode(),
                    'postal-zip-code' => $this->formatPostCode($request->getShipperAddressPostalCode())
                ]
            ],
            'destination' => [
                'name' => substr($request->getRecipientContactPersonName(), 0, 44),
                'company' => $request->getRecipientContactCompanyName() ?
                    substr($request->getRecipientContactCompanyName(), 0, 44) :
                    '',
                'client-voice-number' => $request->getRecipientContactPhoneNumber(),
                'address-details' => [
                    'address-line-1' => substr($request->getRecipientAddressStreet1(), 0, 44),
                    'address-line-2' => $request->getRecipientAddressStreet2() ?
                        substr($request->getRecipientAddressStreet2(), 0, 44) :
                        '',
                    'city' => substr($request->getRecipientAddressCity(), 0, 40),
                    'prov-state' => $request->getRecipientAddressStateOrProvinceCode(),
                    'country-code' => $request->getRecipientAddressCountryCode(),
                    'postal-zip-code' => $this->formatPostCode($request->getRecipientAddressPostalCode())
                ]
            ],
            'parcel-characteristics' => [
                'weight' => $weight,
                'unpackaged' => false,
                'mailing-tube'=> false
            ],
            'notification' => [
                'email' => $request->getRecipientEmail(),
                'on-shipment' => $this->_carrierHelper->getNotificationConfig('on-shipment'),
                'on-exception' => $this->_carrierHelper->getNotificationConfig('on-exception'),
                'on-delivery' => $this->_carrierHelper->getNotificationConfig('on-delivery'),
            ],
            'preferences' => [
                'show-packing-instructions' => true,
                'show-postage-rate'         => false,
                'show-insured-value'        => true,
            ],
            'references' => [
                'customer-ref-1' => $referenceData
            ]
        ];

        // set dimensions
        if ($length || $width || $height) {
            $deliveryRequest['parcel-characteristics']['dimensions'] = [
                'length'    => $length,
                'width'     => $width,
                'height'    => $height
            ];
        }

        // set options
        $options = $this->getOptions($request);
        if (!empty($options)) {
            $deliveryRequest['options']['option'] = $options;
        }

        // for international shipping
        if ($request->getShipperAddressCountryCode() != $request->getRecipientAddressCountryCode()) {
            /** @var \Mageside\CanadaPostShipping\Model\Currency\Currency $currencyService */
            $currencyService = $this->_currencyFactory->create();
            $reasonForExport = $this->_carrierHelper->getConfigCarrier('reason_for_export');
            $destinationCurrency = $currencyService->getCurrencyCodeByCountry(
                $request->getRecipientAddressCountryCode()
            );
            $currencyRate = $currencyService->getCurrencyRate(
                $request->getBaseCurrencyCode(),
                $destinationCurrency
            );
            if (!$currencyRate) {
                $destinationCurrency = $request->getBaseCurrencyCode();
                $currencyRate = 1;
            }
            $deliveryRequest['customs'] = [
                'currency'              => $destinationCurrency,
                'conversion-from-cad'   => $currencyRate,
                'reason-for-export'     => $reasonForExport,
                'sku-list' => [
                    'item' => $this->getSkuList($request)
                ]
            ];
            if ($reasonForExport == 'OTH') {
                $deliveryRequest['customs']['other-reason'] = substr(
                    $this->_carrierHelper->getConfigCarrier('other_reason_for_export'),
                    0,
                    44
                );
            }
        }

        $deliveryRequest = $this->correctTotalWeight($deliveryRequest);

        if ($this->_carrierHelper->getConfigCarrier('contract_id')) {
            $deliveryRequest['sender']['address-details']['country-code'] = 'CA';
            $deliveryRequest['print-preferences'] = [
                'output-format' => $this->_carrierHelper->getConfigCarrier('output_format'),
                'encoding' => 'PDF'
            ];
            $deliveryRequest['settlement-info'] = [
                'contract-id' => $this->_carrierHelper->getConfigCarrier('contract_id'),
                'intended-method-of-payment' => $this->_carrierHelper->getConfigCarrier('has_default_credit_card') ? 'CreditCard' : 'Account'
            ];
            $data = [
                'create-shipment-request' => [
                    'mailed-by' => $this->_carrierHelper->getMailedBy(),
                    'shipment' => [
                        'requested-shipping-point' => $this->formatPostCode($request->getShipperAddressPostalCode()),
                        'expected-mailing-date' => date('Y-m-d'),
                        'delivery-spec' => $deliveryRequest
                    ]
                ]
            ];
            if ($manifest = $this->_registry->registry('canadapost_manifest')) {
                $data['create-shipment-request']['shipment']['groupIdOrTransmitShipment']['ns1:group-id'] =
                    $manifest->getGroupId();
            }

            return $data;
        } else {
            return [
                'create-non-contract-shipment-request' => [
                    'mailed-by' => $this->_carrierHelper->getMailedBy(),
                    'non-contract-shipment' => [
                        'requested-shipping-point' => $this->formatPostCode($request->getShipperAddressPostalCode()),
                        'delivery-spec' => $deliveryRequest
                    ]
                ]
            ];
        }
    }

    /**
     * @param $request
     * @return array
     */
    protected function getOptions($request)
    {
        $packageAmount = $request->getPackageParams()->getCustomsValue();
        $rateCode = $request->getShippingMethod();
        $countryCode = $request->getRecipientAddressCountryCode();

        /** @var \Mageside\CanadaPostShipping\Model\Service\Rating $rateClient */
        $rateClient = $this->_rateClientFactory->create();

        $coverageAmount = 0;
        if ($rateClient->isCoverageEnabled($packageAmount)) {
            $coverageAmount = $packageAmount;
        }

        $options = [];
        $address = $request->getOrderShipment()->getShippingAddress();
        if ($officeId = $address->getData('canada_dpo_id')) {
            $options[] = [
                'option-code'           => 'D2PO',
                'option-qualifier-2'    => $officeId
            ];
        }
        $options = $this->getServiceOptions($packageAmount, $coverageAmount, $countryCode, $options);

        // for international shipping
        if ($request->getShipperAddressCountryCode() != $request->getRecipientAddressCountryCode()) {
            // Non-delivery handling codes
            $options[]['option-code'] = $this->_carrierHelper->getMethodNonDeliveryOption($request->getShippingMethod());
        }

        // get available service options
        $response = $rateClient->getService($rateCode, $countryCode);
        $serviceOptions = [];
        if (isset($response->{'service'}->{'options'}->{'option'})) {
            foreach ($response->{'service'}->{'options'}->{'option'} as $option) {
                $serviceOptions[$option->{'option-code'}] = $option;
            }
        }

        // clean unavailable options
        $returns = ['RTS', 'RASE', 'ABAN'];
        foreach ($options as $key => $option) {
            if (!empty($serviceOptions[$option['option-code']]) && !in_array($option['option-code'], $returns)) {
                if ($option['option-code'] == 'COV') {
                    $options[$key]['option-amount'] = round(
                        min($coverageAmount, $serviceOptions[$option['option-code']]->{'qualifier-max'}),
                        2
                    );
                }
            } elseif (in_array($option['option-code'], $returns)) {
                $serviceReturn = false;
                foreach ($returns as $return) {
                    if (!empty($serviceOptions[$return])) {
                        $serviceReturn = $return;
                        break;
                    }
                }
                if (empty($serviceOptions[$option['option-code']])) {
                    if ($serviceReturn) {
                        $options[$key]['option-code'] = $serviceReturn;
                    } else {
                        unset($options[$key]);
                    }
                }
            } else {
                unset($options[$key]);
            }
        }

        return array_values($options);
    }

    /**
     * Get products data
     *
     * @param $request
     * @return array
     */
    protected function getSkuList($request)
    {
        $skuList = [];
        $productIds = [];
        $packageItems = $request->getPackageItems();
        foreach ($packageItems as $itemShipment) {
            $productIds[] = $itemShipment['product_id'];
        }

        // get countries of manufacture
        $productCollection = $this->_productCollectionFactory->create()->addStoreFilter(
            $request->getStoreId()
        )->addFieldToFilter(
            'entity_id',
            ['in' => $productIds]
        )->addAttributeToSelect(
            'country_of_manufacture'
        );
        $products = $productCollection->getItems();

        foreach ($packageItems as $itemShipment) {
            $item = new \Magento\Framework\DataObject();
            $item->setData($itemShipment);
            $product = $products[$item->getProductId()];
            $unitWeight = $this->convertWeightToKg(
                $item->getWeight(),
                $this->getStoreWeightUnit($request->getStoreId())
            );

            $data = [
                'customs-number-of-units'   => $item->getQty(),
                'customs-description'       => substr(
                    $item->getName(),
                    0,
                    45
                ),
                'unit-weight'               => $unitWeight,
                'customs-value-per-unit'    => $this->convertPrice($item->getCustomsValue()),
                'sku'                       => substr(
                    $product->getSku(),
                    0,
                    15
                ),
            ];

            if ($product->getCountryOfManufacture()) {
                $data['country-of-origin'] = $product->getCountryOfManufacture();
            }

            $skuList[] = $data;
        }

        return $skuList;
    }

    /**
     * Correct parcel weight if sum of weight items greater
     *
     * @param $deliveryRequest
     * @return mixed
     */
    protected function correctTotalWeight($deliveryRequest)
    {
        $totalWeight = 0;
        if (!empty($deliveryRequest['customs']['sku-list']['item'])) {
            $items = $deliveryRequest['customs']['sku-list']['item'];
            foreach ($items as $item) {
                $totalWeight += $item['customs-number-of-units'] * $item['unit-weight'];
            }
            if ($totalWeight > $deliveryRequest['parcel-characteristics']['weight']) {
                $deliveryRequest['parcel-characteristics']['weight'] = $totalWeight;
            }
        }

        return $deliveryRequest;
    }

    /**
     * @param $shipmentId
     * @return int
     */
    public function getShipmentPrice($shipmentId)
    {
        try {
            $cost = 0;
            if ($this->_carrierHelper->isContractShipment()) {
                $client = $this->createSoapClient('shipment');
                $request = [
                    'get-shipment-price-request' => [
                        'mailed-by'     => $this->_carrierHelper->getMailedBy(),
                        'shipment-id'   => $shipmentId
                    ]
                ];
                $response = $client->__soapCall('GetShipmentPrice', $request, null, null);

                if (isset($response->{'shipment-price'})) {
                    $cost = $this->getShipmentCost($response->{'shipment-price'});
                }
            }

            return $cost;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * @param $price
     * @return mixed
     */
    private function getShipmentCost($price)
    {
        if ($this->_carrierHelper->getConfigCarrier('rates_price_type')) {
            $cost = $price->{'base-amount'};
        } else {
            $cost = $price->{'base-amount'};
            if (isset($price->{'adjustments'})) {
                foreach ($price->{'adjustments'}->{'adjustment'} as $adjustment) {
                    $cost += $adjustment->{'adjustment-amount'};
                }
            }
        }

        return $cost;
    }
}
