<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Email\UrlBuilder;
use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\UrlBuilder
 */
class UrlBuilderTest extends TestCase
{
    /**
     * @var UrlBuilder
     */
    private $model;

    /**
     * @var BackendUrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $backendUrlBuilderMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $frontendUrlBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->backendUrlBuilderMock = $this->getMockForAbstractClass(BackendUrlInterface::class);
        $this->frontendUrlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);

        $this->model = $objectManager->getObject(
            UrlBuilder::class,
            [
                'backendUrlBuilder' => $this->backendUrlBuilderMock,
                'frontendUrlBuilder' => $this->frontendUrlBuilderMock,
            ]
        );
    }

    /**
     * Test for getBackendUrl method
     */
    public function testGetBackendUrl()
    {
        $params = [ReviewInterface::ID => 1];
        $storeId = 0;
        $routePath = 'aw_advanced_reviews/review/edit';
        $url = 'http://store.com/admin/aw_advancedreviews/review/edit/id/1';

        $this->backendUrlBuilderMock->expects($this->once())
            ->method('setScope')
            ->with($storeId)
            ->willReturnSelf();
        $this->backendUrlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($routePath, $params)
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getBackendUrl($routePath, $storeId, $params));
    }

    /**
     * Test for getFrontendUrl method
     */
    public function testGetFrontendUrl()
    {
        $params = [];
        $storeId = 0;
        $routePath = 'aw_advanced_reviews/review_page/index';
        $url = 'http://store.com/aw_advanced_reviews/review_page/index';

        $this->frontendUrlBuilderMock->expects($this->once())
            ->method('setScope')
            ->with($storeId)
            ->willReturnSelf();
        $this->frontendUrlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($routePath, $params)
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getFrontendUrl($routePath, $storeId, $params));
    }
}
