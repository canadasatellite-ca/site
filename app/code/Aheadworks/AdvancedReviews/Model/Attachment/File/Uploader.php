<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Attachment\File;

use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Class FileUploader
 * @package Aheadworks\AdvancedReviews\Model\Attachment\File
 */
class Uploader
{
    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var string[]
     */
    private $allowedExtensions;

    /**
     * @var Info
     */
    private $info;

    /**
     * @param UploaderFactory $uploaderFactory
     * @param Info $info
     */
    public function __construct(
        UploaderFactory $uploaderFactory,
        Info $info
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->info = $info;
    }

    /**
     * Save file to temp directory
     *
     * @param string $fileId
     * @return array
     */
    public function saveToTmpFolder($fileId)
    {
        try {
            $result = ['file' => '', 'size' => '', 'name' => '', 'path' => '', 'type' => ''];
            $mediaDirectory = $this->info
                ->getMediaDirectory()
                ->getAbsolutePath(Info::FILE_DIR);
            /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
            $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
            $uploader
                ->setAllowRenameFiles(true)
                ->setAllowedExtensions($this->getAllowedExtensions());
            $result = array_intersect_key($uploader->save($mediaDirectory), $result);

            $result['url'] = $this->info->getMediaUrl($result['file']);
            $result['file_name'] = $result['file'];
            $result['id'] = base64_encode($result['file_name']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $result;
    }

    /**
     * Set allowed extensions
     *
     * @param string[] $allowedExtensions
     * @return $this
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
        return $this;
    }

    /**
     * Retrieve allowed extensions
     *
     * @return string[]
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }
}
