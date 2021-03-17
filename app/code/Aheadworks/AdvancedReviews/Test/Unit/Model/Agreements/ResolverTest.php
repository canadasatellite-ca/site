<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Agreements;

use Aheadworks\AdvancedReviews\Model\Agreements\Resolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\CheckoutAgreements\Api\Data\AgreementInterface;
use Magento\CheckoutAgreements\Model\AgreementModeOptions;
use Magento\CheckoutAgreements\Model\ResourceModel\Agreement\CollectionFactory as CheckoutAgreementsCollectionFactory;
use Magento\CheckoutAgreements\Model\ResourceModel\Agreement\Collection as CheckoutAgreementsCollection;
use Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Agreements\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $model;

    /**
     * @var CheckoutAgreementsRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutAgreementsRepositoryMock;

    /**
     * @var CheckoutAgreementsCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutAgreementsCollectionFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->checkoutAgreementsRepositoryMock = $this->createMock(
            CheckoutAgreementsRepositoryInterface::class
        );
        $this->checkoutAgreementsCollectionFactoryMock = $this->createMock(
            CheckoutAgreementsCollectionFactory::class
        );

        $this->model = $objectManager->getObject(
            Resolver::class,
            [
                'checkoutAgreementsRepository' => $this->checkoutAgreementsRepositoryMock,
                'checkoutAgreementsCollectionFactory' => $this->checkoutAgreementsCollectionFactoryMock,
            ]
        );
    }

    /**
     * Test for getAgreementsForReviews - getting all agreements
     */
    public function testGetAgreementsForReviews()
    {
        $storeId = 1;
        $autoAgreementId = 3;
        $manualAgreementId = 5;
        $autoAgreementMock = $this->createMock(AgreementInterface::class);
        $autoAgreementMock->expects($this->any())
            ->method('getAgreementId')
            ->willReturn($autoAgreementId);
        $manualAgreementMock = $this->createMock(AgreementInterface::class);
        $manualAgreementMock->expects($this->any())
            ->method('getAgreementId')
            ->willReturn($manualAgreementId);
        $list = [$autoAgreementMock, $manualAgreementMock];

        $checkoutAgreementsCollectionMock = $this->createMock(CheckoutAgreementsCollection::class);

        $this->checkoutAgreementsCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($checkoutAgreementsCollectionMock);

        $checkoutAgreementsCollectionMock->expects($this->once())
            ->method('addStoreFilter')
            ->with($storeId)
            ->willReturnSelf();
        $checkoutAgreementsCollectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with(AgreementInterface::IS_ACTIVE, true)
            ->willReturnSelf();

        $checkoutAgreementsCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($list);

        $this->checkoutAgreementsRepositoryMock->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap(
                [
                    [
                        $autoAgreementId,
                        null,
                        $autoAgreementMock
                    ],
                    [
                        $manualAgreementId,
                        null,
                        $manualAgreementMock
                    ],
                ]
            );

        $this->assertSame($list, $this->model->getAgreementsForReviews($storeId));
    }

    /**
     * Test for getAgreementsForReviews - getting only required agreements
     */
    public function testGetAgreementsForReviewsOnlyRequired()
    {
        $storeId = 1;
        $manualAgreementId = 5;
        $manualAgreementMock = $this->createMock(AgreementInterface::class);
        $manualAgreementMock->expects($this->any())
            ->method('getAgreementId')
            ->willReturn($manualAgreementId);
        $list = [$manualAgreementMock];

        $checkoutAgreementsCollectionMock = $this->createMock(CheckoutAgreementsCollection::class);

        $this->checkoutAgreementsCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($checkoutAgreementsCollectionMock);

        $checkoutAgreementsCollectionMock->expects($this->once())
            ->method('addStoreFilter')
            ->with($storeId)
            ->willReturnSelf();
        $checkoutAgreementsCollectionMock->expects($this->exactly(2))
            ->method('addFieldToFilter')
            ->withConsecutive(
                [AgreementInterface::IS_ACTIVE, true],
                [AgreementInterface::MODE, AgreementModeOptions::MODE_MANUAL]
            )->willReturnSelf();

        $checkoutAgreementsCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($list);

        $this->checkoutAgreementsRepositoryMock->expects($this->once())
            ->method('get')
            ->with($manualAgreementId, null)
            ->willReturn($manualAgreementMock);

        $this->assertSame($list, $this->model->getAgreementsForReviews($storeId, true));
    }
}
