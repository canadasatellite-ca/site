<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Logger\Processor;

use Magento\Amazon\Logger\DebugLogging;
use Monolog\Processor\IntrospectionProcessor;

class AddIntrospection extends IntrospectionProcessor
{
    /**
     * @var DebugLogging
     */
    private $debugLogging;

    public function __construct(
        DebugLogging $debugLogging,
        $level = \Monolog\Logger::DEBUG,
        array $skipClassesPartials = [],
        $skipStackFramesCount = 0
    ) {
        parent::__construct($level, $skipClassesPartials, $skipStackFramesCount);
        $this->debugLogging = $debugLogging;
    }

    public function __invoke(array $record)
    {
        if (!$this->debugLogging->isEnabled()) {
            return $record;
        }
        return parent::__invoke($record);
    }
}
