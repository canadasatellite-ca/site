<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Service;

use Aheadworks\AdvancedReviews\Model\Service\ReviewService;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Source\Review\AuthorType;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\Status;
use Aheadworks\AdvancedReviews\Model\Review\Comment\Processor as CommentProcessor;
use Aheadworks\AdvancedReviews\Model\Review\NotificationManager as ReviewNotificationManager;
use Aheadworks\AdvancedReviews\Model\Review\ProcessorInterface;
use Zend_Validate_Interface;
use Zend_Validate_Exception;
use Aheadworks\AdvancedReviews\Model\Source\Review\RatingValue as ReviewRatingValueSource;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Service\ReviewService
 */
class ReviewServiceTest extends TestCase
{
    /**
     * @var ReviewService
     */
    private $service;

    /**
     * @var ReviewRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewRepositoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var CommentProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentProcessorMock;

    /**
     * @var ReviewNotificationManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewNotificationManagerMock;

    /**
     * @var ProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $creationProcessorMock;

    /**
     * @var Zend_Validate_Interface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $creationValidatorMock;

    /**
     * @var array
     */
    private $reviewData = [
        ReviewInterface::ID => 1,
        ReviewInterface::STORE_ID => 1
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->reviewRepositoryMock = $this->getMockForAbstractClass(ReviewRepositoryInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['mergeDataObjects']);
        $this->commentProcessorMock = $this->createPartialMock(CommentProcessor::class, ['processAdminComment']);
        $this->reviewNotificationManagerMock = $this->createMock(ReviewNotificationManager::class);
        $this->creationProcessorMock = $this->createMock(ProcessorInterface::class);
        $this->creationValidatorMock = $this->createMock(Zend_Validate_Interface::class);

        $this->service = $objectManager->getObject(
            ReviewService::class,
            [
                'reviewRepository' => $this->reviewRepositoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'commentProcessor' => $this->commentProcessorMock,
                'reviewNotificationManager' => $this->reviewNotificationManagerMock,
                'creationProcessor' => $this->creationProcessorMock,
                'creationValidator' => $this->creationValidatorMock,
            ]
        );
    }

    /**
     * Test create review
     *
     * @dataProvider getCreateReviewDataProvider
     * @param ReviewInterface|\PHPUnit_Framework_MockObject_MockObject $review
     * @param bool $addAdminNotification
     * @param bool $addAdminCriticalNotification
     */
    public function testCreateReview($review, $addAdminNotification, $addAdminCriticalNotification)
    {
        $callsCount = $addAdminNotification ? 1 : 0;
        $criticalNotificationCallsCount = $addAdminCriticalNotification ? 1 : 0;

        $this->creationProcessorMock->expects($this->once())
            ->method('process')
            ->with($review)
            ->willReturn($review);
        $this->creationValidatorMock->expects($this->once())
            ->method('isValid')
            ->with($review)
            ->willReturn(true);
        $this->creationValidatorMock->expects($this->never())
            ->method('getMessages');
        $this->reviewRepositoryMock->expects($this->once())
            ->method('save')
            ->with($review)
            ->willReturn($review);
        $this->commentProcessorMock->expects($this->once())
            ->method('processAdminComment')
            ->with($review);

        $this->reviewNotificationManagerMock->expects($this->exactly($callsCount))
            ->method('notifyAdminAboutNewReview')
            ->with($review);
        $this->reviewNotificationManagerMock->expects($this->exactly($criticalNotificationCallsCount))
            ->method('notifyAdminAboutNewCriticalReview')
            ->with($review);

        $this->assertSame($review, $this->service->createReview($review));
    }

