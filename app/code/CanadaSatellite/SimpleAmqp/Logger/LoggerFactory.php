<?php

namespace CanadaSatellite\SimpleAmqp\Logger;

use Magento\Framework\Filesystem\DriverInterface;

class LoggerFactory
{
	private $filesystem;

	public function __construct(
		DriverInterface $filesystem
	) {
		$this->filesystem = $filesystem;
	}

	public function getLogger($queueName)
	{
		return new Logger(
			$queueName,
			array(
				new Handler(
					$this->filesystem,
					"/var/log/{$queueName}.txt"
				),
			)
		);
	}
}
