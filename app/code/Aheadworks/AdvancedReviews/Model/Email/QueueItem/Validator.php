<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\QueueItem;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Pool as ValidatorPool;

/**
 * Class Validator
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\QueueItem
 */
class Validator implements ValidatorInterface
{
    /**
     * @var ValidatorPool
     */
    private $validatorPool;

    /**
     * @param ValidatorPool $validatorPool
     */
    public function __construct(
        ValidatorPool $validatorPool
    ) {
        $this->validatorPool = $validatorPool;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(QueueItemInterface $queueItem)
    {
        $isValid = false;

        $validator = $this->validatorPool->getValidatorByType($queueItem->getType());
        if ($validator) {
            $isValid = $validator->isValid($queueItem);
        }

        return $isValid;
    }
}
