<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Quote\Model;

/**
 * Class Address
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Quote\Model
 */
class Address
{
    /**
     * Quotation Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;

    /**
     * Address constructor
     *
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $quoteSession
    ) {
        $this->quoteSession = $quoteSession;
    }

    /**
     * Unset the unwanted shipping rates if the quotation shipping rate is selected
     *
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param array $result
     * @return array
     */
    public function afterGetGroupedAllShippingRates($subject, $result)
    {
        $sessionConfigData = $this->getSessionQuoteConfigData($subject->getQuoteId());
        if ($this->hasFixedShipping($sessionConfigData) &&
            isset($result[\Cart2Quote\Quotation\Model\Carrier\QuotationShipping::CODE])) {
            return [
                \Cart2Quote\Quotation\Model\Carrier\QuotationShipping::CODE =>
                    $result[\Cart2Quote\Quotation\Model\Carrier\QuotationShipping::CODE]
            ];
        }

        return $result;
    }

    /**
     * Get quote data from the session
     *
     * @param int $quoteId
     * @return array
     */
    protected function getSessionQuoteConfigData($quoteId)
    {
        $data = [];
        $configData = $this->quoteSession->getData(\Cart2Quote\Quotation\Model\Session::QUOTATION_STORE_CONFIG_DATA);
        if (isset($configData[$quoteId])) {
            $data = $configData[$quoteId];
        }

        return $data;
    }

    /**
     * Has fixed shipping price
     *
     * @param array $configData
     * @return bool
     */
    protected function hasFixedShipping($configData)
    {
        return isset($configData['fixed_shipping_price']);
    }
}
