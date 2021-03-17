<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\AuthorType;

use Aheadworks\AdvancedReviews\Model\Review\Author\Type\Resolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Store\Model\Store;
use Aheadworks\AdvancedReviews\Model\Source\Review\AuthorType;

/**
 * Class ResolverTest
 *
 * @package Aheadworks\AdvancedReviews\Test\Unit\Model\Review\AuthorType
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->model = $objectManager->getObject(
            Resolver::class
        );
    }

    /**
     * Testing of resolveAuthorType method
     *
     * @param int $storeId
     * @param int|null $customerId
     * @param int $authorType
     * @dataProvider testResolveAuthorTypeDataProvider
     */
    public function testResolveAuthorType($storeId, $customerId, $authorType)
    {
        $this->assertSame($authorType, $this->model->resolveAuthorType($storeId, $customerId));
    }

    /**
     * Data provider for resolveAuthorType
     *
     * @return array
     */
    public function testResolveAuthorTypeDataProvider()
    {
        return [
            [
                1,
                1,
                AuthorType::CUSTOMER
            ],
            [
                Store::DEFAULT_STORE_ID,
                1,
                AuthorType::CUSTOMER
            ],
            [
                1,
                null,
                AuthorType::GUEST
            ],
            [
                1,
                0,
                AuthorType::GUEST
            ],
            [
                Store::DEFAULT_STORE_ID,
                null,
                AuthorType::ADMIN
            ],
        ];
    }
}
