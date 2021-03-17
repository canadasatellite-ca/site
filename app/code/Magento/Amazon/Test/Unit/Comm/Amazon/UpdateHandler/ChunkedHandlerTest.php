<?php

declare(strict_types=1);

namespace Magento\Amazon\Test\Unit\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Comm\Amazon\UpdateHandler\ChunkedHandler;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\Account;
use Magento\Framework\DB\Adapter\DeadlockException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\TestCase;

class ChunkedHandlerTest extends TestCase
{
    /**
     * @var object|ChunkedHandler
     */
    private $chunkedHandler;
    /**
     * @var object|Account
     */
    private $account;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|AscClientLogger
     */
    private $logger;

    protected function setUp()
    {
        $objectManager = new ObjectManagerHelper($this);
        $this->account = $account = $objectManager->getObject(Account::class);
        $this->logger = $this->getMockBuilder(AscClientLogger::class)->disableOriginalConstructor()->getMock();
        $this->chunkedHandler = $objectManager->getObject(ChunkedHandler::class, ['logger' => $this->logger]);
    }

    private function noErrorsExpected()
    {
        $this->logger->expects($this->never())->method($this->anything());
    }

    public function testHandlerFunctionIsCalled()
    {
        $this->noErrorsExpected();
        $allLogs = [
            10 => 10,
            20 => 20,
            30 => 30,
        ];
        $isCalled = false;
        $handlerFn = static function () use (&$isCalled) {
            $isCalled = true;
        };
        $this->chunkedHandler->handleUpdatesWithChunks($handlerFn, $allLogs, $this->account, 'my custom log error');
        $this->assertTrue($isCalled, 'The callback has not been called');
    }

    public function testHandlerFunctionIsNotCalledForEmptyUpdates()
    {
        $this->noErrorsExpected();
        $allLogs = [];
        $isCalled = false;
        $handlerFn = static function () use (&$isCalled) {
            $isCalled = true;
        };
        $this->chunkedHandler->handleUpdatesWithChunks($handlerFn, $allLogs, $this->account, 'my custom log error');
        $this->assertFalse($isCalled, 'The callback has been called');
    }

    public function testLoggerIsCalledOnException()
    {
        $this->logger->expects($this->once())->method($this->anything());
        $allLogs = $this->generateLogs(10);
        $callsCount = 0;
        $handlerFn = function (array $chunk) use (&$callsCount) {
            $callsCount++;
            throw new \Exception('Handler exception');
        };
        $this->chunkedHandler->handleUpdatesWithChunks(
            $handlerFn,
            $allLogs,
            $this->account,
            'my custom log error',
            100
        );
        $this->assertEquals(1, $callsCount, "Calls count doesn't match");
    }

    public function testLoggerPassesCorrectErrorMessage()
    {
        $errorMessage = 'my custom log error message';
        $this->logger->expects($this->once())->method($this->anything())->with($errorMessage);
        $allLogs = $this->generateLogs(10);
        $callsCount = 0;
        $handlerFn = function (array $chunk) use (&$callsCount) {
            $callsCount++;
            throw new \Exception('Handler exception');
        };
        $this->chunkedHandler->handleUpdatesWithChunks($handlerFn, $allLogs, $this->account, $errorMessage, 100);
        $this->assertEquals(1, $callsCount, "Calls count doesn't match");
    }

    public function testHandlerRespectsChunkSize()
    {
        $this->noErrorsExpected();
        $allLogs = $this->generateLogs(31);
        $callsCount = 0;
        $handlerFn = static function () use (&$callsCount) {
            $callsCount++;
        };
        $this->chunkedHandler->handleUpdatesWithChunks($handlerFn, $allLogs, $this->account, 'my custom log error', 10);
        $this->assertEquals(4, $callsCount, "Calls count doesn't match");
    }

    public function testHandlerPassesChunkToFunction()
    {
        $this->noErrorsExpected();
        $allLogs = range(1, 31);
        $callsCount = 0;
        $handlerFn = function (array $chunk) use (&$callsCount) {
            $this->assertNotEmpty($chunk);
            $callsCount++;
        };
        $this->chunkedHandler->handleUpdatesWithChunks($handlerFn, $allLogs, $this->account, 'my custom log error', 10);
        $this->assertEquals(4, $callsCount, "Calls count doesn't match");
    }

