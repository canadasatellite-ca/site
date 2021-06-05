<?php
namespace Interactivated\Price\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper{
	protected $priceCurrency;
	  /**
	   * @param \Magento\Framework\App\Helper\Context $context
	   * @param \Magento\Customer\Model\Address\Config $addressConfig
	   * @param \Magento\Directory\Model\Region $region
	   * @param \Magento\Framework\Stdlib\DateTime $dateFormat
	   * @param PaymentConfig $paymentConfig
	   * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
	   * @param \Magedelight\Firstdata\Model\Config $firstdataConfig
	   */
	  function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
	) {
		  $this->priceCurrency = $priceCurrency;
		  parent::__construct($context);
	  }

	function format($price, $currency) {return $this->priceCurrency->format($price, false, 2, null, $currency);}
}
