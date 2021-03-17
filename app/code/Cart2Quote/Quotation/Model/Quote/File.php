<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote;

use \Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

/**
 * Class File
 * @package Cart2Quote\Quotation\Model\Quote
 */
class File
{
    use \Cart2Quote\Features\Traits\Model\Quote\File {
        uploadFiles as private traitUploadFiles;
        removeFile as private traitRemoveFile;
        setImageDataToSession as private traitSetImageDataToSession;
        getFileDataFromSession as private traitGetFileDataFromSession;
        saveFileQuotationQuote as private traitSaveFileQuotationQuote;
        getFileDataFromQuotation as private traitGetFileDataFromQuotation;
        downloadFile as private traitDownloadFile;
    }

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    private $quoteSession;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileDriver;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $backendSessionQuote;

    /**
     * @var Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * @var \Cart2Quote\Quotation\Helper\FileUpload
     */
    private $fileUploadHelper;

    /**
     * Download helper
     *
     * @var \Magento\Downloadable\Helper\Download
     */
    protected $downloadHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * File constructor.
     *
     * @param \Magento\Downloadable\Helper\Download $downloadHelper
     * @param \Cart2Quote\Quotation\Helper\FileUpload $fileUploadHelper
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Backend\Model\Session\Quote $backendSessionQuote
     * @param \Magento\Framework\Filesystem\Io\File $io
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Downloadable\Helper\Download $downloadHelper,
        \Cart2Quote\Quotation\Helper\FileUpload $fileUploadHelper,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backend\Model\Session\Quote $backendSessionQuote,
        \Magento\Framework\Filesystem\Io\File $io,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->downloadHelper = $downloadHelper;
        $this->fileUploadHelper = $fileUploadHelper;
        $this->fileFactory = $fileFactory;
        $this->backendSessionQuote = $backendSessionQuote;
        $this->ioFile = $io;
        $this->fileDriver = $fileDriver;
        $this->quoteSession = $quoteSession;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->request = $request;
    }

    /**
     * @param $fileAmount
     * @return array
     * @throws \Exception
     */
    public function uploadFiles($fileAmount)
    {
        return $this->traitUploadFiles($fileAmount);
    }

    /**
     * @param string $fileName
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function removeFile($fileName)
    {
        $this->traitRemoveFile($fileName);
    }

    /**
     * @param array $imageData
     */
    public function setImageDataToSession($imageData)
    {
        $this->traitSetImageDataToSession($imageData);
    }

    /**
     * @return array|null
     */
    public function getFileDataFromSession()
    {
        return $this->traitGetFileDataFromSession();
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quotationQuote
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function saveFileQuotationQuote($quotationQuote)
    {
        $this->traitSaveFileQuotationQuote($quotationQuote);
    }

    /**
     * @return array
     */
    public function getFileDataFromQuotation()
    {
        return $this->traitGetFileDataFromQuotation();
    }

    /**
     * @param string $fileName
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     */
    public function downloadFile($fileName)
    {
        return $this->traitDownloadFile($fileName);
    }
}
