<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Customer;

use Aheadworks\AdvancedReviews\Model\ResourceModel\AbstractResource;

/**
 * Class Nickname
 *
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Customer
 */
class Nickname extends AbstractResource
{
    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME = 'aw_ar_customer_nickname';
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
