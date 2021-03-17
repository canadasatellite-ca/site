<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment;

use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Status;

/**
 * Class Approve
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment
 */
class Approve extends AbstractChangeStatusAction
{
    /**
     * {@inheritdoc}
     */
    protected function getStatusToChange()
    {
        return Status::APPROVED;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessMessage()
    {
        return __('The comment was approved successfully.');
    }
}
