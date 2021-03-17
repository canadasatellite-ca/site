<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Email\Sender;

/**
 * Class QuoteProposalSender
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Sender
 */
class QuoteProposalSender extends Sender
{
    use \Cart2Quote\Features\Traits\Model\Quote\Email\Sender\QuoteProposalSender {
        checkAndSend as private traitCheckAndSend;
        createFilePath as private traitCreateFilePath;
        createDocumentFilePath as private traitCreateDocumentFilePath;
        getAttachPdf as private traitGetAttachPdf;
        getAttachDocument as private traitGetAttachDocument;
        getAttachDocumentName as private traitGetAttachDocumentName;
    }

    /**
     * Path to attach_proposal_pdf in system.xml
     */
    const ATTACH_PROPOSAL_PDF = 'cart2quote_email/quote_proposal/attach_proposal_pdf';

    /**
     * Path to attach_proposal_doc in system.xml
     */
    const ATTACH_PROPOSAL_ATTACHMENT = 'cart2quote_email/quote_proposal/attach_proposal_doc';

    /**
     * Path to attach_proposal_name in system.xml
     */
    const ATTACH_PROPOSAL_NAME = 'cart2quote_email/quote_proposal/attach_proposal_name';

    /**
     * Folder structure for uploading email attachment
     */
    const QUOTATION_EMAIL_FOLDER = '/quotation/email/';

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * QuoteProposalSender constructor
     *
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Sales\Model\Order\Email\Container\Template $templateContainer
     * @param \Magento\Sales\Model\Order\Email\Container\Container $identityContainer
     * @param \Cart2Quote\Quotation\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
     * @param \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel
     * @param string $sendEmailIdentifier
     * @param string $emailSentIdentifier
     */
    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Magento\Sales\Model\Order\Email\Container\Container $identityContainer,
        \Cart2Quote\Quotation\Model\Quote\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel,
        $sendEmailIdentifier = \Cart2Quote\Quotation\Api\Data\QuoteInterface::SEND_PROPOSAL_EMAIL,
        $emailSentIdentifier = \Cart2Quote\Quotation\Api\Data\QuoteInterface::PROPOSAL_EMAIL_SENT
    ) {
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $eventManager,
            $globalConfig,
            $pdfModel,
            $sendEmailIdentifier,
            $emailSentIdentifier
        );
        $this->directoryList = $directoryList;
    }

    /**
     * Check and send quote proposal email
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
     * Create complete file path (for PDF)
     *
     * @param string $filePath
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function createFilePath($filePath)
    {
        return $this->traitCreateFilePath($filePath);
    }

    /**
     * Create complete file path (for documents)
     *
     * @param string $filePath
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function createDocumentFilePath($filePath)
    {
        return $this->traitCreateDocumentFilePath($filePath);
    }

    /**
     * Get attach pdf configuration setting
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return bool
     */
    protected function getAttachPdf($quote)
    {
        return $this->traitGetAttachPdf($quote);
    }

    /**
     * Get attached document
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return string|null
     */
    protected function getAttachDocument($quote)
    {
        return $this->traitGetAttachDocument($quote);
    }

    /**
     * Get attachment name
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return string
     */
    protected function getAttachDocumentName($quote)
    {
        return $this->traitGetAttachDocumentName($quote);
    }
}
