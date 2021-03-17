<?php

declare(strict_types=1);

namespace Magento\Amazon\Test\Unit\Comm\Amazon;

use Magento\Amazon\Comm\Amazon\UpdateHandler;
use Magento\Amazon\Comm\Amazon\UpdateHandler\HandlerInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\Account;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\TestCase;

class UpdateHandlerTest extends TestCase
{
    private $objectManager;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Account
     */
    private $account;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|AscClientLogger
     */
    private $logger;

    protected function setUp()
    {
        $this->objectManager = new ObjectManagerHelper($this);
        $this->account = $this->getMockBuilder(Account::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->getMockBuilder(AscClientLogger::class)->disableOriginalConstructor()->getMock();
    }

    public function testCreateWithNoHandlers()
    {
        $updateHandler = $this->createUpdateHandler([]);
        $this->assertInstanceOf(UpdateHandler::class, $updateHandler);
    }

    public function testCreateWithMockedHandler()
    {
        $mockedHandler = $this->getMockBuilder(HandlerInterface::class)
            ->getMockForAbstractClass();
        $handlers = ['MockedHandler' => $mockedHandler];
        $updateHandler = $this->createUpdateHandler($handlers);
        $this->assertInstanceOf(UpdateHandler::class, $updateHandler);
    }

    /**
     * @dataProvider failedLogHandlerDeterminationDataProvider
     * @param array $update
     * @param string $exceptionText
     */
    public function testHandleFailsOnHandlerDetermination(array $update, string $exceptionText)
    {
        $mockedHandler = $this->getMockBuilder(HandlerInterface::class)
            ->getMockForAbstractClass();
        $handlers = ['MockedHandler' => $mockedHandler];
        $updateHandler = $this->createUpdateHandler($handlers, $this->logger);
        $this->assertInstanceOf(UpdateHandler::class, $updateHandler);
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                'Cannot schedule update for processing. Please report an error.',
                $this->callback(
                    function (array $data) use ($exceptionText) {
                        return isset($data['exception'])
                            && $data['exception'] instanceof \Exception
                            && $data['exception']->getMessage() ===  $exceptionText;
                    }
                )
            );
        $update = array_merge($update, ['log' => '{"id":123,"seller_sku":"my-seller-sku"}']);
        $updateHandler->handle([$update], $this->account);
    }

    public function failedLogHandlerDeterminationDataProvider(): array
    {
        return [
            'empty type' => ['update' => ['type' => ''], 'exceptionText' => 'Invalid log record: missing type'],
            'missing action' => [
                'update' => ['type' => 'my-type'],
                'exceptionText' => 'Invalid log record: missing action'
            ],
            'empty action' => [
                'update' => ['type' => 'my-type', 'action' => ''],
                'exceptionText' => 'Invalid log record: missing action'
            ],
        ];
    }

    /**
     * @param array $update
     * @param string $handlerName
     * @dataProvider mockedHandlerBeingResolvedDataProvider
     */
    public function testMockedHandlerBeingResolved(array $update, string $handlerName)
    {
        $mockedHandler = $this->getMockBuilder(HandlerInterface::class)
            ->getMockForAbstractClass();
        $handlers = [$handlerName => $mockedHandler];
        $updateHandler = $this->createUpdateHandler($handlers, $this->logger);
        $this->assertInstanceOf(UpdateHandler::class, $updateHandler);
        $this->logger->expects($this->never())->method('error');
        $update = array_merge($update, ['log' => '{"id":123,"seller_sku":"my-seller-sku"}']);
        $updateHandler->handle([$update], $this->account);
    }

    public function mockedHandlerBeingResolvedDataProvider()
    {
        return [
            'concatenated name' => [
                'update' => ['type' => 'Type', 'action' => 'Action'],
                'handlerName' => 'ActionType'
            ],
            'just type' => [
                'update' => ['type' => 'Type', 'action' => 'Action'],
                'handlerName' => 'Type'
            ],
        ];
    }

    /**
     * @param array $update
     * @param string $handlerName
     * @dataProvider mockedHandlerIsNotResolvedDataProvider
     */
    public function testMockedHandlerIsNotResolved(array $update, string $handlerName)
    {
        $mockedHandler = $this->getMockBuilder(HandlerInterface::class)
            ->getMockForAbstractClass();
        $handlers = [$handlerName => $mockedHandler];
        $updateHandler = $this->createUpdateHandler($handlers, $this->logger);
        $this->assertInstanceOf(UpdateHandler::class, $updateHandler);
        $this->logger->expects($this->once())->method('error');
        $update = array_merge($update, ['log' => '{"id":123,"seller_sku":"my-seller-sku"}']);
        $updateHandler->handle([$update], $this->account);
    }

    public function mockedHandlerIsNotResolvedDataProvider()
    {
        return [
            'just action' => [
                'update' => ['type' => 'Type', 'action' => 'Blah'],
                'handlerName' => 'Blah'
            ],
            'combined type action' => [
                'update' => ['type' => 'Type', 'action' => 'Action'],
                'handlerName' => 'TypeAction'
            ],
        ];
    }

    /**
     * @param array $update
     * @param string $handlerName
     */
    public function testPrefersCombinedHandlerOverJustType()
    {
        $handlerNeverCalled = $this->getMockBuilder(HandlerInterface::class)
            ->getMockForAbstractClass();
        $handlerNeverCalled->expects($this->never())->method('handle');
        $handlerCalledOnce = $this->getMockBuilder(HandlerInterface::class)
            ->getMockForAbstractClass();
        $handlerCalledOnce->expects($this->once())->method('handle');
        $handlers = ['Action' => $handlerNeverCalled, 'ActionType' => $handlerCalledOnce];
        $updateHandler = $this->createUpdateHandler($handlers, $this->logger);
        $this->assertInstanceOf(UpdateHandler::class, $updateHandler);
        $this->logger->expects($this->never())->method('error');
        $update = ['type' => 'Type', 'action' => 'Action'];
        $update = array_merge($update, ['log' => '{"id":123,"seller_sku":"my-seller-sku"}']);
        $updateHandler->handle([$update], $this->account);
    }

    private function createUpdateHandler(array $handlers, ?AscClientLogger $logger = null): UpdateHandler
    {
        $params = [
            'handlers' => $handlers,
        ];
        if ($logger) {
            $params['logger'] = $logger;
        }
        /** @var UpdateHandler $updateHandler */
        $updateHandler = $this->objectManager->getObject(UpdateHandler::class, $params);
        return $updateHandler;
    }
}
