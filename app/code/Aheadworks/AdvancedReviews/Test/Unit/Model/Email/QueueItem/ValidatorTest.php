<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\QueueItem;

use Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type as EmailTypeSource;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Pool as ValidatorPool;
use Aheadworks\AdvancedReviews\Model\Email\QueueItem\ValidatorInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator
 */
class ValidatorTest extends TestCase
{
    const TEST_CORRECT_EMAIL_TYPE = EmailTypeSource::ADMIN_NEW_REVIEW;
    const TEST_INCORRECT_EMAIL_TYPE = -1;

    /**
     * @var Validator
     */
    private $model;

    /**
     * @var ValidatorPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $validatorPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->validatorPoolMock = $this->createMock(ValidatorPool::class);

        $this->model = $objectManager->getObject(
            Validator::class,
            [
                'validatorPool' => $this->validatorPoolMock,
            ]
        );
    }

    /**
     * Testing of isValid method
     *
     * @param int $emailType
     * @param bool $result
     * @dataProvider isValidDataProvider
     */
    public function testIsValidEmail($emailType, $result)
    {
        $queueItemMock = $this->createMock(QueueItemInterface::class);
        $queueItemMock->expects($this->any())
            ->method('getType')
            ->willReturn($emailType);

        $validatorMock = $this->createMock(ValidatorInterface::class);
        $validatorMock->expects($this->any())
            ->method('isValid')
            ->with($queueItemMock)
            ->willReturn($result);

        $this->validatorPoolMock->expects($this->once())
            ->method('getValidatorByType')
            ->willReturnMap(
                [
                    [
                        self::TEST_CORRECT_EMAIL_TYPE,
                        $validatorMock
                    ],
                    [
                        self::TEST_INCORRECT_EMAIL_TYPE,
                        null
                    ],
                ]
            );

        $this->assertEquals(
            $result,
            $this->model->isValid(
                $queueItemMock
            )
        );
    }

    /**
     * Data provider for isValid
     *
     * @return array
     */
    public function isValidDataProvider()
    {
        return [
            [
                'emailType' => self::TEST_CORRECT_EMAIL_TYPE,
                'result' => true
            ],
            [
                'emailType' => self::TEST_CORRECT_EMAIL_TYPE,
                'result' => false
            ],
            [
                'emailType' => self::TEST_INCORRECT_EMAIL_TYPE,
                'result' => false
            ],
        ];
    }
}
