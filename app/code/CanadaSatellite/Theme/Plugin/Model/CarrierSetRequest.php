<?php

namespace CanadaSatellite\Theme\Plugin\Model;

use Mageside\CanadaPostShipping\Model\Carrier;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Checkout\Model\Session as CheckoutSession;

class CarrierSetRequest
{
    /**
     * Rate request data
     *
     * @var RateRequest|null
     */
    protected $_request = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /** @var CheckoutSession */
    protected $checkoutSession;

    function __construct(
        CheckoutSession $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\CountryFactory $countryFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_countryFactory = $countryFactory;
    }

    function aroundSetRequest(
        Carrier $subject,
        callable $proceed,
        RateRequest $request
    ) {
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
            $destCountry = Carrier::USA_COUNTRY_ID;
        }
        $r->setDestCountry($this->_countryFactory->create()->load($destCountry)->getData('iso2_code'));
        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        }
        if ($request->getDestCity()) {
            $r->setDestCity($request->getDestCity());
        }
        $weight = $subject->getTotalNumOfBoxes($request->getPackageWeight());
        $r->setWeight($weight);
        if ($request->getFreeMethodWeight() != $request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }
        $r->setValue($request->getPackagePhysicalValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());
        $r->setIsReturn($request->getIsReturn());
        $r->setBaseSubtotalExclTax($request->getPackageValue());
        $r->setBaseSubtotalInclTax($request->getBaseSubtotalInclTax());

        /** @var \Magento\Quote\Model\Quote  */
        $quote = $this->checkoutSession->getQuote();
        $address = $quote->getShippingAddress();
        if ($officeId = $address->getData('canada_dpo_id')) {
            $r->setCanadaPostOfficeId($officeId);
        }

        $subject->setRawRequest($r);

        return $subject;
    }

}
