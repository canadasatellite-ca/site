<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class CurrencyUtils
{
	/**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    private $currencyFactory;

    private $logger;

    function __construct(
    	\Magento\Directory\Model\CurrencyFactory $currencyFactory,
    	\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
    ) {
    	$this->currencyFactory = $currencyFactory;
    	$this->logger = $logger;
    }

	function convert($from, $to, $value)
	{
		if ($from === null) {
			$from = 'CAD';
		}

		if ($to === null) {
			$to = 'CAD';
		}

		if ($from == $to) {
			return $value;
		}

		$rate = $this->currencyFactory->create()->load($from)->getAnyRate($to);
		$this->logger->info("Rate: $rate");
        return $value * $rate;
	}
}
