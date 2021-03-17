<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Throwable;

class CannotPersistChangesException extends \RuntimeException
{
    /**
     * @var array
     */
    private $logIds;

    public function __construct(string $type, array $logIds, Throwable $previous = null)
    {
        $message = sprintf(
            'Cannot save a bunch of %d logs of the type "%s". Please report an error.',
            count($logIds),
            $type
        );
        parent::__construct($message, 0, $previous);
        $this->logIds = $logIds;
    }

    /**
     * @return array
     */
    public function getLogIds(): array
    {
        return $this->logIds;
    }
}
