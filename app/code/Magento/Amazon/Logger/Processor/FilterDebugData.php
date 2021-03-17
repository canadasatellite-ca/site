<?php

namespace Magento\Amazon\Logger\Processor;

use Magento\Amazon\Logger\DebugLogging;

class FilterDebugData implements \Monolog\Processor\ProcessorInterface
{
    /**
     * @var DebugLogging
     */
    private $debugLogging;

    /**
     * FilterDebugData constructor.
     * @param DebugLogging $debugLogging
     */
    public function __construct(DebugLogging $debugLogging)
    {
        $this->debugLogging = $debugLogging;
    }

    public function __invoke(array $records)
    {
        if (!$this->debugLogging->isEnabled()) {
            unset($records['context']['debug']);
        }
        return $records;
    }
}
