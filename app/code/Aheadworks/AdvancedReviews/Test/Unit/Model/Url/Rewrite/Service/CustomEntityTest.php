<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Url\Rewrite\Service;

use Aheadworks\AdvancedReviews\Model\Url\Rewrite\Service\CustomEntity;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteData;
use Magento\UrlRewrite\Model\UrlRewrite;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Psr\Log\LoggerInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Url\Rewrite\Service\CustomEntity
 */
class CustomEntityTest extends TestCase
{
    /**
     * @var CustomEntity
     */
    private $service;

    /**
     * @var UrlPersistInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlPersistMock;

    /**
     * @var UrlRewriteFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlRewriteFactoryMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->urlPersistMock = $this->createMock(UrlPersistInterface::class);
        $this->urlRewriteFactoryMock = $this->createMock(UrlRewriteFactory::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->service = $objectManager->getObject(
            CustomEntity::class,
            [
                'urlPersist' => $this->urlPersistMock,
                'urlRewriteFactory' => $this->urlRewriteFactoryMock,
                'logger' => $this->loggerMock,
            ]
        );
    }

    /**
     * Test deleteRewrites method
     *
     * @param array $storeIds
     * @param string $requestPath
     * @param bool $isNeedToCallInnerDeleteMethod
     * @dataProvider deleteRewritesDataProvider
     */
    public function testDeleteRewrites($storeIds, $requestPath, $isNeedToCallInnerDeleteMethod)
    {
        if ($isNeedToCallInnerDeleteMethod) {
            $this->urlPersistMock->expects($this->once())
                ->method('deleteByData')
                ->with(
                    [
                        UrlRewriteData::ENTITY_TYPE => CustomEntity::URL_REWRITE_ENTITY_TYPE,
                        UrlRewriteData::STORE_ID => $storeIds,
                        UrlRewriteData::REQUEST_PATH => $requestPath,
                    ]
                );
        } else {
            $this->urlPersistMock->expects($this->never())
                ->method('deleteByData');
        }
        $this->service->deleteRewrites($storeIds, $requestPath);
    }

    /**
     * @return array
     */
    public function deleteRewritesDataProvider()
    {
        return [
            [
                'storeIds' => [],
                'requestPath' => '',
                'isNeedToCallInnerDeleteMethod' => false,
            ],
            [
                'storeIds' => [1,2],
                'requestPath' => '',
                'isNeedToCallInnerDeleteMethod' => false,
            ],
            [
                'storeIds' => [],
                'requestPath' => 'all-reviews',
                'isNeedToCallInnerDeleteMethod' => false,
            ],
            [
                'storeIds' => [1,2],
                'requestPath' => 'all-reviews',
                'isNeedToCallInnerDeleteMethod' => true,
            ],
        ];
    }

    /**
     * Test addRewrites method
     *
     * @param array $storeIds
     * @param string $requestPath
     * @param string $targetPath
     * @dataProvider addRewritesDataProvider
     */
    public function testAddRewrites($storeIds, $requestPath, $targetPath)
    {
        for ($index = 0; $index < count($storeIds); $index++) {
            $urlRewriteMock = $this->createPartialMock(
                UrlRewrite::class,
                [
                    'setStoreId',
                    'setEntityType',
                    'setRequestPath',
                    'setTargetPath',
                    'setRedirectType',
                    'save',
                ]
            );
            $urlRewriteMock->expects($this->once())
                ->method('setStoreId')
                ->willReturnSelf();
            $urlRewriteMock->expects($this->once())
                ->method('setEntityType')
                ->with(CustomEntity::URL_REWRITE_ENTITY_TYPE)
                ->willReturnSelf();
            $urlRewriteMock->expects($this->once())
                ->method('setRequestPath')
                ->with($requestPath)
                ->willReturnSelf();
            $urlRewriteMock->expects($this->once())
                ->method('setTargetPath')
                ->with($targetPath)
                ->willReturnSelf();
            $urlRewriteMock->expects($this->once())
                ->method('setRedirectType')
                ->with(CustomEntity::REDIRECT_TYPE)
                ->willReturnSelf();
            $urlRewriteMock->expects($this->once())
                ->method('save')
                ->willReturnSelf();
            $this->urlRewriteFactoryMock->expects($this->at($index))
                ->method('create')
                ->willReturn($urlRewriteMock);
        }
        $this->loggerMock->expects($this->never())
            ->method('warning');
        $this->service->addRewrites($storeIds, $requestPath, $targetPath);
    }

