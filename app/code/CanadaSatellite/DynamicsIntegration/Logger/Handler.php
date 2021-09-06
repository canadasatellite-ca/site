<?php

namespace CanadaSatellite\DynamicsIntegration\Logger;

use Monolog\Logger as DynamicsIntegrationLogger;

class Handler extends \Magento\Framework\Logger\Handler\Base {
	protected $loggerType = DynamicsIntegrationLogger::DEBUG;

	protected $fileName = '/var/log/dynamics.log';
}