<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Admin\Quote;
trait QuoteCreator
{
    /**
     * @return \Magento\Framework\Phrase
     */
    private function getQuoteCreator(){
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$userFullName = $this->authSession->getUser()->getName();
        $userRole = $this->authSession->getUser()->getRole()->getRoleName();
        $createdBy = __("%1: %2", $userRole, $userFullName);
        return $createdBy;
		}
	}
}
