<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model;

use Aheadworks\AdvancedReviews\Model\Config;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\FlagManager;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Config\Model\Config\Source\Nooptreq;
use Aheadworks\AdvancedReviews\Model\Source\Review\Agreements\DisplayMode as ReviewAgreementsDisplayMode;

/**
 * Class ConfigTest
 *
 * @package Aheadworks\AdvancedReviews\Test\Unit\Model
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * @var SenderResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $senderResolverMock;

    /**
     * @var FlagManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $flagManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->scopeConfigMock = $this->getMockForAbstractClass(
            ScopeConfigInterface::class
        );
        $this->senderResolverMock = $this->getMockForAbstractClass(
            SenderResolverInterface::class
        );
        $this->flagManagerMock = $this->createPartialMock(
            FlagManager::class,
            [
                'getFlagData',
                'saveFlag',
            ]
        );

        $this->model = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock,
                'senderResolver' => $this->senderResolverMock,
                'flagManager' => $this->flagManagerMock,
            ]
        );
    }

    /**
     * Test getDisplayModeOfEmailFieldForGuest method
     */
    public function testGetDisplayModeOfEmailFieldForGuest()
    {
        $storeId = 1;
        $displayMode = Nooptreq::VALUE_OPTIONAL;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Config::XML_PATH_GENERAL_DISPLAY_MODE_OF_EMAIL_FIELD_FOR_GUEST,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )->willReturn($displayMode);

        $this->assertEquals($displayMode, $this->model->getDisplayModeOfEmailFieldForGuest($storeId));
    }

    /**
     * Test areProsAndConsEnabled method
     */
    public function testAreProsAndConsEnabled()
    {
        $websiteId = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_GENERAL_ARE_PROS_AND_CONS_ENABLED, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn(true);

        $this->assertTrue($this->model->areProsAndConsEnabled($websiteId));
    }

    /**
     * Test areAgreementsEnabled method
     */
    public function testAreAgreementsEnabled()
    {
        $storeId = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_GENERAL_ARE_AGREEMENTS_ENABLED, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(true);

        $this->assertTrue($this->model->areAgreementsEnabled($storeId));
    }

    /**
     * Test getAgreementsDisplayMode method
     */
    public function testGetAgreementsDisplayMode()
    {
        $storeId = 1;
        $displayMode = ReviewAgreementsDisplayMode::GUESTS_ONLY;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Config::XML_PATH_GENERAL_AGREEMENTS_DISPLAY_MODE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )->willReturn($displayMode);

        $this->assertEquals($displayMode, $this->model->getAgreementsDisplayMode($storeId));
    }

    /**
     * Test isAllowGuestSubmitReview method
     */
    public function testIsAllowGuestSubmitReview()
    {
        $storeId = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_ALLOW_GUEST_SUBMIT_REVIEW_FLAG, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(true);

        $this->assertTrue($this->model->isAllowGuestSubmitReview($storeId));
    }

    /**
     * Test getAdminCommentCaption method
     */
    public function testGetAdminCommentCaption()
    {
        $storeId = 1;
        $adminCommentCaption = 'Admin';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_ADMIN_COMMENT_CAPTION, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($adminCommentCaption);

        $this->assertEquals($adminCommentCaption, $this->model->getAdminCommentCaption($storeId));
    }

    /**
     * Test isEnableCaptcha method
     */
    public function testIsEnableCaptcha()
    {
        $websiteId = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_GENERAL_ENABLE_CAPTCHA, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn(true);

        $this->assertTrue($this->model->isEnableCaptcha($websiteId));
    }

    /**
     * Test getRequestPathForPageWithAllReviews method
     */
    public function testGetRequestPathForPageWithAllReviews()
    {
        $storeId = 1;
        $url = 'reviews';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_ALL_REVIEWS_PAGE_REQUEST_PATH, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getRequestPathForPageWithAllReviews($storeId));
    }

    /**
     * Test getMetaDescriptionForAllReviewsPage method
     */
    public function testGetMetaDescriptionForAllReviewsPage()
    {
        $storeId = 1;
        $description = 'test description';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_ALL_REVIEWS_PAGE_META_DESCRIPTION, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($description);

        $this->assertEquals($description, $this->model->getMetaDescriptionForAllReviewsPage($storeId));
    }

    /**
     * Test getSender method
     */
    public function testGetSender()
    {
        $storeId = 1;
        $sender = 'general';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->assertEquals($sender, $this->model->getSender($storeId));
    }

    /**
     * Test getSenderName method
     */
    public function testGetSenderName()
    {
        $storeId = 1;
        $sender = 'general';
        $senderName = 'general_sender_name';
        $senderData = [
            'name' => $senderName
        ];

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->senderResolverMock->expects($this->once())
            ->method('resolve')
            ->with($sender, $storeId)
            ->willReturn($senderData);

        $this->assertEquals($senderName, $this->model->getSenderName($storeId));
    }

    /**
     * Test getSenderName method with exception
     *
     * @expectedException \Magento\Framework\Exception\MailException
     * @expectedExceptionMessage Invalid sender data
     */
    public function testGetSenderNameWithException()
    {
        $storeId = 1;
        $sender = 'general';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->senderResolverMock->expects($this->once())
            ->method('resolve')
            ->with($sender, $storeId)
            ->willThrowException(new \Magento\Framework\Exception\MailException(__('Invalid sender data')));

        $this->model->getSenderName($storeId);
    }

    /**
     * Test getSenderEmail method
     */
    public function testGetSenderEmail()
    {
        $storeId = 1;
        $sender = 'general';
        $senderEmail = 'general_sender_email@email.com';
        $senderData = [
            'email' => $senderEmail
        ];

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->senderResolverMock->expects($this->once())
            ->method('resolve')
            ->with($sender, $storeId)
            ->willReturn($senderData);

        $this->assertEquals($senderEmail, $this->model->getSenderEmail($storeId));
    }

    /**
     * Test getSenderEmail method with exception
     *
     * @expectedException \Magento\Framework\Exception\MailException
     * @expectedExceptionMessage Invalid sender data
     */
    public function testGetSenderEmailWithException()
    {
        $storeId = 1;
        $sender = 'general';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->senderResolverMock->expects($this->once())
            ->method('resolve')
            ->with($sender, $storeId)
            ->willThrowException(new \Magento\Framework\Exception\MailException(__('Invalid sender data')));

        $this->model->getSenderEmail($storeId);
    }

    /**
     * Test isReviewReminderEnabled method
     */
    public function testIsReviewReminderEnabled()
    {
        $storeId = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_ENABLE_REMINDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(true);

        $this->assertTrue($this->model->isReviewReminderEnabled($storeId));
    }

    /**
     * Test getAdminNotificationTemplate method
     */
    public function testGetAdminNotificationTemplate()
    {
        $storeId = 1;
        $adminNotificationTemplate = 'aw_advanced_reviews_email_admin_template';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_ADMIN_TEMPLATE, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($adminNotificationTemplate);

        $this->assertEquals($adminNotificationTemplate, $this->model->getAdminNotificationTemplate($storeId));
    }

    /**
     * Test getAdminNotificationEmail method
     */
    public function testGetAdminNotificationEmail()
    {
        $storeId = 1;
        $adminNotificationEmail = 'admin@admin.com';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_ADMIN_EMAIL, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($adminNotificationEmail);

        $this->assertEquals($adminNotificationEmail, $this->model->getAdminNotificationEmail($storeId));
    }

    /**
     * Test getReviewReminderTemplate method
     */
    public function testGetReviewReminderTemplate()
    {
        $storeId = 1;
        $reviewReminderTemplate = 'aw_advanced_reviews_email_reminder_template';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_REMINDER_TEMPLATE, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($reviewReminderTemplate);

        $this->assertEquals($reviewReminderTemplate, $this->model->getReviewReminderTemplate($storeId));
    }

    /**
     * Test getEmailAddressForAbuseReports method
     */
    public function testGetEmailAddressForAbuseReports()
    {
        $storeId = 1;
        $adminNotificationEmail = 'admin@admin.com';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_EMAIL_ADDRESS_FOR_ABUSE_REPORTS, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($adminNotificationEmail);

        $this->assertEquals($adminNotificationEmail, $this->model->getEmailAddressForAbuseReports($storeId));
    }

    /**
     * Test getEmailTemplateForAbuseReport method
     */
    public function testGetEmailTemplateForAbuseReport()
    {
        $storeId = 1;
        $reviewReminderTemplate = 'template';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_TEMPLATE_FOR_ABUSE_REPORT, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($reviewReminderTemplate);

        $this->assertEquals($reviewReminderTemplate, $this->model->getEmailTemplateForAbuseReport($storeId));
    }

    /**
     * Test getEmailAddressForCriticalReviewAlert method
     */
    public function testGetEmailAddressForCriticalReviewAlert()
    {
        $storeId = 1;
        $adminNotificationEmail = 'admin@admin.com';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Config::XML_PATH_EMAIL_EMAIL_ADDRESS_FOR_CRITICAL_REVIEW_ALERT,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )->willReturn($adminNotificationEmail);

        $this->assertEquals($adminNotificationEmail, $this->model->getEmailAddressForCriticalReviewAlert($storeId));
    }

    /**
     * Test getEmailTemplateForCriticalReviewAlert method
     */
    public function testGetEmailTemplateForCriticalReviewAlert()
    {
        $storeId = 1;
        $reviewReminderTemplate = 'template';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_TEMPLATE_FOR_CRITICAL_REVIEW_ALERT, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($reviewReminderTemplate);

        $this->assertEquals($reviewReminderTemplate, $this->model->getEmailTemplateForCriticalReviewAlert($storeId));
    }

    /**
     * Test getSendReminderAfterDays method
     */
    public function testGetSendReminderAfterDays()
    {
        $storeId = 1;
        $sendReminderAfterDays = 7;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_REMINDER_AFTER_DAYS, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sendReminderAfterDays);

        $this->assertEquals($sendReminderAfterDays, $this->model->getSendReminderAfterDays($storeId));
    }

    /**
     * Test getDefaultAdminRecipientName method
     */
    public function testGetDefaultAdminRecipientName()
    {
        $defaultAdminRecipientName = 'Admin';

        $this->assertEquals($defaultAdminRecipientName, $this->model->getDefaultAdminRecipientName());
    }

    /**
     * Test getDefaultPageSizeForProductReviewList method
     */
    public function testGetDefaultPageSizeForProductReviewList()
    {
        $storeId = 1;
        $defaultPageSizeForProductReviewList = 10;

        $this->assertEquals(
            $defaultPageSizeForProductReviewList,
            $this->model->getDefaultPageSizeForProductReviewList($storeId)
        );
    }

    /**
     * Test getSendEmailsLastExecTime method
     */
    public function testGetSendEmailsLastExecTime()
    {
        $sendEmailsLastExecTime = 800000;

        $this->flagManagerMock->expects($this->once())
            ->method('getFlagData')
            ->with(Config::SEND_EMAILS_LAST_EXEC_TIME)
            ->willReturn($sendEmailsLastExecTime);

        $this->assertEquals($sendEmailsLastExecTime, $this->model->getSendEmailsLastExecTime());
    }

    /**
     * Test setSendEmailsLastExecTime method
     */
    public function testSetSendEmailsLastExecTime()
    {
        $sendEmailsLastExecTime = 800000;

        $this->flagManagerMock->expects($this->once())
            ->method('saveFlag')
            ->with(Config::SEND_EMAILS_LAST_EXEC_TIME, $sendEmailsLastExecTime)
            ->willReturn(true);

        $this->model->setSendEmailsLastExecTime($sendEmailsLastExecTime);
    }

    /**
     * Test getClearQueueLastExecTime method
     */
    public function testGetClearQueueLastExecTime()
    {
        $clearQueueLastExecTime = 800000;

        $this->flagManagerMock->expects($this->once())
            ->method('getFlagData')
            ->with(Config::CLEAR_QUEUE_LAST_EXEC_TIME)
            ->willReturn($clearQueueLastExecTime);

        $this->assertEquals($clearQueueLastExecTime, $this->model->getClearQueueLastExecTime());
    }

    /**
     * Test setClearQueueLastExecTime method
     */
    public function testSetClearQueueLastExecTime()
    {
        $clearQueueLastExecTime = 800000;

        $this->flagManagerMock->expects($this->once())
            ->method('saveFlag')
            ->with(Config::CLEAR_QUEUE_LAST_EXEC_TIME, $clearQueueLastExecTime)
            ->willReturn(true);

        $this->model->setClearQueueLastExecTime($clearQueueLastExecTime);
    }

    /**
     * Test getLastImportedReviewId method
     */
    public function testGetLastImportedReviewId()
    {
        $lastImportedReviewId = 100;

        $this->flagManagerMock->expects($this->once())
            ->method('getFlagData')
            ->with(Config::LAST_IMPORTED_REVIEW_ID)
            ->willReturn($lastImportedReviewId);

        $this->assertEquals($lastImportedReviewId, $this->model->getLastImportedReviewId());
    }

    /**
     * Test setLastImportedReviewId method
     */
    public function testSetLastImportedReviewId()
    {
        $lastImportedReviewId = 120;

        $this->flagManagerMock->expects($this->once())
            ->method('saveFlag')
            ->with(Config::LAST_IMPORTED_REVIEW_ID, $lastImportedReviewId)
            ->willReturn(true);

        $this->model->setLastImportedReviewId($lastImportedReviewId);
    }
}
