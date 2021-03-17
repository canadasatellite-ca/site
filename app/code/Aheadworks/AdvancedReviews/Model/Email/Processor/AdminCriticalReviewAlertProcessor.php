<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Processor;

/**
 * Class AdminCriticalReviewAlertProcessor
 * @package Aheadworks\AdvancedReviews\Model\Email\Processor
 */
class AdminCriticalReviewAlertProcessor extends NewReviewProcessor
{
    /**
     * {@inheritdoc}
     */
    protected function getTemplateId($storeId)
    {
        return $this->config->getEmailTemplateForCriticalReviewAlert($storeId);
    }
}
