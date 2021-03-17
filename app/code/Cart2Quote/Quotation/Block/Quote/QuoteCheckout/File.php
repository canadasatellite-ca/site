<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\QuoteCheckout;

use Magento\Framework\View\Element\Template;

/**
 * Class File
 * @package Cart2Quote\Quotation\Block\Quote\QuoteCheckout
 */
class File extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $fileSystem;

    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    private $quoteSession;

    /**
     * File constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        array $data
    ) {
        $this->quoteSession = $quoteSession;
        $this->fileSystem = $context->getFilesystem();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getPostUrl()
    {
        return $this->_urlBuilder->getUrl('quotation/fileupload/upload');
    }

    /**
     * @return string
     */
    public function getRemoveUrl()
    {
        return $this->_urlBuilder->getUrl('quotation/fileupload/remove');
    }

    /**
     * @return string
     */
    public function getUploadedFiles()
    {
        return $this->quoteSession->getUploadedFile();
    }
}
