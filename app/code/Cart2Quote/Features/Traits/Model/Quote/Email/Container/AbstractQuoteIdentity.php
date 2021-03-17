<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Email\Container;
/**
 * Trait AbstractQuoteIdentity
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Container
 */
trait AbstractQuoteIdentity
{
    /**
     * Get is enabled setting
     *
     * @return bool
     */
    private function isEnabled()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->scopeConfig->isSetFlag(
            $this::XML_PATH_EMAIL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
		}
	}
    /**
     * Return email copy_to list
     *
     * @return array|bool
     */
    private function getEmailCopyTo()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$data = $this->getConfigValue($this::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
		}
	}
    /**
     * Return copy method
     *
     * @return mixed
     */
    private function getCopyMethod()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getConfigValue($this::XML_PATH_EMAIL_COPY_METHOD, $this->getStore()->getStoreId());
		}
	}
    /**
     * Return template id
     *
     * @return mixed
     */
    private function getTemplateId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getConfigValue($this::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
		}
	}
    /**
     * Return email identity
     *
     * @return mixed
     */
    private function getEmailIdentity()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getConfigValue($this::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
		}
	}
    /**
     * Return template id
     *
     * @return mixed
     */
    private function getGuestTemplateId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $this->getStore()->getStoreId());
		}
	}
    /**
     * Get reciever email address
     *
     * @return string
     */
    private function getRecieverEmail()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getCustomerEmail();
		}
	}
    /**
     * Get reciever name
     *
     * @return string
     */
    private function getRecieverName()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getCustomerName();
		}
	}
    /**
     * Salesrep placeholder
     *
     * @return bool
     */
    private function isSendCopyToSalesRep()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return false;
		}
	}
}
