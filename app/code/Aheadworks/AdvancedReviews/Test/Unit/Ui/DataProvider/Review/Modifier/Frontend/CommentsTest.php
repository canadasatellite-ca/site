<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\Comments;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Model\Review\Comment\Resolver as CommentResolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\AdvancedReviews\Model\DateTime\Formatter as DateTimeFormatter;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\Comments
 */
class CommentsTest extends TestCase
{
    /**
     * @var Comments
     */
    private $modifier;

    /**
     * @var DateTimeFormatter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTimeFormatterMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var CommentResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->dateTimeFormatterMock = $this->createMock(DateTimeFormatter::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->commentResolverMock = $this->createMock(CommentResolver::class);

        $this->commentResolverMock->expects($this->any())
            ->method('isNeedToShowOnFrontend')
            ->willReturnMap(
                [
                    [
                        1,
                        true
                    ],
                    [
                        2,
                        false
                    ]
                ]
            );

        $this->modifier = $objectManager->getObject(
            Comments::class,
            [
                'dateTimeFormatter' => $this->dateTimeFormatterMock,
                'storeManager' => $this->storeManagerMock,
                'commentResolver' => $this->commentResolverMock,
            ]
        );
    }

    /**
     * Test modifyMeta method
     *
     * @param array $meta
     * @param array $result
     * @dataProvider modifyMetaDataProvider
     */
    public function testModifyMeta($meta, $result)
    {
        $this->assertSame($result, $this->modifier->modifyMeta($meta));
    }

    /**
     * @return array
     */
    public function modifyMetaDataProvider()
    {
        return [
            [
                [],
                []
            ],
            [
                ['some meta data'],
                ['some meta data'],
            ],
        ];
    }

    /**
     * Test modifyData method
     *
     * @param array $data
     * @param array $result
     * @dataProvider modifyDataDataProvider
     */
    public function testModifyData($data, $result)
    {
        $currentStoreId = 1;

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($currentStoreId);
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->with(true)
            ->willReturn($storeMock);

        $this->commentResolverMock->expects($this->any())
            ->method('getNicknameForFrontend')
            ->with('some_nickname', $currentStoreId)
            ->willReturn('prepared_nickname');

        $this->dateTimeFormatterMock->expects($this->any())
            ->method('getLocalizedDate')
            ->with('date_time_in_db_format', $currentStoreId)
            ->willReturn('localized_datetime');

        $this->assertSame($result, $this->modifier->modifyData($data));
    }

    /**
     * @return array
     */
    public function modifyDataDataProvider()
    {
        return [
            [
                [],
                []
            ],
            [
                [
                    ReviewInterface::ID => null,
                ],
                [
                    ReviewInterface::ID => null,
                ],
            ],
            [
                [
                    ReviewInterface::ID => '',
                ],
                [
                    ReviewInterface::ID => '',
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => []
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => []
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => []
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => [
                        ''
                    ]
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => []
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => [
                        [
                            ''
                        ],
                    ]
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => []
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => [
                        [
                            ''
                        ],
                        [
                            CommentInterface::ID => 1
                        ],
                    ]
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => []
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => [
                        [
                            ''
                        ],
                        [
                            CommentInterface::ID => 1
                        ],
                    ]
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => []
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => [
                        [
                            ''
                        ],
                        [
                            CommentInterface::ID => 1
                        ],
                        [
                            CommentInterface::ID => 2,
                            CommentInterface::STATUS => 2
                        ],
                    ]
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => []
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => [
                        [
                            ''
                        ],
                        [
                            CommentInterface::ID => 1,
                        ],
                        [
                            CommentInterface::ID => 2,
                            CommentInterface::STATUS => 2,
                        ],
                        [
                            CommentInterface::ID => 3,
                            CommentInterface::STATUS => 2,
                            CommentInterface::TYPE => 'comment_type',
                            CommentInterface::CONTENT => 'comment_content',
                            CommentInterface::NICKNAME => 'some_nickname',
                            CommentInterface::CREATED_AT => 'date_time_in_db_format',
                        ],
                    ]
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => []
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => [
                        [
                            ''
                        ],
                        [
                            CommentInterface::ID => 1,
                        ],
                        [
                            CommentInterface::ID => 2,
                            CommentInterface::STATUS => 2,
                        ],
                        [
                            CommentInterface::ID => 3,
                            CommentInterface::STATUS => 2,
                            CommentInterface::TYPE => 'comment_type',
                            CommentInterface::CONTENT => 'comment_content',
                            CommentInterface::NICKNAME => 'some_nickname',
                            CommentInterface::CREATED_AT => 'date_time_in_db_format',
                        ],
                        [
                            CommentInterface::ID => 4,
                            CommentInterface::STATUS => 1,
                            CommentInterface::TYPE => 'comment_type',
                            CommentInterface::CONTENT => 'comment_content',
                            CommentInterface::NICKNAME => 'some_nickname',
                            CommentInterface::CREATED_AT => 'date_time_in_db_format',
                        ],
                    ]
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::COMMENTS => [
                        [
                            CommentInterface::ID => 4,
                            CommentInterface::TYPE => 'comment_type',
                            CommentInterface::CONTENT => 'comment_content',
                            CommentInterface::NICKNAME => 'prepared_nickname',
                            CommentInterface::CREATED_AT => 'localized_datetime',
                        ],
                    ]
                ],
            ],
        ];
    }

    /**
     * Test modifyData method when no current store detected
     *
     * @param array $data
     * @param array $result
     * @dataProvider modifyDataDataProvider
     */
    public function testModifyDataNoCurrentStore($data, $result)
    {
        $currentStoreId = null;

        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->with(true)
            ->willThrowException(new NoSuchEntityException());

        $this->commentResolverMock->expects($this->any())
            ->method('getNicknameForFrontend')
            ->with('some_nickname', $currentStoreId)
            ->willReturn('prepared_nickname');

        $this->dateTimeFormatterMock->expects($this->any())
            ->method('getLocalizedDate')
            ->with('date_time_in_db_format', $currentStoreId)
            ->willReturn('localized_datetime');

        $this->assertSame($result, $this->modifier->modifyData($data));
    }
}