    /**
     * @return array
     */
    public function addRewritesDataProvider()
    {
        return [
            [
                'storeIds' => [1,2,3],
                'requestPath' => 'all-reviews',
                'targetPath' => 'aw_advanced_reviews/review_page/index',
            ],
        ];
    }

    /**
     * Test addRewrites method when no rewrites were added
     *
     * @param array $storeIds
     * @param string $requestPath
     * @param string $targetPath
     * @dataProvider addRewritesNoRewritesAddedDataProvider
     */
    public function testAddRewritesNoRewritesAdded($storeIds, $requestPath, $targetPath)
    {
        $this->urlRewriteFactoryMock->expects($this->never())
            ->method('create');
        $this->loggerMock->expects($this->never())
            ->method('warning');
        $this->service->addRewrites($storeIds, $requestPath, $targetPath);
    }

    /**
     * @return array
     */
    public function addRewritesNoRewritesAddedDataProvider()
    {
        return [
            [
                'storeIds' => [],
                'requestPath' => '',
                'targetPath' => '',
            ],
            [
                'storeIds' => [],
                'requestPath' => 'all-reviews',
                'targetPath' => 'aw_advanced_reviews/review_page/index',
            ],
            [
                'storeIds' => [1,2,3],
                'requestPath' => '',
                'targetPath' => 'aw_advanced_reviews/review_page/index',
            ],
            [
                'storeIds' => [1,2,3],
                'requestPath' => 'all-reviews',
                'targetPath' => '',
            ],
            [
                'storeIds' => [1,2,3],
                'requestPath' => '',
                'targetPath' => '',
            ],
        ];
    }

    /**
     * Test addRewrites method with exception
     */
    public function testAddRewritesException()
    {
        $storeIds = [1,2,3];
        $requestPath = 'all-reviews';
        $targetPath = 'aw_advanced_reviews/review_page/index';
        $exceptionMessage = 'Exception message';
        $exceptionIndex = count($storeIds) - 1;

        for ($index = 0; $index < count($storeIds); $index++) {
            $urlRewriteMock = $this->createPartialMock(
                UrlRewrite::class,
                [
                    'setStoreId',
                    'setEntityType',
                    'setRequestPath',
                    'setTargetPath',
                    'setRedirectType',
                    'save',
                ]
            );
            $urlRewriteMock->expects($this->once())
                ->method('setStoreId')
                ->willReturnSelf();
            $urlRewriteMock->expects($this->once())
                ->method('setEntityType')
                ->with(CustomEntity::URL_REWRITE_ENTITY_TYPE)
                ->willReturnSelf();
            $urlRewriteMock->expects($this->once())
                ->method('setRequestPath')
                ->with($requestPath)
                ->willReturnSelf();
            $urlRewriteMock->expects($this->once())
                ->method('setTargetPath')
                ->with($targetPath)
                ->willReturnSelf();
            $urlRewriteMock->expects($this->once())
                ->method('setRedirectType')
                ->with(CustomEntity::REDIRECT_TYPE)
                ->willReturnSelf();
            if ($index == $exceptionIndex) {
                $urlRewriteMock->expects($this->once())
                    ->method('save')
                    ->willThrowException(new \Exception($exceptionMessage));
            } else {
                $urlRewriteMock->expects($this->once())
                    ->method('save')
                    ->willReturnSelf();
            }
            $this->urlRewriteFactoryMock->expects($this->at($index))
                ->method('create')
                ->willReturn($urlRewriteMock);
        }
        $this->loggerMock->expects($this->once())
            ->method('warning')
            ->with($exceptionMessage);
        $this->service->addRewrites($storeIds, $requestPath, $targetPath);
    }
}
