<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Currency;

class FixerIo extends \Magento\Directory\Model\Currency\Import\FixerIo
{
    /**
     * @var array
     */
    private $_currencyFrom = [];

    /**
     * @var array
     */
    private $_currencyTo = [];

    /**
     * @param $currencyFrom
     * @param $currencyTo
     * @return float|int|null
     */
    public function getCurrencyRate($currencyFrom, $currencyTo)
    {
        $rateExchange = null;
        try {
            $this->_currencyFrom = [$currencyFrom];
            $this->_currencyTo = [$currencyTo];
            $rates = $this->fetchRates();
            if (isset($rates[$currencyFrom][$currencyTo])) {
                $rateExchange = $rates[$currencyFrom][$currencyTo];
            }
        } catch (\Exception $e) {
            $rateExchange = null;
        }

        return $rateExchange;
    }

    /**
     * @return array
     */
    protected function _getCurrencyCodes()
    {
        return $this->_currencyTo;
    }

    /**
     * @return array
     */
    protected function _getDefaultCurrencyCodes()
    {
        return $this->_currencyFrom;
    }
}