    /**
     * @return array
     */
    public function getCreateReviewDataProvider()
    {
        return [
            [
                $this->getReviewMock(
                    AuthorType::ADMIN,
                    Status::PENDING,
                    null,
                    ReviewRatingValueSource::FIVE_STAR_VALUE
                ),
                false,
                false
            ],
            [
                $this->getReviewMock(
                    AuthorType::ADMIN,
                    Status::PENDING,
                    null,
                    ReviewRatingValueSource::ONE_STAR_VALUE
                ),
                false,
                false
            ],
            [
                $this->getReviewMock(
                    AuthorType::GUEST,
                    Status::PENDING,
                    null,
                    ReviewRatingValueSource::FIVE_STAR_VALUE
                ),
                true,
                false
            ],
            [
                $this->getReviewMock(
                    AuthorType::GUEST,
                    Status::PENDING,
                    null,
                    ReviewRatingValueSource::ONE_STAR_VALUE
                ),
                true,
                true
            ],
        ];
    }

    /**
     * Test create review with exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Error!
     */
    public function testCreateReviewExp()
    {
        $review = $this->getReviewMock(AuthorType::GUEST, Status::PENDING, null);
        $exception = new CouldNotSaveException(__('Error!'));

        $this->creationProcessorMock->expects($this->once())
            ->method('process')
            ->with($review)
            ->willReturn($review);
        $this->creationValidatorMock->expects($this->once())
            ->method('isValid')
            ->with($review)
            ->willReturn(true);
        $this->creationValidatorMock->expects($this->never())
            ->method('getMessages');
        $this->reviewRepositoryMock->expects($this->once())
            ->method('save')
            ->with($review)
            ->willThrowException($exception);
        $this->commentProcessorMock->expects($this->never())
            ->method('processAdminComment');
        $this->reviewNotificationManagerMock->expects($this->never())
            ->method('notifyAdminAboutNewReview');

        $this->service->createReview($review);
    }

    /**
     * Test create review with validation exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testCreateReviewValidationException()
    {
        $review = $this->getReviewMock(AuthorType::GUEST, Status::PENDING, null);
        $validationMessages = ['Validation error 1', 'Validation error 2'];

        $this->creationProcessorMock->expects($this->once())
            ->method('process')
            ->with($review)
            ->willReturn($review);
        $this->creationValidatorMock->expects($this->once())
            ->method('isValid')
            ->with($review)
            ->willReturn(false);
        $this->creationValidatorMock->expects($this->once())
            ->method('getMessages')
            ->willReturn($validationMessages);

        $this->reviewRepositoryMock->expects($this->never())
            ->method('save');
        $this->commentProcessorMock->expects($this->never())
            ->method('processAdminComment');
        $this->reviewNotificationManagerMock->expects($this->never())
            ->method('notifyAdminAboutNewReview');

        $this->service->createReview($review);
    }

    /**
     * Test create review with validation inner exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Review validation error occurred
     */
    public function testCreateReviewValidationInnerException()
    {
        $review = $this->getReviewMock(AuthorType::GUEST, Status::PENDING, null);

        $this->creationProcessorMock->expects($this->once())
            ->method('process')
            ->with($review)
            ->willReturn($review);
        $this->creationValidatorMock->expects($this->once())
            ->method('isValid')
            ->with($review)
            ->willThrowException(new Zend_Validate_Exception());
        $this->creationValidatorMock->expects($this->never())
            ->method('getMessages');

        $this->reviewRepositoryMock->expects($this->never())
            ->method('save');
        $this->commentProcessorMock->expects($this->never())
            ->method('processAdminComment');
        $this->reviewNotificationManagerMock->expects($this->never())
            ->method('notifyAdminAboutNewReview');

        $this->service->createReview($review);
    }

