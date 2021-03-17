<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Processor;

use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;

/**
 * Interface MetadataProcessorInterface
 * @package Aheadworks\AdvancedReviews\Model\Email\Processor
 */
interface MetadataProcessorInterface
{
    /**
     * Process
     *
     * @param QueueItemInterface $confirmation
     * @return EmailMetadataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function process($confirmation);
}
