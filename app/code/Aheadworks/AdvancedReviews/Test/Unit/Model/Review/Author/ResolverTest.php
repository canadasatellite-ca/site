<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Author;

use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Pool as ResolverPool;
use Aheadworks\AdvancedReviews\Model\Source\Review\AuthorType;
use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\ResolverInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Author\Resolver
 */
class ResolverTest extends TestCase
{
    const TEST_CORRECT_AUTHOR_TYPE = AuthorType::CUSTOMER;
    const TEST_INCORRECT_AUTHOR_TYPE = -1;
    const TEST_CUSTOMER_ID = 2;

    /**
     * @var Resolver
     */
    private $model;

    /**
     * @var ResolverPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resolverPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resolverPoolMock = $this->createMock(ResolverPool::class);

        $this->model = $objectManager->getObject(
            Resolver::class,
            [
                'resolverPool' => $this->resolverPoolMock,
            ]
        );
    }

    /**
     * Testing of getBackendUrl method
     *
     * @param int $authorType
     * @param int|null $customerId
     * @param string $authorUrl
     * @dataProvider getBackendUrlDataProvider
     */
    public function testGetBackendUrl($authorType, $customerId, $authorUrl)
    {
        $resolverMock = $this->createMock(ResolverInterface::class);
        $resolverMock->expects($this->any())
            ->method('getBackendUrl')
            ->willReturnMap(
                [
                    [
                        self::TEST_CUSTOMER_ID,
                        $authorUrl
                    ],
                    [
                        null,
                        ""
                    ],
                ]
            );

        $this->resolverPoolMock->expects($this->once())
            ->method('getResolverByAuthorType')
            ->willReturnMap(
                [
                    [
                        self::TEST_CORRECT_AUTHOR_TYPE,
                        $resolverMock
                    ],
                    [
                        self::TEST_INCORRECT_AUTHOR_TYPE,
                        null
                    ],
                ]
            );

        $this->assertEquals(
            $authorUrl,
            $this->model->getBackendUrl(
                $authorType,
                $customerId
            )
        );
    }

    /**
     * Data provider for getBackendUrl
     *
     * @return array
     */
    public function getBackendUrlDataProvider()
    {
        return [
            [
                self::TEST_CORRECT_AUTHOR_TYPE,
                self::TEST_CUSTOMER_ID,
                "www.store.com/admin/customer/index/edit/1"
            ],
            [
                self::TEST_CORRECT_AUTHOR_TYPE,
                null,
                ""
            ],
            [
                self::TEST_INCORRECT_AUTHOR_TYPE,
                self::TEST_CUSTOMER_ID,
                ""
            ],
            [
                self::TEST_INCORRECT_AUTHOR_TYPE,
                null,
                ""
            ],
        ];
    }

    /**
     * Testing of getBackendLabel method
     *
     * @param int $authorType
     * @param int|null $customerId
     * @param string $authorLabel
     * @dataProvider getBackendLabelDataProvider
     */
    public function testGetBackendLabel($authorType, $customerId, $authorLabel)
    {
        $resolverMock = $this->createMock(ResolverInterface::class);
        $resolverMock->expects($this->any())
            ->method('getBackendLabel')
            ->with($customerId)
            ->willReturn($authorLabel);

        $this->resolverPoolMock->expects($this->once())
            ->method('getResolverByAuthorType')
            ->willReturnMap(
                [
                    [
                        self::TEST_CORRECT_AUTHOR_TYPE,
                        $resolverMock
                    ],
                    [
                        self::TEST_INCORRECT_AUTHOR_TYPE,
                        null
                    ],
                ]
            );

        $this->assertEquals(
            $authorLabel,
            $this->model->getBackendLabel(
                $authorType,
                $customerId
            )
        );
    }

    /**
     * @return array
     */
    public function getBackendLabelDataProvider()
    {
        return [
            [
                self::TEST_CORRECT_AUTHOR_TYPE,
                self::TEST_CUSTOMER_ID,
                "test backend label 1"
            ],
            [
                self::TEST_CORRECT_AUTHOR_TYPE,
                null,
                "test backend label 2"
            ],
            [
                self::TEST_INCORRECT_AUTHOR_TYPE,
                self::TEST_CUSTOMER_ID,
                ''
            ],
            [
                self::TEST_INCORRECT_AUTHOR_TYPE,
                null,
                ''
            ],
        ];
    }

    /**
     * Testing of getName method
     *
     * @param int $authorType
     * @param string|null $authorName
     * @dataProvider getNameDataProvider
     */
    public function testGetName($authorType, $authorName)
    {
        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->any())
            ->method('getAuthorType')
            ->willReturn($authorType);

        $resolverMock = $this->createMock(ResolverInterface::class);
        $resolverMock->expects($this->any())
            ->method('getName')
            ->with($review)
            ->willReturn($authorName);

        $this->resolverPoolMock->expects($this->once())
            ->method('getResolverByAuthorType')
            ->willReturnMap(
                [
                    [
                        self::TEST_CORRECT_AUTHOR_TYPE,
                        $resolverMock
                    ],
                    [
                        self::TEST_INCORRECT_AUTHOR_TYPE,
                        null
                    ],
                ]
            );

        $this->assertEquals(
            $authorName,
            $this->model->getName(
                $review
            )
        );
    }

    /**
     * @return array
     */
    public function getNameDataProvider()
    {
        return [
            [
                self::TEST_CORRECT_AUTHOR_TYPE,
                "test name",
            ],
            [
                self::TEST_INCORRECT_AUTHOR_TYPE,
                null,
            ],
        ];
    }

    /**
     * Testing of getEmail method
     *
     * @param int $authorType
     * @param string|null $authorEmail
     * @dataProvider getEmailDataProvider
     */
    public function testGetEmail($authorType, $authorEmail)
    {
        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->any())
            ->method('getAuthorType')
            ->willReturn($authorType);

        $resolverMock = $this->createMock(ResolverInterface::class);
        $resolverMock->expects($this->any())
            ->method('getEmail')
            ->with($review)
            ->willReturn($authorEmail);

        $this->resolverPoolMock->expects($this->once())
            ->method('getResolverByAuthorType')
            ->willReturnMap(
                [
                    [
                        self::TEST_CORRECT_AUTHOR_TYPE,
                        $resolverMock
                    ],
                    [
                        self::TEST_INCORRECT_AUTHOR_TYPE,
                        null
                    ],
                ]
            );

        $this->assertEquals(
            $authorEmail,
            $this->model->getEmail(
                $review
            )
        );
    }

    /**
     * @return array
     */
    public function getEmailDataProvider()
    {
        return [
            [
                self::TEST_CORRECT_AUTHOR_TYPE,
                "test name",
            ],
            [
                self::TEST_INCORRECT_AUTHOR_TYPE,
                null,
            ],
        ];
    }
}
