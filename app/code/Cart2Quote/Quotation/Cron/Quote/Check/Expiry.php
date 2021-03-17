<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Cron\Quote\Check;

/**
 * Class Expiry
 *
 * @package Cart2Quote\Quotation\Cron\Quote\Check
 */
class Expiry
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalExpireSender
     */
    protected $proposalExpireSender;

    /**
     * Expiry constructor.
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalExpireSender $proposalExpireSender
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalExpireSender $proposalExpireSender
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->proposalExpireSender = $proposalExpireSender;
    }

    /**
     * Check for expired quotes
     */
    public function execute()
    {
        $availableStatus = [
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_SENT
        ];

        $quotes = $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('expiry_enabled', ['eq' => 1])
            ->addFieldToFilter('is_quote', ['eq' => \Cart2Quote\Quotation\Model\Quote::IS_QUOTE])
            ->addFieldToFilter('status', ['in' => $availableStatus])
            ->setOrder('created_at', 'desc');

        /**
         * @var \Cart2Quote\Quotation\Model\Quote $quote
         */
        foreach ($quotes as $quote) {
            $expiryDate = $quote->getExpiryDate();
            if ($expiryDate !== null) {
                if ($expiryDate == date('Y-m-d') && !$quote->getProposalExpiredEmailSent()) {
                    //Proposal expires today
                    $this->proposalExpireSender->send($quote);
                } else {
                    if ($expiryDate <= date('Y-m-d')) {
                        $quote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_PENDING);
                        $quote->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_EXPIRED);
                        $quote->save();
                    }
                }
            }
        }
    }
}
