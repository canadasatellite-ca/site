<?php

namespace MW\Onestepcheckout\Model\System\Config\Source\Shipping;

class Allowedmethods implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @var \Magento\Shipping\Model\Config
	 */
	protected $_shippingConfig;

	/**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
	public function __construct(
		\Magento\Shipping\Model\Config $shippingConfig,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	) {
		$this->_shippingConfig = $shippingConfig;
		$this->_scopeConfig = $scopeConfig;
	}

	/**
     * Return array of carriers.
     * If $isActiveOnlyFlag is set to true, will return only active carriers
     *
     * @return array
     */
    public function toOptionArray()
    {
        $isActiveOnlyFlag = true;
		$methods = [
			['value' => '', 'label' => '']
		];
        $carriers = $this->_shippingConfig->getAllCarriers();

        foreach ($carriers as $carrierCode => $carrierModel) {
            if (!$carrierModel->isActive() && (bool)$isActiveOnlyFlag === true) {
                continue;
            }

            $carrierMethods = $carrierModel->getAllowedMethods();
            if (!$carrierMethods) {
                continue;
            }

            $carrierTitle = $this->_scopeConfig->getValue(
				'carriers/' . $carrierCode . '/title',
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			);
            $methods[$carrierCode] = [
                'label' => $carrierTitle,
                'value' => [],
            ];
            foreach ($carrierMethods as $methodCode => $methodTitle) {
                $methods[$carrierCode]['value'][] = [
                    'value' => $carrierCode . '_' . $methodCode,
                    'label' => '[' . $carrierCode . '] ' . $methodTitle,
                ];
            }
        }

        return $methods;
    }
}
