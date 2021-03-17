<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Email;

use Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteSenderInterface;

/**
 * Class Sender
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email
 */
abstract class AbstractSender implements QuoteSenderInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Email\AbstractSender {
        checkAndSend as private traitCheckAndSend;
        prepareTemplate as private traitPrepareTemplate;
        getTemplateOptions as private traitGetTemplateOptions;
        getSender as private traitGetSender;
        getFormattedShippingAddress as private traitGetFormattedShippingAddress;
        getFormattedBillingAddress as private traitGetFormattedBillingAddress;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Pdf\Quote
     */
    protected $_pdfModel;

    /**
     * @var \Magento\Sales\Model\Order\Email\SenderBuilderFactory
     */
    protected $senderBuilderFactory;

    /**
     * @var \Magento\Sales\Model\Order\Email\Container\Template
     */
    protected $templateContainer;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Container\IdentityInterface
     */
    protected $identityContainer;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * AbstractSender constructor
     *
     * @param \Magento\Sales\Model\Order\Email\Container\Template $templateContainer
     * @param \Magento\Sales\Model\Order\Email\Container\IdentityInterface $identityContainer
     * @param \Cart2Quote\Quotation\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer
     * @param \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel
     */
    public function __construct(
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Magento\Sales\Model\Order\Email\Container\IdentityInterface $identityContainer,
        \Cart2Quote\Quotation\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer,
        \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel
    ) {
        $this->templateContainer = $templateContainer;
        $this->identityContainer = $identityContainer;
        $this->senderBuilderFactory = $senderBuilderFactory;
        $this->logger = $logger;
        $this->addressRenderer = $addressRenderer;
        $this->_pdfModel = $pdfModel;
    }

    /**
     * Send the mail if this mail is enabled
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param array|null $attachments
     * @return bool
     */
    protected function checkAndSend(
        \Cart2Quote\Quotation\Model\Quote $quote,
        $attachments = null
    ) {
        return $this->traitCheckAndSend($quote, $attachments);
    }

    /**
     * Prepare template
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     */
    protected function prepareTemplate(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        $this->traitPrepareTemplate($quote);
    }

    /**
     * Get template options
     *
     * @return array
     */
    protected function getTemplateOptions()
    {
        return $this->traitGetTemplateOptions();
    }

    /**
     * Get sender object
     *
     * @return SenderBuilder|\Magento\Sales\Model\Order\Email\SenderBuilder
     */
    protected function getSender()
    {
        return $this->traitGetSender();
    }

    /**
     * Get the shipping address formated (html)
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return null|string
     */
    protected function getFormattedShippingAddress(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        return $this->traitGetFormattedShippingAddress($quote);
    }

    /**
     * Get the billing address formatted (html)
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return null|string
     */
    protected function getFormattedBillingAddress($quote)
    {
        return $this->traitGetFormattedBillingAddress($quote);
    }
}
