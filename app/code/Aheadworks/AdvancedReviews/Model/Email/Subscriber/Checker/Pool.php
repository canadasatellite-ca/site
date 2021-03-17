<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker;

/**
 * Class Pool
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker
 */
class Pool
{
    /**
     * @var CheckerInterface[]
     */
    private $checkers;

    /**
     * @param array $checkers
     */
    public function __construct(
        $checkers = []
    ) {
        $this->checkers = $checkers;
    }

    /**
     * Retrieve checker instance by email type
     *
     * @param int $emailType
     * @return CheckerInterface|null
     */
    public function getCheckerByEmailType($emailType)
    {
        if (isset($this->checkers[$emailType])
            && ($this->checkers[$emailType] instanceof CheckerInterface)
        ) {
            return $this->checkers[$emailType];
        }

        return null;
    }
}
