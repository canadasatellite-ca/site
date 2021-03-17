<?php

namespace Magento\Amazon\Test\Integration\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Comm\Amazon\UpdateHandler\ChunkedHandler;
use Magento\Amazon\Comm\Amazon\UpdateHandler\Order;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\Account;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\CollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;

abstract class BaseTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|AscClientLogger
     */
    protected $logger;
    /**
     * @var ChunkedHandler
     */
    protected $chunkedHandler;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->logger = $this->getMockBuilder(AscClientLogger::class)->disableOriginalConstructor()->getMock();
        $this->chunkedHandler = $this->objectManager->create(ChunkedHandler::class, ['logger' => $this->logger]);
    }

    /**
     * @param string $name
     * @return Account
     */
    protected function getAccountByName(string $name): Account
    {
        $accountCollectionFactory = $this->objectManager->create(
            \Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory::class
        );

        /** @var \Magento\Amazon\Model\Amazon\Account $account */
        $account = $accountCollectionFactory->create()
            ->addFieldToFilter('name', $name)
            ->getFirstItem();

        return $account;
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function getTestFileContents(string $fileName): string
    {
        /** @var string $content */
        $content = file_get_contents(__DIR__ . '/../../../_files/' . $fileName);

        return $content;
    }
}