    public function testHandlerPassesCorrectChunks()
    {
        $this->noErrorsExpected();
        $allLogs = $this->generateLogs(31, 1);
        $callsCount = 0;
        $expectedChunksPerCall = [
            1 => $this->generateLogs(10, 1),
            2 => $this->generateLogs(10, 11),
            3 => $this->generateLogs(10, 21),
            4 => $this->generateLogs(1, 31),
        ];
        $handlerFn = function (array $chunk) use (&$callsCount, $expectedChunksPerCall) {
            $this->assertNotEmpty($chunk);
            $callsCount++;
            $this->assertArrayHasKey($callsCount, $expectedChunksPerCall, 'This call should not happen');
            $this->assertEquals(
                array_values($expectedChunksPerCall[$callsCount]),
                array_values($chunk),
                'The chunk data does not match'
            );
        };
        $this->chunkedHandler->handleUpdatesWithChunks($handlerFn, $allLogs, $this->account, 'my custom log error', 10);
        $this->assertEquals(4, $callsCount, "Calls count doesn't match");
    }

    public function testHandlerReturnsProcessedIds()
    {
        $this->noErrorsExpected();
        $allLogs = $this->generateLogs(31, 1000);
        $callsCount = 0;
        $handlerFn = function () use (&$callsCount) {
            $callsCount++;
        };
        $processedLogs = $this->chunkedHandler->handleUpdatesWithChunks(
            $handlerFn,
            $allLogs,
            $this->account,
            'my custom log error',
            10
        );
        $this->assertEquals(4, $callsCount, "Calls count doesn't match");
        $this->assertEquals(array_keys($allLogs), $processedLogs, "Returned log ids doesn't match");
    }

    public function testHandlerSkipsIdsWithinFailedChunk()
    {
        $this->logger->expects($this->once())->method($this->anything());
        $allLogs = $this->generateLogs(31, 1001);
        $callsCount = 0;
        $handlerFn = function () use (&$callsCount) {
            $callsCount++;
            if ($callsCount === 2) {
                throw new \Exception('Ignoring second chunk');
            }
        };
        $processedLogs = $this->chunkedHandler->handleUpdatesWithChunks(
            $handlerFn,
            $allLogs,
            $this->account,
            'my custom log error',
            10
        );
        $this->assertEquals(4, $callsCount, "Calls count doesn't match");
        $expectedIds = array_merge(range(1001, 1010), range(1021, 1031));
        $this->assertEquals($expectedIds, $processedLogs, "Returned log ids doesn't match");
    }

    public function testHandlerRetriesOnDeadlocks()
    {
        $this->logger->expects($this->exactly(2))->method($this->anything());
        $allLogs = $this->generateLogs(30, 51);
        $callsCount = 0;
        $handlerFn = function (array $chunk) use (&$callsCount) {
            $callsCount++;
            // todo: should be just array_key_last($chunk); once we'll drop PHP <= 7.3.0
            $lastId = key(array_slice($chunk, -1, 1, true));
            // throw exception for first 2 chunks
            if ($lastId <= 70) {
                throw new DeadlockException('Testing deadlocks');
            }
        };
        $processedLogs = $this->chunkedHandler->handleUpdatesWithChunks(
            $handlerFn,
            $allLogs,
            $this->account,
            'my custom log error',
            10
        );
        // two iterations with 10 retries each and one successful iteration
        $this->assertEquals(21, $callsCount, "Calls count doesn't match");
        $expectedIds = range(71, 80);
        $this->assertEquals($expectedIds, $processedLogs, "Returned log ids doesn't match");
    }

    private function generateLogs(int $count, int $startId = 1)
    {
        $logs = [];
        $lastRangeItem = $startId + $count - 1;
        foreach (range($startId, $lastRangeItem) as $logNumber) {
            $logs[$logNumber] = [
                'name' => "log_${logNumber}",
                'identifier' => $logNumber,
            ];
        }
        return $logs;
    }
}
