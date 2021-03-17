<?php

namespace MW\Onestepcheckout\Model\Quote\Address\Total;

class Giftwrap extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
	/**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
	protected $_sessionManager;

	/**
	 * @var \Magento\Framework\Pricing\PriceCurrencyInterface
	 */
	protected $_priceCurrency;

	/**
	 * @var \MW\Onestepcheckout\Helper\Data
	 */
	protected $_dataHelper;

	/**
	 * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
	 * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
	 * @param \MW\Onestepcheckout\Helper\Data $dataHelper
	 */
	public function __construct(
		\Magento\Framework\Session\SessionManagerInterface $sessionManager,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
		\MW\Onestepcheckout\Helper\Data $dataHelper
	) {
		$this->_sessionManager = $sessionManager;
		$this->_priceCurrency = $priceCurrency;
		$this->_dataHelper = $dataHelper;
		$this->setCode('giftwrap_amount');
	}

	/**
	 * Collect giftwrap amount
	 *
	 * @param \Magento\Quote\Model\Quote $quote
	 * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
	 * @param \Magento\Quote\Model\Quote\Address\Total $total
	 * @return $this
	 */
	public function collect(
		\Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
	) {
		parent::collect($quote, $shippingAssignment, $total);

		$address = $shippingAssignment->getShipping()->getAddress();
		if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
			return $this;
		}

		$hasGiftwrap = $this->_sessionManager->getIsWrap();
		if (!$hasGiftwrap) {
			return $this;
		}

		$baseGiftwrapAmount = $this->_dataHelper->getStoreConfig('onestepcheckout/addfield/price_gift_wrap');
		$giftwrapAmount = $this->_priceCurrency->convert($baseGiftwrapAmount);

		$quote->setBaseGiftwrapAmount($baseGiftwrapAmount);
		$quote->setGiftwrapAmount($giftwrapAmount);

		$address->setBaseGiftwrapAmount($baseGiftwrapAmount);
		$address->setGiftwrapAmount($giftwrapAmount);
		$address->setBaseGrandTotal($address->getBaseGrandTotal() + $baseGiftwrapAmount);
		$address->setGrandTotal($address->getGrandTotal() + $giftwrapAmount);

		$totals = array_sum($total->getAllTotalAmounts());
        $baseTotals = array_sum($total->getAllBaseTotalAmounts());
        $total->setGrandTotal($totals + $giftwrapAmount);
        $total->setBaseGrandTotal($baseTotals + $baseGiftwrapAmount);

		return $this;
	}

	/**
     * Add giftwrap amount information to address
     *
     * @param   \Magento\Quote\Model\Quote $quote
     * @param   \Magento\Quote\Model\Quote\Address\Total $total
     * @return  array
     */
    public function fetch(
    	\Magento\Quote\Model\Quote $quote,
    	\Magento\Quote\Model\Quote\Address\Total $total
    ) {
    	$isWrap = $this->_sessionManager->getIsWrap();
        if (!$isWrap) {
            return [];
        }

        $amount = $quote->getGiftwrapAmount();
        if ($amount != 0) {
        	return [
	            'code' => $this->getCode(),
	            'title' => __('Gift Wrap'),
	            'value' => $amount
	        ];
        }

        return [];
    }
}
