<?php

namespace Magento\Amazon\Model;

use Magento\Framework\DB\Adapter\DeadlockException;
use Magento\Framework\DB\Adapter\LockWaitException;

trait DeadlockRetriesTrait
{
    /**
     * @param callable $action
     * @param int $retries
     * @param int $timeOutInMs
     * @return mixed
     * @throws DeadlockException
     * @throws LockWaitException
     */
    protected function doWithDeadlockRetries(
        callable $action,
        int $retries = 10,
        int $timeOutInMs = 200
    ) {
        $attempt = 1;
        do {
            try {
                return call_user_func($action);
            } catch (DeadlockException | LockWaitException $exception) {
                if ($retries === $attempt) {
                    throw $exception;
                }
            }
            $attempt++;
            usleep($timeOutInMs);
        } while ($attempt <= $retries);
    }
}
