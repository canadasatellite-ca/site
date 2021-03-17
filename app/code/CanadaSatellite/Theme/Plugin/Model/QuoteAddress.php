<?php

namespace CanadaSatellite\Theme\Plugin\Model;

use Magento\Quote\Model\Quote\Address;

class QuoteAddress
{
    private $_carrierFactory;

    protected $_state;

    public function __construct(
        \Magento\Shipping\Model\CarrierFactoryInterface $carrierFactory,
        \Magento\Framework\App\State $state
    ){
        $this->_carrierFactory = $carrierFactory;
        $this->_state = $state;
    }

    public function aroundGetGroupedAllShippingRates(Address $subject, callable $proceed)
    {
        $rates = [];
        $areaCode = $this->_state->getAreaCode();
        foreach ($subject->getShippingRatesCollection() as $rate) {
            if (!$rate->isDeleted() && $this->_carrierFactory->get($rate->getCarrier())) {

                if ($rate->getPrice() != "0.0000" || $rate->getCarrier() == "freeshippingcustom" || $areaCode == 'adminhtml') {
                    if (!isset($rates[$rate->getCarrier()])) {
                        $rates[$rate->getCarrier()] = [];
                    }

                    $rates[$rate->getCarrier()][] = $rate;
                    $rates[$rate->getCarrier()][0]->carrier_sort_order = $this->_carrierFactory->get(
                        $rate->getCarrier()
                    )->getSortOrder();
                }

            }
        }
        uasort($rates, [$this, '_sortRates']);

        return $rates;
    }

    protected function _sortRates($firstItem, $secondItem)
    {
        if ((int)$firstItem[0]->carrier_sort_order < (int)$secondItem[0]->carrier_sort_order) {
            return -1;
        } elseif ((int)$firstItem[0]->carrier_sort_order > (int)$secondItem[0]->carrier_sort_order) {
            return 1;
        } else {
            return 0;
        }
    }
}
