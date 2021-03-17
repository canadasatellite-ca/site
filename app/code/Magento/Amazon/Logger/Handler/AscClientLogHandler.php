<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Logger\Handler;

use Magento\Amazon\Logger\DebugLogging;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

/**
 * Class AscClientLogHandler
 */
class AscClientLogHandler extends Base
{
    /** @var string */
    protected $fileName = '/var/log/channel_amazon.log';

    /**
     * AscClientLogHandler constructor.
     * @param DebugLogging $debugLogging
     * @param DriverInterface $filesystem
     * @param null $filePath
     * @param null $fileName
     * @throws \Exception
     */
    public function __construct(
        DebugLogging $debugLogging,
        DriverInterface $filesystem,
        $filePath = null,
        $fileName = null
    ) {
        // Dynamically identify supported logging level depending on configuration
        $this->loggerType = $debugLogging->isEnabled() ? Logger::DEBUG : Logger::WARNING;

        parent::__construct($filesystem, $filePath, $fileName);
        $this->setFormatter(new LineFormatter(null, null, false));
    }
}
