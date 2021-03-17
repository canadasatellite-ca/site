<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Email\Container;
/**
 * Trait QuoteProposalAcceptedIdentity
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Container
 */
trait QuoteProposalAcceptedIdentity
{
    /**
     * Get reciever email
     *
     * @return string
     */
    private function getRecieverEmail()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$emailIdentity = $this->senderResolver->resolve($this->getEmailIdentity());
        return $emailIdentity['email'];
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
			$emailIdentity = $this->senderResolver->resolve($this->getEmailIdentity());
        return $emailIdentity['name'];
		}
	}
}
