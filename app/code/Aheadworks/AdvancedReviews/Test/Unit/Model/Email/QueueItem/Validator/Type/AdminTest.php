<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\QueueItem\Validator\Type;

use Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Type\Admin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Type\Admin
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

        $this->validator = $objectManager->getObject(Admin::class, []);
    }

    /**
     * Test isValid method
     */
    public function testIsValid()
    {
        $queueItemMock = $this->createMock(QueueItemInterface::class);
        $this->assertTrue($this->validator->isValid($queueItemMock));
    }
}
