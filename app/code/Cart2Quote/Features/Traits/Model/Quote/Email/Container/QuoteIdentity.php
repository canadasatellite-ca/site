<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Email\Container;
/**
 * Trait QuoteIdentity
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Container
 */
trait QuoteIdentity
{
    /**
     * Return guest template id
     *
     * @return mixed
     */
    private function getGuestTemplateId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $this->getStore()->getStoreId());
		}
	}
}
