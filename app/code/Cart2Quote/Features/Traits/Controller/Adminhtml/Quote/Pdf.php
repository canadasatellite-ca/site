<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Controller\Adminhtml\Quote;
/**
 * Trait Pdf
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
trait Pdf
{
    /**
     * Download PDF for the quotation quote item
     */
    private function execute()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($results = parent::execute()) {
            return $results;
        }
        ini_set('zlib.output_compression', '0');
        $quote = $this->_initQuote();
        $filePath = $this->pdfModel->createQuotePdf([$quote]);
        $this->downloadHelper->setResource($filePath, \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE);
        $fileName = $this->downloadHelper->getFilename();
        $contentType = $this->downloadHelper->getContentType();
        //$contentDisposition = $this->_downloadHelper->getContentDisposition()
        $contentDisposition = 'attachment';
        $this->getResponse()->setHttpResponseCode(
            200
        )->setHeader(
            'target',
            '_blank',
            true
        )->setHeader(
            'Pragma',
            'public',
            true
        )->setHeader(
            'Cache-Control',
            'private, max-age=0, must-revalidate, post-check=0, pre-check=0',
            true
        )->setHeader(
            'Content-type',
            $contentType,
            true
        );
        if ($fileSize = $this->downloadHelper->getFileSize()) {
            $this->getResponse()->setHeader('Content-Length', $fileSize);
        }
        $this->getResponse()->setHeader('Content-Disposition', $contentDisposition . '; filename=' . $fileName);
        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();
        $this->downloadHelper->output();
		}
	}
    /**
     * ACL check
     *
     * @return bool
     */
    private function _isAllowed()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_authorization->isAllowed('Cart2Quote_Quotation::actions_view');
		}
	}
}
