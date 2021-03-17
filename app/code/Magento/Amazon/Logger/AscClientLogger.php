<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Logger;

/**
 * Class AscClientLogger
 *
 * Writes ASC module log entries in var\log\amazon_channel.log for DEBUG level and above.
 * DEBUG and INFO levels are entered in Developer mode, and blocked in production mode.
 * Provides utility for code tracing (Developer mode only), and improved Exception logging.
 *
 * Additionally, writes ASC module log entries in var\log\system.log for WARNING level and above.
 * Additionally, Writes ASC module log entries in var\log\exception.log for ERROR level.
 *
 */
class AscClientLogger extends \Magento\Framework\Logger\Monolog
{
}