    /**
     * Test update review
     *
     * @dataProvider getUpdateReviewDataProvider
     * @param ReviewInterface|\PHPUnit_Framework_MockObject_MockObject $review
     * @param ReviewInterface|\PHPUnit_Framework_MockObject_MockObject $reviewToMerge
     * @param bool $addApprovedNotification
     */
    public function testUpdateReview($review, $reviewToMerge, $addApprovedNotification)
    {
        $callsCount = $addApprovedNotification ? 1 : 0;

        $this->reviewRepositoryMock->expects($this->once())
            ->method('save')
            ->with($review)
            ->willReturn($review);
        $this->reviewRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->reviewData[ReviewInterface::ID])
            ->willReturn($reviewToMerge);
        $this->commentProcessorMock->expects($this->once())
            ->method('processAdminComment')
            ->with($review);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('mergeDataObjects')
            ->with(ReviewInterface::class, $review, $reviewToMerge)
            ->willReturn($review);

        $this->reviewNotificationManagerMock->expects($this->exactly($callsCount))
            ->method('notifyAuthorAboutReviewApproval')
            ->with($review);

        $this->assertSame($review, $this->service->updateReview($review));
    }

    /**
     * @return array
     */
    public function getUpdateReviewDataProvider()
    {
        return [
            [
                $this->getReviewMock(AuthorType::ADMIN, Status::PENDING, null),
                $this->getReviewMock(AuthorType::ADMIN, Status::PENDING, null),
                false
            ],
            [
                $this->getReviewMock(AuthorType::CUSTOMER, Status::APPROVED, 1),
                $this->getReviewMock(AuthorType::CUSTOMER, Status::PENDING, 1),
                true
            ]
        ];
    }

    /**
     * Test update review with exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Error!
     */
    public function testUpdateReviewExp()
    {
        $review = $this->getReviewMock(AuthorType::CUSTOMER, Status::APPROVED, 1);
        $exception = new NoSuchEntityException(__('Error!'));

        $this->reviewRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->reviewData[ReviewInterface::ID])
            ->willThrowException($exception);
        $this->reviewRepositoryMock->expects($this->never())
            ->method('save');
        $this->commentProcessorMock->expects($this->never())
            ->method('processAdminComment');
        $this->reviewNotificationManagerMock->expects($this->never())
            ->method('notifyAuthorAboutReviewApproval');

        $this->service->updateReview($review);
    }

    /**
     * Test delete review by id
     */
    public function testDeleteReviewById()
    {
        $review = $this->getReviewMock(AuthorType::CUSTOMER, Status::APPROVED, 1);
        $result = true;

        $this->reviewRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->reviewData[ReviewInterface::ID])
            ->willReturn($review);
        $this->reviewRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($review)
            ->willReturn($result);

        $this->assertSame($result, $this->service->deleteReviewById($this->reviewData[ReviewInterface::ID]));
    }

    /**
     * Test delete review by id with exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Error!
     */
    public function testDeleteReviewByIdExp()
    {
        $review = $this->getReviewMock(AuthorType::CUSTOMER, Status::APPROVED, 1);
        $exception = new CouldNotDeleteException(__('Error!'));

        $this->reviewRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->reviewData[ReviewInterface::ID])
            ->willReturn($review);
        $this->reviewRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($review)
            ->willThrowException($exception);

        $this->service->deleteReviewById($this->reviewData[ReviewInterface::ID]);
    }

    /**
     * Get review mock
     *
     * @param int $authorType
     * @param int $status
     * @param int|null $customerId
     * @param int|null $rating
     * @return ReviewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getReviewMock($authorType, $status, $customerId, $rating = null)
    {
        $reviewMock = $this->getMockForAbstractClass(ReviewInterface::class);

        $reviewMock->expects($this->any())
            ->method('getId')
            ->willReturn($this->reviewData[ReviewInterface::ID]);
        $reviewMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($this->reviewData[ReviewInterface::STORE_ID]);
        $reviewMock->expects($this->any())
            ->method('getStatus')
            ->willReturn($status);
        $reviewMock->expects($this->atMost(2))
            ->method('getCustomerId')
            ->willReturn($customerId);
        $reviewMock->expects($this->atMost(2))
            ->method('getAuthorType')
            ->willReturn($authorType);
        $reviewMock->expects($this->any())
            ->method('getRating')
            ->willReturn($rating);

        return $reviewMock;
    }
}
