<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Author\Resolver\Type;

use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Type\Admin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Type\Admin
 */
class AdminTest extends TestCase
{
    /**
     * @var Admin
     */
    private $resolver;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resolver = $objectManager->getObject(Admin::class, []);
    }

    /**
     * Test getBackendLabel method
     *
     * @param int|null $customerId
     * @dataProvider getBackendLabelDataProvider
     */
    public function testGetBackendLabel($customerId)
    {
        $backendLabel = __('Administrator');
        $this->assertEquals((string)$backendLabel, (string)$this->resolver->getBackendLabel($customerId));
    }

    public function getBackendLabelDataProvider()
    {
        return [
            [
                'customerId' => 2,
            ],
            [
                'customerId' => null,
            ],
        ];
    }

    /**
     * Test getBackendUrl method
     *
     * @param int|null $customerId
     * @dataProvider getBackendUrlDataProvider
     */
    public function testGetBackendUrl($customerId)
    {
        $url = '';
        $this->assertEquals($url, $this->resolver->getBackendUrl($customerId));
    }

    public function getBackendUrlDataProvider()
    {
        return [
            [
                'customerId' => 2,
            ],
            [
                'customerId' => null,
            ],
        ];
    }

    /**
     * Test getName method
     */
    public function testGetName()
    {
        $name = null;
        $review = $this->createMock(ReviewInterface::class);
        $this->assertEquals($name, $this->resolver->getName($review));
    }

    /**
     * Test getEmail method
     */
    public function testGetEmail()
    {
        $email = null;
        $review = $this->createMock(ReviewInterface::class);
        $this->assertEquals($email, $this->resolver->getEmail($review));
    }
}
