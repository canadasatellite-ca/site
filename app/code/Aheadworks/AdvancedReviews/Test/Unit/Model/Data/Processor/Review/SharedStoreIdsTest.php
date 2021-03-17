<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\Processor\Review\SharedStoreIds;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Data\Processor\Review\SharedStoreIds
 */
class SharedStoreIdsTest extends TestCase
{
    /**
     * @var SharedStoreIds
     */
    private $processor;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->processor = $objectManager->getObject(
            SharedStoreIds::class,
            []
        );
    }

    /**
     * Test for process method
     *
     * @param array $data
     * @param array $result
     * @dataProvider processDataProvider
     */
    public function testProcess($data, $result)
    {
        $this->assertEquals($result, $this->processor->process($data));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [
                'data' => [],
                'result' => [
                    ReviewInterface::SHARED_STORE_IDS => [],
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::SHARED_STORE_IDS => [],
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::SHARED_STORE_IDS => [],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::SHARED_STORE_IDS => [],
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::SHARED_STORE_IDS => [1,2],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::SHARED_STORE_IDS => [1,2],
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::SHARED_STORE_IDS => "",
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::SHARED_STORE_IDS => [],
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::SHARED_STORE_IDS => 0,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::SHARED_STORE_IDS => [],
                ],
            ],
        ];
    }
}
