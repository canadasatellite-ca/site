<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Proposal;

/**
 * Class Reject
 * @package Cart2Quote\Quotation\Block\Proposal
 */
class Reject extends \Cart2Quote\Quotation\Block\Quote\View
{
    /**
     * @return bool
     */
    public function getIsRejectProposalEnabled()
    {
        return (bool)$this->quotationHelper->isRejectProposalEnabled();
    }

    /**
     * @return string
     */
    public function getRejectUrl()
    {
        return $this->getUrl('quotation/proposal/view', ['quote_id' => $this->getQuote()->getEntityId()]);
    }
}
