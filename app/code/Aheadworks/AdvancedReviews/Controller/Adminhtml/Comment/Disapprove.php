<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment;

use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Status;

/**
 * Class Disapprove
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment
 */
class Disapprove extends AbstractChangeStatusAction
{
    /**
     * {@inheritdoc}
     */
    protected function getStatusToChange()
    {
        return Status::NOT_APPROVED;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessMessage()
    {
        return __('The comment was disapproved successfully.');
    }
}
