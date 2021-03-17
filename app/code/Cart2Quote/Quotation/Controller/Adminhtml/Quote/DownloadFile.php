<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class DownloadFile
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class DownloadFile extends \Magento\Backend\App\Action
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\File
     */
    protected $fileModel;

    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $urlDecoder;

    /**
     * DownloadFile constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Cart2Quote\Quotation\Model\Quote\File $fileModel
     * @param \Magento\Framework\Url\DecoderInterface $urlDecoder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Cart2Quote\Quotation\Model\Quote\File $fileModel,
        \Magento\Framework\Url\DecoderInterface $urlDecoder
    ) {
        $this->fileModel = $fileModel;
        $this->urlDecoder = $urlDecoder;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $fileName = $this->getRequest()->getParam('file');
            $fileName = $this->urlDecoder->decode($fileName);

            //add and replace the quotation folder to make sure we are in that folder
            $fileName = str_replace('quotation' . DIRECTORY_SEPARATOR, '', $fileName);
            $filePath = 'quotation' . DIRECTORY_SEPARATOR . $fileName;

            $this->fileModel->downloadFile($filePath);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }
}
