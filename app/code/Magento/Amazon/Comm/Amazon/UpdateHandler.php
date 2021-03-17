<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon;

use Assert\Assert;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Comm\Amazon\UpdateHandler\HandlerInterface;
use Magento\Amazon\Comm\Amazon\UpdateHandler\InvalidRecordException;
use Magento\Amazon\Comm\Amazon\UpdateHandler\NoHandlerFoundException;
use Magento\Amazon\Logger\AscClientLogger;

class UpdateHandler implements HandlerInterface
{
    /**
     * @var array
     */
    private $handlers;
    /**
     * @var AscClientLogger
     */
    private $logger;

    /**
     * UpdateHandler constructor.
     * @param AscClientLogger $logger
     * @param array $handlers
     */
    public function __construct(AscClientLogger $logger, array $handlers)
    {
        Assert::thatAll($handlers)->isInstanceOf(HandlerInterface::class);
        $this->handlers = $handlers;
        $this->logger = $logger;
    }

    /**
     * @param array $data
     * @return mixed|string
     * @throws InvalidRecordException
     * @throws NoHandlerFoundException
     */
    private function getHandlerName(array $data)
    {
        $type = $data['type'] ?? '';
        $action = $data['action'] ?? '';

        if (!$type) {
            throw new InvalidRecordException(__('Invalid log record: missing type'));
        }
        if (!$action) {
            throw new InvalidRecordException(__('Invalid log record: missing action'));
        }
        $actionTypeName = $action . $type;
        if (isset($this->handlers[$actionTypeName])) {
            return $actionTypeName;
        }
        if (isset($this->handlers[$type])) {
            return $type;
        }
        throw new NoHandlerFoundException(__('Cannot find a handler for log record of type ' . $type));
    }

    /**
     * @param array $updates
     * @param AccountInterface $account
     * @return array ids of persisted logs
     */
    public function handle(array $updates, AccountInterface $account): array
    {
        /** @var array[] $updatesPerHandler */
        $updatesPerHandler = [];
        $this->logger->debug('Going to process updates', ['account' => $account, 'logIds' => array_keys($updates)]);
        foreach ($updates as $logId => $logData) {
            $logId = (string)$logId;

            try {
                $log = $logData['log'] ?? '';
                $log = json_decode($log, true);
                if (empty($log)) {
                    continue;
                }

                $handlerName = $this->getHandlerName($logData);
                $updatesPerHandler[$handlerName][$logId] = $log;
            } catch (\Throwable $e) {
                $this->logger->error(
                    'Cannot schedule update for processing. Please report an error.',
                    ['exception' => $e, 'logId' => $logId, 'logData' => $logData, 'account' => $account]
                );
            }
        }
        $processedLogsResults = [];
        foreach ($this->handlers as $handlerName => $handler) {
            if (isset($updatesPerHandler[$handlerName])) {
                $setOfUpdates = $updatesPerHandler[$handlerName];
                $this->logger->debug(
                    'Handling updates of type ' . $handlerName,
                    ['account' => $account, 'logIds' => array_keys($setOfUpdates)]
                );
                try {
                    $processedLogsResults[] = $handler->handle($setOfUpdates, $account);
                } catch (\Throwable $e) {
                    $this->logger->error('Cannot handle updates of type ' . $handlerName, ['exception' => $e]);
                }
            } else {
                $this->logger->debug(
                    'No updates fetched for type ' . $handlerName,
                    ['account' => $account]
                );
            }
        }
        return array_merge([], ...$processedLogsResults);
    }
}
