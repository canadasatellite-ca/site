<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Controller\Subscriber;

use Aheadworks\AdvancedReviews\Controller\Subscriber\Edit;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Resolver as EmailSubscriberResolver;
use Magento\Framework\Controller\Result\Redirect;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor\UnsubscribeLink\FormDataProvider;
use Magento\Framework\Controller\Result\RedirectFactory as ResultRedirectFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;

/**
 * Test for \Aheadworks\AdvancedReviews\Controller\Subscriber\Edit
 */
class EditTest extends TestCase
{
    /**
     * @var Edit
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var ResultRedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var MessageManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageFactoryMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlMock;

    /**
     * @var EmailSubscriberResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subscriberResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultRedirectFactoryMock = $this->createMock(ResultRedirectFactory::class);
        $this->messageManagerMock = $this->createMock(MessageManagerInterface::class);
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->urlMock = $this->createMock(UrlInterface::class);

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'url' => $this->urlMock,
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
            ]
        );

        $this->resultPageFactoryMock = $this->createMock(PageFactory::class);
        $this->subscriberResolverMock = $this->createMock(EmailSubscriberResolver::class);

        $this->controller = $objectManager->getObject(
            Edit::class,
            [
                'context' => $this->contextMock,
                'resultPageFactory' => $this->resultPageFactoryMock,
                'subscriberResolver' => $this->subscriberResolverMock,
            ]
        );
    }

    /**
     * Test execute method when no current subscriber is fetched
     *
     * @param string $securityCode
     * @dataProvider executeNoSubscriberDataProvider
     */
    public function testExecuteNoSubscriber($securityCode)
    {
        $baseUrl = 'www.store.com';

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(FormDataProvider::SECURITY_CODE_REQUEST_PARAM_KEY, '')
            ->willReturn($securityCode);

        $this->subscriberResolverMock->expects($this->any())
            ->method('getBySecurityCode')
            ->with($securityCode)
            ->willReturn(null);

        $this->resultPageFactoryMock->expects($this->never())
            ->method('create');

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('Unsubscribe link has already expired.'));

        $this->urlMock->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn($baseUrl);

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setUrl')
            ->with($baseUrl);

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * @return array
     */
    public function executeNoSubscriberDataProvider()
    {
        return [
            [
                'securityCode' => '',
            ],
            [
                'securityCode' => 'test_security_code',
            ],
        ];
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $securityCode = 'test_security_code';

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(FormDataProvider::SECURITY_CODE_REQUEST_PARAM_KEY, '')
            ->willReturn($securityCode);

        $subscriber = $this->createMock(SubscriberInterface::class);
        $this->subscriberResolverMock->expects($this->any())
            ->method('getBySecurityCode')
            ->with($securityCode)
            ->willReturn($subscriber);

        $titleMock = $this->createMock(Title::class);
        $titleMock->expects($this->once())
            ->method('set')
            ->with(__('Reviews Notifications'));

        $pageConfigMock = $this->createMock(Config::class);
        $pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);

        $resultPageMock = $this->createMock(ResultPage::class);
        $resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($pageConfigMock);
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultPageMock);

        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $this->urlMock->expects($this->never())
            ->method('getBaseUrl');

        $this->resultRedirectFactoryMock->expects($this->never())
            ->method('create');

        $this->assertSame($resultPageMock, $this->controller->execute());
    }
}
