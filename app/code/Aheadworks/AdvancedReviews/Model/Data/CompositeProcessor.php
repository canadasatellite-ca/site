<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Data;

/**
 * Class CompositeProcessor
 *
 * @package Aheadworks\AdvancedReviews\Model\Data
 */
class CompositeProcessor implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        foreach ($this->processors as $processor) {
            if (!$processor instanceof ProcessorInterface) {
                throw new \Exception('Data processor must implement ' . ProcessorInterface::class);
            }
            $data = $processor->process($data);
        }
        return $data;
    }
}
