<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel;

/**
 * Class Review
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel
 */
class Review extends AbstractResource
{
    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME = 'aw_ar_review';
    const MAIN_TABLE_ID_FIELD_NAME = 'id';
    const SHARED_STORE_TABLE_NAME = 'aw_ar_review_shared_store';
    const REVIEW_ATTACHMENT_TABLE_NAME = 'aw_ar_review_attachment';
    const REMINDER_ORDER_ITEM_TABLE_NAME = 'aw_ar_reminder_review_order_item';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }
}
