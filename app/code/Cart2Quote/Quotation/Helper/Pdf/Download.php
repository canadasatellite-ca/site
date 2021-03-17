<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper\Pdf;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException as CoreException;

/**
 * Class Download
 *
 * @package Cart2Quote\Quotation\Helper\Pdf
 */
class Download extends \Magento\Downloadable\Helper\Download
{
    /**
     * Downloadable file
     *
     * @var \Cart2Quote\Quotation\Helper\Pdf\File
     */
    protected $_downloadableFile;

    /**
     * Download constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Cart2Quote\Quotation\Helper\Pdf\File $downloadableFile
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param \Magento\Framework\Filesystem\File\ReadFactory $fileReadFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Cart2Quote\Quotation\Helper\Pdf\File $downloadableFile,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\Filesystem\File\ReadFactory $fileReadFactory
    ) {
        parent::__construct(
            $context,
            $downloadableFile,
            $coreFileStorageDb,
            $filesystem,
            $session,
            $fileReadFactory
        );
    }

    /**
     * Retrieve Resource file handle (socket, file pointer etc)
     *
     * @return \Magento\Framework\Filesystem\File\ReadInterface
     * @throws CoreException|\Exception
     */
    protected function _getHandle()
    {
        if (!$this->_resourceFile) {
            throw new CoreException(__('Please set resource file and link type.'));
        }

        if ($this->_handle === null) {
            if ($this->_linkType == self::LINK_TYPE_URL) {
                $path = $this->_resourceFile;
                $protocol = strtolower(parse_url($path, PHP_URL_SCHEME));
                if ($protocol) {
                    // Strip down protocol from path
                    $path = preg_replace('#.+://#', '', $path);
                }
                $this->_handle = $this->fileReadFactory->create($path, $protocol);
            } elseif ($this->_linkType == self::LINK_TYPE_FILE) {
                $this->_workingDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
                $fileExists = $this->_downloadableFile->ensureFileInFilesystem($this->_resourceFile);
                if ($fileExists) {
                    $this->_handle = $this->_workingDirectory->openFile($this->_resourceFile);
                } else {
                    throw new CoreException(__('Invalid download link type.'));
                }
            } else {
                throw new CoreException(__('Invalid download link type.'));
            }
        }

        return $this->_handle;
    }
}
