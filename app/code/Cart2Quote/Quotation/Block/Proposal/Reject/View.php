<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Proposal\Reject;

use Magento\Customer\Model\Context;

/**
 * Class View
 *
 * @package Cart2Quote\Quotation\Block\Proposal\Reject
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quotationQuoteCollection
     */
    protected $quotationQuoteCollection;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var string
     */
    protected $_template = 'quote/view.phtml';

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * View constructor.
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quotationQuoteCollection
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quotationQuoteCollection,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->request = $context->getRequest();
        $this->quotationQuoteCollection = $quotationQuoteCollection;
        $this->_isScopePrivate = true;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->quotationQuoteCollection->getQuote($this->request->getParam('quote_id'));
    }

    /**
     *
     * @return string
     */
    public function getRejectUrl()
    {
        return $this->getUrl(
            'quotation/proposal',
            ['quote_id' => $this->request->getParam('quote_id')]
        );
    }
}
