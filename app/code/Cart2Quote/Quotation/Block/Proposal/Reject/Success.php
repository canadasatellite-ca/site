<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Proposal\Reject;

use Magento\Customer\Model\Context;

/**
 * Class Success
 *
 * @package Cart2Quote\Quotation\Block\Proposal\Reject
 */
class Success extends \Cart2Quote\Quotation\Block\Proposal\Reject\View
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
     */
    protected $quotationQuoteCollection;

    /**
     * @var \Cart2Quote\Quotation\Block\Proposal\Reject\View $rejectView
     */
    protected $rejectView;

    /**
     * Success constructor
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quotationQuoteCollection,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_isScopePrivate = true;
        parent::__construct(
            $quotationQuoteCollection,
            $context,
            $data
        );
    }

    /**
     * @return string
     */
    public function getReturnToStoreUrl()
    {
        return $this->getUrl('');
    }
}
