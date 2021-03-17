<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
use Magento\Framework\App\ObjectManager;
/**
 * Trait QuotationConfigProvider
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
trait QuotationConfigProvider
{
    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    private function getConfig()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->session->addConfigData([]);
        $this->prepareAddressConfig();
        $resultConfig = array_merge(
            $this->prepareDataField(\Cart2Quote\Quotation\Model\Session::QUOTATION_GUEST_FIELD_DATA),
            $this->prepareDataField(\Cart2Quote\Quotation\Model\Session::QUOTATION_FIELD_DATA),
            $this->prepareDataField(\Cart2Quote\Quotation\Model\Session::QUOTATION_PRODUCT_DATA),
            $this->prepareDataField(\Cart2Quote\Quotation\Model\Session::QUOTATION_STORE_CONFIG_DATA)
        );
        //Magento 2.3.4 support
        if (class_exists(\Vertex\AddressValidation\Model\Checkout\ConfigProvider::class)) {
            /** @var \Vertex\AddressValidation\Model\Checkout\ConfigProvider $vertexConfigProvider */
            $vertexConfigProvider = ObjectManager::getInstance()->get(
                \Vertex\AddressValidation\Model\Checkout\ConfigProvider::class
            );
            $vertexConfig = $vertexConfigProvider->getConfig();
            $resultConfig = array_merge($resultConfig, $vertexConfig);
        }
        //disable checkout agreement on quote checkout
        $resultConfig['checkoutAgreements'] = ['isEnabled' => false];
        return $resultConfig;
		}
	}
    /**
     * Add config fields regarding the shipping and billing configuration
     *
     * @return void
     */
    private function prepareAddressConfig()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$allowGuestConfig = $this->helper->getAllowGuestConfig();
        $config = [
            'allowGuest' => $allowGuestConfig,
            'allowForm' => $this->helper->getEnableForm(),
            'displayShipping' => $this->helper->getDisplayShipping(),
            'registeredQuoteCheckout' => $this->helper->getRegisteredQuoteCheckoutConfig(),
            'displaySwitcher' => $this->helper->getDisplaySwitcher()
        ];
        $this->session->setQuotationStoreConfigData($config);
		}
	}
    /**
     * Prepare the session data field
     *
     * @param string $fieldName
     * @return array
     */
    private function prepareDataField($fieldName)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$output[$fieldName] = [];
        if (is_array($this->session->getData($fieldName))) {
            $output[$fieldName] = $this->session->getData($fieldName);
        }
        return $output;
		}
	}
}
