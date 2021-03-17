<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\FlagManager;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Aheadworks\AdvancedReviews\Model
 */
class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_ALLOW_GUEST_SUBMIT_REVIEW_FLAG = 'catalog/review/allow_guest';
    const XML_PATH_GENERAL_DISPLAY_MODE_OF_EMAIL_FIELD_FOR_GUEST =
        'aw_advanced_reviews/general/display_mode_of_email_field_for_guest';
    const XML_PATH_GENERAL_ARE_PROS_AND_CONS_ENABLED =
        'aw_advanced_reviews/general/are_pros_and_cons_enabled';
    const XML_PATH_GENERAL_ARE_AGREEMENTS_ENABLED =
        'aw_advanced_reviews/general/are_agreements_enabled';
    const XML_PATH_GENERAL_AGREEMENTS_DISPLAY_MODE =
        'aw_advanced_reviews/general/agreements_display_mode';
    const XML_PATH_GENERAL_ADMIN_COMMENT_CAPTION = 'aw_advanced_reviews/general/admin_comment_caption';
    const XML_PATH_GENERAL_ALL_REVIEWS_PAGE_REQUEST_PATH = 'aw_advanced_reviews/general/all_reviews_page_request_path';
    const XML_PATH_GENERAL_ALL_REVIEWS_PAGE_META_DESCRIPTION =
        'aw_advanced_reviews/general/all_reviews_page_meta_description';
    const XML_PATH_GENERAL_REVIEWS_AUTO_APPROVE = 'aw_advanced_reviews/general/auto_approve_reviews';
    const XML_PATH_GENERAL_COMMENTS_AUTO_APPROVE = 'aw_advanced_reviews/general/auto_approve_comments';
    const XML_PATH_GENERAL_ENABLE_CAPTCHA = 'aw_advanced_reviews/general/enable_captcha';
    const XML_PATH_FILE_ATTACHMENTS_ALLOW_ATTACH_FILES = 'aw_advanced_reviews/file_attachments/allow_attach_files';
    const XML_PATH_FILE_ATTACHMENTS_MAX_UPLOAD_FILE_SIZE = 'aw_advanced_reviews/file_attachments/max_upload_file_size';
    const XML_PATH_FILE_ATTACHMENTS_ALLOW_FILE_EXTENSIONS =
        'aw_advanced_reviews/file_attachments/allow_file_extensions';
    const XML_PATH_EMAIL_SENDER = 'aw_advanced_reviews/email/sender';
    const XML_PATH_EMAIL_ENABLE_REMINDER = 'aw_advanced_reviews/email/enable_reminder';
    const XML_PATH_EMAIL_ADMIN_TEMPLATE = 'aw_advanced_reviews/email/admin_template';
    const XML_PATH_EMAIL_ADMIN_EMAIL = 'aw_advanced_reviews/email/admin_email';
    const XML_PATH_EMAIL_REMINDER_AFTER_DAYS = 'aw_advanced_reviews/email/reminder_after_days';
    const XML_PATH_EMAIL_REMINDER_TEMPLATE = 'aw_advanced_reviews/email/reminder_template';
    const XML_PATH_EMAIL_EMAIL_ADDRESS_FOR_ABUSE_REPORTS = 'aw_advanced_reviews/email/email_address_for_abuse_reports';
    const XML_PATH_EMAIL_TEMPLATE_FOR_ABUSE_REPORT = 'aw_advanced_reviews/email/template_for_abuse_report';
    const XML_PATH_EMAIL_EMAIL_ADDRESS_FOR_CRITICAL_REVIEW_ALERT =
        'aw_advanced_reviews/email/email_address_for_critical_review_alert';
    const XML_PATH_EMAIL_TEMPLATE_FOR_CRITICAL_REVIEW_ALERT =
        'aw_advanced_reviews/email/template_for_critical_review_alert';
    /**#@-*/

    /**#@+
     * Constant defined for setting flag data
     */
    const SEND_EMAILS_LAST_EXEC_TIME = 'aw_advanced_reviews_send_emails_last_exec_time';
    const CLEAR_QUEUE_LAST_EXEC_TIME = 'aw_advanced_reviews_clear_queue_last_exec_time';
    const LAST_IMPORTED_REVIEW_ID = 'aw_advanced_reviews_last_imported_review_id';
    /**#@-*/

    /**
     * Canonical url path for 'All reviews' page
     */
    const ALL_REVIEWS_PAGE_CANONICAL_URL_PATH = 'aw_advanced_reviews/review_page/index';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    /**
     * @var FlagManager
     */
    private $flagManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SenderResolverInterface $senderResolver
     * @param FlagManager $flagManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SenderResolverInterface $senderResolver,
        FlagManager $flagManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->senderResolver = $senderResolver;
        $this->flagManager = $flagManager;
    }

    /**
     * Check if allow guest submit review
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isAllowGuestSubmitReview($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ALLOW_GUEST_SUBMIT_REVIEW_FLAG,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get display mode of email field for guest
     *
     * @param int|null $storeId
     * @return string
     */
    public function getDisplayModeOfEmailFieldForGuest($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_DISPLAY_MODE_OF_EMAIL_FIELD_FOR_GUEST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check are pros and cons enabled
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function areProsAndConsEnabled($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_ARE_PROS_AND_CONS_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check are terms and conditions enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function areAgreementsEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_ARE_AGREEMENTS_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get display mode of terms and conditions
     *
     * @param int|null $storeId
     * @return int
     */
    public function getAgreementsDisplayMode($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_AGREEMENTS_DISPLAY_MODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get admin comment caption
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAdminCommentCaption($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ADMIN_COMMENT_CAPTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get request path for page with all reviews
     *
     * @param int|null $storeId
     * @return string
     */
    public function getRequestPathForPageWithAllReviews($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ALL_REVIEWS_PAGE_REQUEST_PATH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get meta description for page with all reviews
     *
     * @param int|null $storeId
     * @return string
     */
    public function getMetaDescriptionForAllReviewsPage($storeId = null)
    {
        $defaultDescription = __('Read product reviews by real customers');
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ALL_REVIEWS_PAGE_META_DESCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return !empty($value) ? $value : $defaultDescription;
    }

    /**
     * Check is auto approve comments enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isAutoApproveCommentsEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_COMMENTS_AUTO_APPROVE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check is auto approve review enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isAutoApproveReviewsEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_REVIEWS_AUTO_APPROVE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if captcha is enable
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isEnableCaptcha($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_ENABLE_CAPTCHA,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if allow customer to attach files
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isAllowCustomerAttachFiles($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_FILE_ATTACHMENTS_ALLOW_ATTACH_FILES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve max upload file size
     *
     * @param int|null $storeId
     * @return int
     */
    public function getMaxUploadFileSize($storeId = null)
    {
        $fileSizeMb = (int)$this->scopeConfig->getValue(
            self::XML_PATH_FILE_ATTACHMENTS_MAX_UPLOAD_FILE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $fileSizeMb * 1024 * 1024;
    }

    /**
     * Retrieve allow file extensions
     *
     * @param int|null $storeId
     * @return array
     */
    public function getAllowFileExtensions($storeId = null)
    {
        $extensions = $this->scopeConfig->getValue(
            self::XML_PATH_FILE_ATTACHMENTS_ALLOW_FILE_EXTENSIONS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return empty($extensions) ? [] : explode(',', $extensions);
    }

    /**
     * Get sender
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSender($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get sender name
     *
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getSenderName($storeId = null)
    {
        $sender = $this->getSender($storeId);
        $data = $this->senderResolver->resolve($sender, $storeId);

        return $data['name'];
    }

    /**
     * Get sender email
     *
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getSenderEmail($storeId = null)
    {
        $sender = $this->getSender($storeId);
        $data = $this->senderResolver->resolve($sender, $storeId);

        return $data['email'];
    }

    /**
     * Check is enable review reminder
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isReviewReminderEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_ENABLE_REMINDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get admin notification template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAdminNotificationTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_ADMIN_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get admin notification email address
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAdminNotificationEmail($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_ADMIN_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get email template for abuse report
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailTemplateForAbuseReport($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE_FOR_ABUSE_REPORT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get email address for abuse reports
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailAddressForAbuseReports($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_EMAIL_ADDRESS_FOR_ABUSE_REPORTS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get email template for critical review alert
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailTemplateForCriticalReviewAlert($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE_FOR_CRITICAL_REVIEW_ALERT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get email address for critical review alert
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailAddressForCriticalReviewAlert($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_EMAIL_ADDRESS_FOR_CRITICAL_REVIEW_ALERT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get review reminder template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getReviewReminderTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_REMINDER_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get send Review Reminder after days count
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSendReminderAfterDays($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_REMINDER_AFTER_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getDefaultAdminRecipientName()
    {
        return 'Admin';
    }

    /**
     * Get send emails last exec time
     *
     * @return int
     */
    public function getSendEmailsLastExecTime()
    {
        return (int)$this->getFlagData(self::SEND_EMAILS_LAST_EXEC_TIME);
    }

    /**
     * Set send emails last exec time
     *
     * @param int $timestamp
     * @return $this
     */
    public function setSendEmailsLastExecTime($timestamp)
    {
        $this->setFlagData(self::SEND_EMAILS_LAST_EXEC_TIME, $timestamp);
        return $this;
    }

    /**
     * Get clear queue last exec time
     *
     * @return int
     */
    public function getClearQueueLastExecTime()
    {
        return (int)$this->getFlagData(self::CLEAR_QUEUE_LAST_EXEC_TIME);
    }

    /**
     * Set clear queue last exec time
     *
     * @param int $timestamp
     * @return $this
     */
    public function setClearQueueLastExecTime($timestamp)
    {
        $this->setFlagData(self::CLEAR_QUEUE_LAST_EXEC_TIME, $timestamp);
        return $this;
    }

    /**
     * Set last imported review ID
     *
     * @return int
     */
    public function getLastImportedReviewId()
    {
        return (int)$this->getFlagData(self::LAST_IMPORTED_REVIEW_ID);
    }

    /**
     * Set last imported review ID
     *
     * @param int $reviewId
     * @return $this
     */
    public function setLastImportedReviewId($reviewId)
    {
        $this->setFlagData(self::LAST_IMPORTED_REVIEW_ID, $reviewId);
        return $this;
    }

    /**
     * Get flag data
     *
     * @param string $flagCode
     * @return mixed
     */
    private function getFlagData($flagCode)
    {
        return $this->flagManager->getFlagData($flagCode);
    }

    /**
     * Set flag data
     *
     * @param string $flagCode
     * @param mixed $value
     */
    private function setFlagData($flagCode, $value)
    {
        $this->flagManager->saveFlag($flagCode, $value);
    }

    /**
     * Retrieve default page size for review list on the product page
     *
     * @param int|null $storeId
     * @return int
     */
    public function getDefaultPageSizeForProductReviewList($storeId = null)
    {
        return 10;
    }

    /**
     * Retrieve success message
     *
     * @return \Magento\Framework\Phrase
     */
    public function getReviewPostSuccessMessage()
    {
        return $this->isAutoApproveReviewsEnabled()
            ? __('Thank you for your review.')
            : __('You submitted your review for moderation.');
    }
}
