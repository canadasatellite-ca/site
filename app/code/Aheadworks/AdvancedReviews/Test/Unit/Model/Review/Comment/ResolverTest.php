<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Comment;

use Aheadworks\AdvancedReviews\Model\Review\Comment\Resolver;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Status as CommentStatusSource;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Comment\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);
        $this->configMock->expects($this->any())
            ->method('getAdminCommentCaption')
            ->willReturnMap(
                [
                    [
                        null,
                        'Default admin comment caption'
                    ],
                    [
                        1,
                        'Admin comment caption (EN)'
                    ],
                    [
                        2,
                        'Admin comment caption (US)'
                    ],
                ]
            );

        $this->resolver = $objectManager->getObject(
            Resolver::class,
            [
                'config' => $this->configMock,
            ]
        );
    }

    /**
     * Test isNeedToShowOnFrontend method
     *
     * @param int $status
     * @param bool $result
     * @dataProvider isNeedToShowOnFrontendDataProvider
     */
    public function testIsNeedToShowOnFrontend($status, $result)
    {
        $this->assertEquals($result, $this->resolver->isNeedToShowOnFrontend($status));
    }

    /**
     * @return array
     */
    public function isNeedToShowOnFrontendDataProvider()
    {
        return [
            [
                'status' => CommentStatusSource::APPROVED,
                'result' => true,
            ],
            [
                'status' => '',
                'result' => false,
            ],
            [
                'status' => 0,
                'result' => false,
            ],
            [
                'status' => -1,
                'result' => false,
            ],
            [
                'status' => CommentStatusSource::PENDING,
                'result' => false,
            ],
            [
                'status' => CommentStatusSource::NOT_APPROVED,
                'result' => false,
            ],
        ];
    }

    /**
     * Test getNicknameForBackend method
     *
     * @param string $nickname
     * @param string $result
     * @dataProvider getNicknameForBackendDataProvider
     */
    public function testGetNicknameForBackend($nickname, $result)
    {
        $this->assertEquals($result, $this->resolver->getNicknameForBackend($nickname));
    }

    /**
     * @return array
     */
    public function getNicknameForBackendDataProvider()
    {
        return [
            [
                'nickname' => 'Some nickname from frontend',
                'result' => 'Some nickname from frontend',
            ],
            [
                'nickname' => '',
                'result' => 'Default admin comment caption',
            ],
            [
                'nickname' => null,
                'result' => 'Default admin comment caption',
            ],
        ];
    }

    /**
     * Test getNicknameForFrontend method
     *
     * @param string $nickname
     * @param int|null $storeId
     * @param string $result
     * @dataProvider getNicknameForFrontendDataProvider
     */
    public function testGetNicknameForFrontend($nickname, $storeId, $result)
    {
        $this->assertEquals($result, $this->resolver->getNicknameForFrontend($nickname, $storeId));
    }

    /**
     * @return array
     */
    public function getNicknameForFrontendDataProvider()
    {
        return [
            [
                'nickname' => 'Some nickname from frontend',
                'storeId' => null,
                'result' => 'Some nickname from frontend',
            ],
            [
                'nickname' => 'Some nickname from frontend',
                'storeId' => 1,
                'result' => 'Some nickname from frontend',
            ],
            [
                'nickname' => 'Some nickname from frontend',
                'storeId' => 2,
                'result' => 'Some nickname from frontend',
            ],
            [
                'nickname' => 'Some nickname from frontend',
                'storeId' => -1,
                'result' => 'Some nickname from frontend',
            ],
            [
                'nickname' => '',
                'storeId' => null,
                'result' => __('Response from %1', 'Default admin comment caption'),
            ],
            [
                'nickname' => '',
                'storeId' => 1,
                'result' => __('Response from %1', 'Admin comment caption (EN)'),
            ],
            [
                'nickname' => '',
                'storeId' => 2,
                'result' => __('Response from %1', 'Admin comment caption (US)'),
            ],
            [
                'nickname' => null,
                'storeId' => null,
                'result' => __('Response from %1', 'Default admin comment caption'),
            ],
            [
                'nickname' => null,
                'storeId' => 1,
                'result' => __('Response from %1', 'Admin comment caption (EN)'),
            ],
            [
                'nickname' => null,
                'storeId' => 2,
                'result' => __('Response from %1', 'Admin comment caption (US)'),
            ],
        ];
    }
}
