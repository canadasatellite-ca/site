<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Email\Sender;
/**
 * Trait QuoteProposalSender
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Sender
 */
trait QuoteProposalSender
{
    /**
     * Check and send quote proposal email
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param array|null $attachments
     * @return bool
     */
    private function checkAndSend(
        \Cart2Quote\Quotation\Model\Quote $quote,
        $attachments = null
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->getAttachPdf($quote)) {
            $filePath = $this->_pdfModel->createQuotePdf([$quote]);
            if (isset($filePath)) {
                $attachmentName = $quote->getIncrementId() . '.pdf';
                $attachments[$attachmentName] = $this->createFilePath($filePath);
                $quote->setAttachPdf(true);
            }
        }
        if ($file = $this->getAttachDocument($quote)) {
            $filePath = self::QUOTATION_EMAIL_FOLDER . $file;
            $attachmentName = $this->getAttachDocumentName($quote);
            $attachments[$attachmentName] = $this->createDocumentFilePath($filePath);
            $quote->setAttachDoc(true);
        }
        return parent::checkAndSend($quote, $attachments);
		}
	}
    /**
     * Create complete file path (for PDF)
     *
     * @param string $filePath
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function createFilePath($filePath)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
            . DIRECTORY_SEPARATOR
            . $filePath;
		}
	}
    /**
     * Create complete file path (for documents)
     *
     * @param string $filePath
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function createDocumentFilePath($filePath)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            . DIRECTORY_SEPARATOR
            . $filePath;
		}
	}
    /**
     * Get attach pdf configuration setting
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return bool
     */
    private function getAttachPdf($quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->globalConfig->getValue(
            self::ATTACH_PROPOSAL_PDF,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $quote->getStoreId()
        );
		}
	}
    /**
     * Get attached document
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return string|null
     */
    private function getAttachDocument($quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->globalConfig->getValue(
            self::ATTACH_PROPOSAL_ATTACHMENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $quote->getStoreId()
        );
		}
	}
    /**
     * Get attachment name
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return string
     */
    private function getAttachDocumentName($quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$name = $this->globalConfig->getValue(
            self::ATTACH_PROPOSAL_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $quote->getStoreId()
        );
        if (isset($name)) {
            return $name;
        }
        $document = $this->getAttachDocument($quote);
        $fileName = substr($document, strrpos($document, DIRECTORY_SEPARATOR) + 1);
        return $fileName;
		}
	}
}
