<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker\EmailType;

use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker\CheckerInterface;

/**
 * Class NewComment
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker\EmailType
 */
class NewComment implements CheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isNeedToSendEmail($subscriber)
    {
        return $subscriber->getIsNewCommentEmailEnabled();
    }
}
