<?php

namespace Interactivated\Quotecheckout\Model\System\Config\Source\Payment;

class Allowedmethods implements \Magento\Framework\Option\ArrayInterface 
{
	/**
	 * @var \Magento\Payment\Model\Config
	 */
	protected $_paymentConfig;

	/**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
	public function __construct(
		\Magento\Payment\Model\Config $paymentConfig,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	) {
		$this->_paymentConfig = $paymentConfig;
		$this->_scopeConfig = $scopeConfig;
	}

    public function toOptionArray()
    {
        $methods = [
        	['value'=>'', 'label'=>'']
        ];
        $payments = $this->_paymentConfig->getActiveMethods();

        foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = $this->_scopeConfig->getValue(
				'payment/' . $paymentCode . '/title',
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			);
            $methods[$paymentCode] = [
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            ];
        }

        return $methods;
    }
}
