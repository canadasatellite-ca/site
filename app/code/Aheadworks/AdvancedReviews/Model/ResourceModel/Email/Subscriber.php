<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Email;

use Aheadworks\AdvancedReviews\Model\ResourceModel\AbstractResource;

/**
 * Class Subscriber
 *
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Email
 */
class Subscriber extends AbstractResource
{
    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME = 'aw_ar_email_subscriber';
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
