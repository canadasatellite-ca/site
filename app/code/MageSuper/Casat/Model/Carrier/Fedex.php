<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace MageSuper\Casat\Model\Carrier;

use Magento\Framework\Module\Dir;
use Magento\Framework\Xml\Security;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Tracking\Result as TrackingResult;

/**
 * Fedex shipping implementation
 *
 * @author Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Fedex extends \Magento\Fedex\Model\Carrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    public $multiplier;

    function setRequest(RateRequest $request)
    {
        $this->_request = $request;

        $r = new \Magento\Framework\DataObject();

        if ($request->getLimitMethod()) {
            $r->setService($request->getLimitMethod());
        }

        if ($request->getFedexAccount()) {
            $account = $request->getFedexAccount();
        } else {
            $account = $this->getConfigData('account');
        }
        $r->setAccount($account);

        if ($request->getFedexDropoff()) {
            $dropoff = $request->getFedexDropoff();
        } else {
            $dropoff = $this->getConfigData('dropoff');
        }
        $r->setDropoffType($dropoff);

        if ($request->getFedexPackaging()) {
            $packaging = $request->getFedexPackaging();
        } else {
            $packaging = $this->getConfigData('packaging');
        }
        $r->setPackaging($packaging);

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
        } else {
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

        $r->setMeterNumber($this->getConfigData('meter_number'));
        $r->setKey($this->getConfigData('key'));
        $r->setPassword($this->getConfigData('password'));

        $r->setIsReturn($request->getIsReturn());

        $r->setBaseSubtotalInclTax($request->getBaseSubtotalInclTax());

        $RequestedPackageLineItems = array();
        $PackageCount = 0;

        $max_l = $max_w = $max_h = 0;
        $sum_h = 0;
        foreach ($request->getAllItems() as $item) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $item->getProduct();
            $data = $product->getResource()->getAttributeRawValue($product->getEntityId(), array('shipping_length', 'shipping_width', 'shipping_height'), $item->getQuote()->getStoreId());
            if (isset($data['shipping_length']) && $data['shipping_length']) {
                if ($max_l < $data['shipping_length']) {
                    $max_l = (float)$data['shipping_length'];
                }
            }
            if (isset($data['shipping_width']) && $data['shipping_width']) {
                if ($max_w < $data['shipping_width']) {
                    $max_w = (float)$data['shipping_width'];
                }
            }
            if (isset($data['shipping_height']) && $data['shipping_height']) {
                if ($max_h < $data['shipping_height']) {
                    $max_h = (float)$data['shipping_height'];
                }
            }
            if (isset($data['shipping_height']) && $data['shipping_height']) {
                $sum_h += ($data['shipping_height'] * $item->getQty());
            }
        }
        $obem = $max_l * $max_w * $sum_h;
        $h = $sum_h;
        $weight = $r->getWeight();
        if ($obem > 250000) {
            $this->multiplier = ceil($sum_h/$max_h);
            $weight = $weight/$this->multiplier;
            $h = $max_h;
        }

        $tmp = [
            'Weight' => [
                'Value' => (double)$weight,
                'Units' => $this->getConfigData('unit_of_measure'),
            ],
            'GroupPackageCount' => 1
        ];
        if($max_l && $max_w && $sum_h){
            $tmp['Dimensions'] = [
                'Length' => $max_l,
                'Width'  => $max_w,
                'Height' => $h,
                'Units'  => 'CM'
            ];
        }

        $RequestedPackageLineItems[] = $tmp;
        $r->setData('PackageCount', $PackageCount);
        $r->setData('RequestedPackageLineItems', $RequestedPackageLineItems);

        $this->setRawRequest($r);

        return $this;
    }

    /**
     * Forming request for rate estimation depending to the purpose
     *
     * @param string $purpose
     * @return array
     */
    protected function _formRateRequest($purpose)
    {
        $r = $this->_rawRequest;
        $ratesRequest = [
            'WebAuthenticationDetail' => [
                'UserCredential' => ['Key' => $r->getKey(), 'Password' => $r->getPassword()],
            ],
            'ClientDetail' => ['AccountNumber' => $r->getAccount(), 'MeterNumber' => $r->getMeterNumber()],
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => [
                'DropoffType' => $r->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $r->getPackaging(),
                'TotalInsuredValue' => ['Amount' => $r->getValue(), 'Currency' => $this->getCurrencyCode()],
                'Shipper' => [
                    'Address' => ['PostalCode' => $r->getOrigPostal(), 'CountryCode' => $r->getOrigCountry()],
                ],
                'Recipient' => [
                    'Address' => [
                        'PostalCode' => $r->getDestPostal(),
                        'CountryCode' => $r->getDestCountry(),
                        'Residential' => (bool)$this->getConfigData('residence_delivery'),
                    ],
                ],
                'ShippingChargesPayment' => [
                    'PaymentType' => 'SENDER',
                    'Payor' => ['AccountNumber' => $r->getAccount(), 'CountryCode' => $r->getOrigCountry()],
                ],
                'CustomsClearanceDetail' => [
                    'CustomsValue' => ['Amount' => $r->getValue(), 'Currency' => $this->getCurrencyCode()],
                ],
                'RateRequestTypes' => 'LIST',
                'PackageCount' => '1',
                'PackageDetail' => 'INDIVIDUAL_PACKAGES',
                'RequestedPackageLineItems' => $r->getData('RequestedPackageLineItems'),
            ],
        ];

        if ($r->getDestCity()) {
            $ratesRequest['RequestedShipment']['Recipient']['Address']['City'] = $r->getDestCity();
        }

        if ($purpose == self::RATE_REQUEST_GENERAL) {
            $InsuredValue = $r->getValue();
            if($InsuredValue>100){
                $InsuredValue = 100;
            }
            $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][0]['InsuredValue'] = [
                'Amount' => $InsuredValue,
                'Currency' => $this->getCurrencyCode(),
            ];
        } else {
            if ($purpose == self::RATE_REQUEST_SMARTPOST) {
                $ratesRequest['RequestedShipment']['ServiceType'] = self::RATE_REQUEST_SMARTPOST;
                $ratesRequest['RequestedShipment']['SmartPostDetail'] = [
                    'Indicia' => (double)$r->getWeight() >= 1 ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                    'HubId' => $this->getConfigData('smartpost_hubid'),
                ];
            }
        }

        return $ratesRequest;
    }

    function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates()) {
            return $this->getErrorMessage();
        }

        $this->setRequest($request);
        $this->_getQuotes();
        $this->_updateFreeMethodQuote($request);

        $this->multiplyRates();
        return $this->getResult();
    }
    function multiplyRates()
    {
        if ($this->multiplier > 1) {
            $result = $this->getResult();
            $rates = $result->getAllRates();
            foreach ($rates as $rate) {
                $price = $rate->getPrice();
                $rate->setPrice($price*$this->multiplier);
                $cost = $rate->getCost();
                $rate->setCost($cost*$this->multiplier);
            }
        }
    }
}
