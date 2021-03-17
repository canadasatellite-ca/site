<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Address;
/**
 * Trait Renderer
 */
trait Renderer
{
    /**
     * Format quote address like magento formats the order addresses
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param string $type
     * @return null|string
     */
    private function formatQuoteAddress(\Magento\Quote\Model\Quote\Address $address, $type)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->addressConfig->setStore($address->getQuote()->getStoreId());
        $formatType = $this->addressConfig->getFormatByCode($type);
        if (!$formatType || !$formatType->getRenderer()) {
            return null;
        }
        $this->eventManager->dispatch('customer_address_format', ['type' => $formatType, 'address' => $address]);
        return $formatType->getRenderer()->renderArray($address->getData());
		}
	}
}
