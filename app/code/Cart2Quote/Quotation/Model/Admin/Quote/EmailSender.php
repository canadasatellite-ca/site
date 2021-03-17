<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Admin\Quote;

/**
 * Class EmailSender
 */
class EmailSender
{
    use \Cart2Quote\Features\Traits\Model\Admin\Quote\EmailSender {
        send as private traitSend;
    }

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender
     */
    protected $quoteRequestSender;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $quoteRequestSender
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $quoteRequestSender
    ) {
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->quoteRequestSender = $quoteRequestSender;
    }

    /**
     * Send email about new quote.
     * - Process mail exception
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return bool
     */
    public function send(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        return $this->traitSend($quote);
    }
}
