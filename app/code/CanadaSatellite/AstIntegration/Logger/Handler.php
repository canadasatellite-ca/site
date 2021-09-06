<?php

namespace CanadaSatellite\AstIntegration\Logger;

use Monolog\Logger as AstIntegrationLogger;

class Handler extends \Magento\Framework\Logger\Handler\Base {
    protected $loggerType = AstIntegrationLogger::DEBUG;

    protected $fileName = '/var/log/ast_integration.log';
}