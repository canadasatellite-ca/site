<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review;

use Aheadworks\AdvancedReviews\Api\Data\ReviewAttachmentInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class Attachment
 * @package Aheadworks\AdvancedReviews\Model\Review
 */
class Attachment extends AbstractSimpleObject implements ReviewAttachmentInterface
{
    /**
     * {@inheritdoc}
     */
    public function getReviewId()
    {
        return $this->_get(self::REVIEW_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setReviewId($reviewId)
    {
        return $this->setData(self::REVIEW_ID, $reviewId);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return $this->_get(self::FILE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setFileName($fileName)
    {
        return $this->setData(self::FILE_NAME, $fileName);
    }
}
