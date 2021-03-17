<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\AbstractModifier;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection;

/**
 * Class Attachments
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend
 */
class Attachments extends AbstractModifier
{
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
     */
    private function prepareAttachments(&$data)
    {
        $attachments = isset($data[ReviewInterface::ATTACHMENTS]) ? $data[ReviewInterface::ATTACHMENTS] : [];
        $attachmentIndex = 1;
        foreach ($attachments as &$attachment) {
            $productNameLabel = isset($data[Collection::PRODUCT_NAME_COLUMN_NAME . '_label'])
                ? __(" of %1", $data[Collection::PRODUCT_NAME_COLUMN_NAME . '_label'])
                : '';
            $authorLabel = isset($data[ReviewInterface::NICKNAME])
                ? __(" by %1", $data[ReviewInterface::NICKNAME])
                : '';
            $imageTitlePattern = __("A real photo") . $productNameLabel . $authorLabel;
            $attachment = array_merge(
                $attachment,
                [
                    'image_title' => $imageTitlePattern . ' (' . $attachmentIndex . ')',
                ]
            );
            $attachmentIndex++;
        }
        $data[ReviewInterface::ATTACHMENTS] = $attachments;
        return $data;
    }
}
