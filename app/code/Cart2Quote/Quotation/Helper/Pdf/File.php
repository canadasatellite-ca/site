<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper\Pdf;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class File
 * @package Cart2Quote\Quotation\Helper\Pdf
 */
class File extends \Magento\Downloadable\Helper\File
{
    /**
     * Overwrite to make this point to the var directory
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * File constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Framework\Filesystem $filesystem
     * @param array $mimeTypes
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        array $mimeTypes = []
    ) {
        parent::__construct(
            $context,
            $coreFileStorageDatabase,
            $filesystem,
            $mimeTypes
        );
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }
}
