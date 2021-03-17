<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Controller\Review\Page;

use Aheadworks\AdvancedReviews\Controller\Review\Page\Index;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\HTTP\Header as HttpHeader;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Http\UserAgent\Validator as UserAgentValidator;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Title;

/**
 * Test for \Aheadworks\AdvancedReviews\Controller\Review\Page\Index
 */
class IndexTest extends TestCase
{
    /**
     * @var Index
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageFactoryMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var HttpHeader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpHeaderMock;

    /**
     * @var UserAgentValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $userAgentValidatorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->contextMock = $this->createMock(Context::class);
        $this->resultPageFactoryMock = $this->createMock(PageFactory::class);
        $this->configMock = $this->createMock(Config::class);
        $this->httpHeaderMock = $this->createMock(HttpHeader::class);
        $this->userAgentValidatorMock = $this->createMock(UserAgentValidator::class);

        $this->controller = $objectManager->getObject(
            Index::class,
            [
                'context' => $this->contextMock,
                'config' => $this->configMock,
                'resultPageFactory' => $this->resultPageFactoryMock,
                'httpHeader' => $this->httpHeaderMock,
                'userAgentValidator' => $this->userAgentValidatorMock,
            ]
        );
    }

    /**
     * Test execute method
     *
     * @param bool $isBot
     * @param string $metaDescription
     * @dataProvider executeDataProvider
     */
    public function testExecute($isBot, $metaDescription)
    {
        $additionalHandleName = 'aw_advanced_reviews_static_review_page';
        $pageTitle = __('All Customer Reviews');
        $userAgent = 'user_agent_code';

        $this->httpHeaderMock->expects($this->once())
            ->method('getHttpUserAgent')
            ->willReturn($userAgent);

        $this->userAgentValidatorMock->expects($this->once())
            ->method('isBot')
            ->with($userAgent)
            ->willReturn($isBot);

        $this->configMock->expects($this->once())
            ->method('getMetaDescriptionForAllReviewsPage')
            ->willReturn($metaDescription);

        $titleMock = $this->createMock(Title::class);
        $titleMock->expects($this->once())
            ->method('set')
            ->with($pageTitle)
            ->willReturnSelf();

        $pageConfigMock = $this->createMock(PageConfig::class);
        $pageConfigMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($titleMock);
        $pageConfigMock->expects($this->any())
            ->method('setDescription')
            ->with($metaDescription);

        $resultPageMock = $this->createMock(Page::class);

        if ($isBot) {
            $resultPageMock->expects($this->at(0))
                ->method('addHandle')
                ->with($additionalHandleName)
                ->willReturnSelf();
            $resultPageMock->expects($this->at(1))
                ->method('getConfig')
                ->willReturn($pageConfigMock);
        } else {
            $resultPageMock->expects($this->never())
                ->method('addHandle')
                ->with($additionalHandleName);
            $resultPageMock->expects($this->any())
                ->method('getConfig')
                ->willReturn($pageConfigMock);
        }

        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultPageMock);

        $this->assertSame($resultPageMock, $this->controller->execute());
    }

    /**
     * @return array
     */
    public function executeDataProvider()
    {
        return [
            [
                'isBot' => false,
                'metaDescription' => '',
            ],
            [
                'isBot' => true,
                'metaDescription' => '',
            ],
            [
                'isBot' => false,
                'metaDescription' => 'some meta description',
            ],
            [
                'isBot' => true,
                'metaDescription' => 'some meta description',
            ],
        ];
    }
}
