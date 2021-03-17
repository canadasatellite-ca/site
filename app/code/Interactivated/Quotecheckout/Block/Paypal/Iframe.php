<?php

namespace Interactivated\Quotecheckout\Block\Paypal;

class Iframe extends \Magento\Paypal\Block\Iframe
{
	/**
	 * @var \Interactivated\Quotecheckout\Helper\Data
	 */
	protected $_dataHelper;

	/**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Paypal\Helper\Hss $hssHelper
     * @param \Interactivated\Quotecheckout\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Cart2Quote\Quotation\Model\Session $checkoutSession,
        \Magento\Paypal\Helper\Hss $hssHelper,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Module\Dir\Reader $reader,
        \Interactivated\Quotecheckout\Helper\Data $dataHelper,
        array $data = []
    ) {
    	$this->_dataHelper = $dataHelper;
        parent::__construct($context, $orderFactory, $checkoutSession, $hssHelper, $readFactory, $reader ,$data);
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $paymentCode = $this->_getCheckout()->getQuote()->getPayment()->getMethod();
        if (in_array($paymentCode, $this->_hssHelper->getHssMethods())) {
            $this->_paymentMethodCode = $paymentCode;
            $templatePath = str_replace('_', '', $paymentCode);

            if ($this->_dataHelper->getStoreConfig('onestepcheckout/general/enabled') == 1) {
            	$templateFile = "Interactivated_Quotecheckout::paypal/{$templatePath}/iframe.phtml";
            } else {
            	$templateFile = "{$templatePath}/iframe.phtml";
            }

            $directory = $this->_filesystem->getDirectoryRead(DirectoryList::MODULES);
            $file = $this->resolver->getTemplateFileName($templateFile, ['module' => 'Magento_Paypal']);
            if ($file && $directory->isExist($directory->getRelativePath($file))) {
                $this->setTemplate($templateFile);
            } else {
            	if ($this->_dataHelper->getStoreConfig('onestepcheckout/general/enabled') == 1) {
            		$this->setTemplate('Interactivated_Quotecheckout::paypal/hss/iframe.phtml');
            	} else {
            		$this->setTemplate('hss/iframe.phtml');
            	}
            }
        }
    }
}
