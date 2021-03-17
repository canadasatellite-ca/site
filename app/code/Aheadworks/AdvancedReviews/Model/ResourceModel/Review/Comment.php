<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Review;

use Aheadworks\AdvancedReviews\Model\ResourceModel\AbstractResource;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;

/**
 * Class Comment
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Review
 */
class Comment extends AbstractResource
{
    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME = 'aw_ar_review_comment';
    const MAIN_TABLE_ID_FIELD_NAME = 'id';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }
}
