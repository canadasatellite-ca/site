<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Validator\Agreements\AuthorType;

use Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\AuthorType\Admin;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Review;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\AuthorType\Admin
 */
class AdminTest extends TestCase
{
    /**
     * @var Admin
     */
    private $validator;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->validator = $objectManager->getObject(
            Admin::class,
            []
        );
    }

    /**
     * Test for isValid method
     */
    public function testIsValid()
    {
        $review = $this->createMock(Review::class);

        $this->assertTrue($this->validator->isValid($review));
    }
}
