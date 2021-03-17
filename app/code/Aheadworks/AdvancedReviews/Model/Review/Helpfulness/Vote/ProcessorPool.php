<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote;

use Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote\Processor\Base as BaseProcessor;

/**
 * Class ProcessorPool
 * @package Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote
 */
class ProcessorPool
{
    /**
     * @var BaseProcessor
     */
    private $baseProcessor;

    /**
     * @var array
     */
    private $processors;

    /**
     * @param BaseProcessor $baseProcessor
     * @param array $processors
     */
    public function __construct(
        BaseProcessor $baseProcessor,
        array $processors = []
    ) {
        $this->baseProcessor = $baseProcessor;
        $this->processors = $processors;
    }

    /**
     * Retrieve processor by action
     *
     * @param string $action
     * @return ProcessorInterface
     */
    public function getByAction($action)
    {
        if (!isset($this->processors[$action])
            || !($this->processors[$action] instanceof ProcessorInterface)
        ) {
            return $this->baseProcessor;
        }

        return $this->processors[$action];
    }
}
