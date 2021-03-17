<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Author\Resolver;

use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Pool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\ResolverInterface;
use Magento\Framework\DataObject;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Pool
 */
class PoolTest extends TestCase
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->pool = $objectManager->getObject(Pool::class, []);
    }

    /**
     * Test getResolverByAuthorType method
     *
     * @param ResolverInterface[] $resolvers
     * @param int $authorType
     * @param ResolverInterface|null $result
     * @throws \ReflectionException
     * @dataProvider getResolverByAuthorTypeDataProvider
     */
    public function testGetResolverByAuthorType($resolvers, $authorType, $result)
    {
        $this->setProperty('resolvers', $resolvers);

        $this->assertSame($result, $this->pool->getResolverByAuthorType($authorType));
    }

    /**
     * @return array
     */
    public function getResolverByAuthorTypeDataProvider()
    {
        $resolverMock = $this->createMock(ResolverInterface::class);
        $badResolver = $this->createMock(DataObject::class);
        $resolvers = [
            'resolver_one' => $resolverMock,
            'resolver_bad' => $badResolver
        ];
        return [
            [
                'resolvers' => $resolvers,
                'authorType' => 'resolver_one',
                'result' => $resolverMock
            ],
            [
                'resolvers' => $resolvers,
                'authorType' => 'unknown_type',
                'result' => null
            ],
            [
                'resolvers' => $resolvers,
                'authorType' => 'resolver_bad',
                'result' => null
            ],
        ];
    }

    /**
     * Set property
     *
     * @param string $propertyName
     * @param mixed $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setProperty($propertyName, $value)
    {
        $class = new \ReflectionClass($this->pool);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->pool, $value);

        return $this;
    }
}
