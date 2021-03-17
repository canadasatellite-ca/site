<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Controller\Subscriber;

use Aheadworks\AdvancedReviews\Controller\Subscriber\UpdateByCode;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Request\Http as HttpRequest;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Api\EmailSubscriberManagementInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Redirect;
use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Resolver as EmailSubscriberResolver;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor\UnsubscribeLink\FormDataProvider;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory as ResultRedirectFactory;

/**
 * Test for \Aheadworks\AdvancedReviews\Controller\Subscriber\UpdateByCode
 */
class UpdateByCodeTest extends TestCase
{
    /**
     * @var UpdateByCode
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var HttpRequest|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var ResultRedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var MessageManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var FormKeyValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formKeyValidatorMock;

    /**
     * @var EmailSubscriberManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subscriberManagementMock;

    /**
     * @var ProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subscriberPostDataProcessorMock;

    /**
     * @var EmailSubscriberResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subscriberResolverMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->createMock(HttpRequest::class);
        $this->messageManagerMock = $this->createMock(MessageManagerInterface::class);
        $this->resultRedirectFactoryMock = $this->createMock(ResultRedirectFactory::class);

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
            ]
        );

        $this->formKeyValidatorMock = $this->createMock(FormKeyValidator::class);
        $this->subscriberManagementMock = $this->createMock(EmailSubscriberManagementInterface::class);
        $this->subscriberPostDataProcessorMock = $this->createMock(ProcessorInterface::class);
        $this->subscriberResolverMock = $this->createMock(EmailSubscriberResolver::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);

        $this->controller = $objectManager->getObject(
            UpdateByCode::class,
            [
                'context' => $this->contextMock,
                'formKeyValidator' => $this->formKeyValidatorMock,
                'subscriberManagement' => $this->subscriberManagementMock,
                'subscriberPostDataProcessor' => $this->subscriberPostDataProcessorMock,
                'subscriberResolver' => $this->subscriberResolverMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
            ]
        );
    }

    /**
     * Test execute method when no post data specified
     *
     * @param array|null|string $postValue
     * @dataProvider executeNoPostData
     */
    public function testExecuteNoPostData($postValue)
    {
        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postValue);

        $this->formKeyValidatorMock->expects($this->never())
            ->method('validate');

        $this->subscriberResolverMock->expects($this->never())
            ->method('getBySecurityCode');

        $this->subscriberManagementMock->expects($this->never())
            ->method('updateSubscriber');

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererOrBaseUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * @return array
     */
    public function executeNoPostData()
    {
        return [
            [
                'postValue' => null,
            ],
            [
                'postValue' => '',
            ],
            [
                'postValue' => [],
            ],
        ];
    }

    /**
     * Test execute method when form key is invalid
     */
    public function testExecuteInvalidFormKey()
    {
        $postValue = ['postData'];

        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postValue);

        $this->formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(false);

        $this->subscriberResolverMock->expects($this->never())
            ->method('getBySecurityCode');

        $this->subscriberManagementMock->expects($this->never())
            ->method('updateSubscriber');

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('Invalid Form Key. Please refresh the page.'));

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererOrBaseUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * Test execute method when no current subscriber is fetched
     *
     * @param string $securityCode
     * @dataProvider executeNoSubscriberDataProvider
     */
    public function testExecuteNoSubscriber($securityCode)
    {
        $postValue = ['postData'];

        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postValue);
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with(FormDataProvider::SECURITY_CODE_REQUEST_PARAM_KEY, '')
            ->willReturn($securityCode);

        $this->formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        $this->subscriberResolverMock->expects($this->any())
            ->method('getBySecurityCode')
            ->with($securityCode)
            ->willReturn(null);

        $this->subscriberManagementMock->expects($this->never())
            ->method('updateSubscriber');

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('Something went wrong while saving settings.'));

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererOrBaseUrl')
            ->willReturnSelf();

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
     * Test execute method with exception on update
     */
    public function testExecuteExceptionOnUpdate()
    {
        $postValue = ['postData'];
        $preparedData = ['preparedData'];
        $securityCode = 'test_security_code';
        $errorMessage = __('Error!');

        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postValue);
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with(FormDataProvider::SECURITY_CODE_REQUEST_PARAM_KEY, '')
            ->willReturn($securityCode);

        $this->formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        $subscriber = $this->createMock(SubscriberInterface::class);
        $this->subscriberResolverMock->expects($this->any())
            ->method('getBySecurityCode')
            ->with($securityCode)
            ->willReturn($subscriber);

        $this->subscriberPostDataProcessorMock->expects($this->once())
            ->method('process')
            ->with($postValue)
            ->willReturn($preparedData);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with(
                $subscriber,
                $preparedData,
                SubscriberInterface::class
            );

        $this->subscriberManagementMock->expects($this->once())
            ->method('updateSubscriber')
            ->with($subscriber)
            ->willThrowException(new LocalizedException($errorMessage));

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($errorMessage);

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererOrBaseUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $postValue = ['postData'];
        $preparedData = ['preparedData'];
        $securityCode = 'test_security_code';

        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postValue);
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with(FormDataProvider::SECURITY_CODE_REQUEST_PARAM_KEY, '')
            ->willReturn($securityCode);

        $this->formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        $subscriber = $this->createMock(SubscriberInterface::class);
        $this->subscriberResolverMock->expects($this->any())
            ->method('getBySecurityCode')
            ->with($securityCode)
            ->willReturn($subscriber);

        $this->subscriberPostDataProcessorMock->expects($this->once())
            ->method('process')
            ->with($postValue)
            ->willReturn($preparedData);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with(
                $subscriber,
                $preparedData,
                SubscriberInterface::class
            );

        $this->subscriberManagementMock->expects($this->once())
            ->method('updateSubscriber')
            ->with($subscriber)
            ->willReturn($subscriber);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('You saved your notifications settings.'));

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererOrBaseUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }
}
