<?php

namespace CanadaSatellite\SimpleAmqp\Logger;

use Magento\Framework\Filesystem\DriverInterface;

class Handler extends \Magento\Framework\Logger\Handler\Base {
	protected $loggerType = \Monolog\Logger::DEBUG;

	public function __construct(
		DriverInterface $filesystem,
		$fileName,
		$filePath = null
	) {
		$this->fileName = $fileName;
		parent::__construct($filesystem, $filePath);
	}
}
