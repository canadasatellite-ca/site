<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier;

use Aheadworks\AdvancedReviews\Model\Attachment\File\Info as FileInfo;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewAttachmentInterface;

/**
 * Class Attachments
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier
 */
class Attachments extends AbstractModifier
{
    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @param FileInfo $fileInfo
     */
    public function __construct(
        FileInfo $fileInfo
    ) {
        $this->fileInfo = $fileInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->isSetId($data)) {
            $this->prepareAttachments($data);
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Prepare attachments
     *
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function prepareAttachments(&$data)
    {
        $attachments = isset($data[ReviewInterface::ATTACHMENTS]) ? $data[ReviewInterface::ATTACHMENTS] : [];
        foreach ($attachments as &$attachment) {
            $fileName = $attachment[ReviewAttachmentInterface::FILE_NAME];
            $attachment = array_merge(
                $attachment,
                [
                    'id' => base64_encode($fileName),
                    'url' => $this->fileInfo->getMediaUrl($fileName),
                    'type' => $this->fileInfo->getMimeType($fileName),
                    'size' => $this->fileInfo->getStat($fileName)['size'],
                ]
            );
        }
        $data[ReviewInterface::ATTACHMENTS] = $attachments;
        return $data;
    }
}
