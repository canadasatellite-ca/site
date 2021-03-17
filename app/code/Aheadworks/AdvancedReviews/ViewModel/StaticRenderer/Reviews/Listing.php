<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver as ReviewRatingResolver;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection;

/**
 * Class Listing
 *
 * @package Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews
 */
class Listing implements ArgumentInterface
{
    /**
     * @var DataProviderInterface
     */
    private $reviewDataProvider;

    /**
     * @var ReviewRatingResolver
     */
    private $reviewRatingResolver;

    /**
     * @var array
     */
    private $sortableColumnsHeaders = [];

    /**
     * @param DataProviderInterface $reviewDataProvider
     * @param ReviewRatingResolver $reviewRatingResolver
     * @param array $sortableColumnsHeaders
     */
    public function __construct(
        DataProviderInterface $reviewDataProvider,
        ReviewRatingResolver $reviewRatingResolver,
        $sortableColumnsHeaders = []
    ) {
        $this->reviewDataProvider = $reviewDataProvider;
        $this->reviewRatingResolver = $reviewRatingResolver;
        $this->sortableColumnsHeaders = $sortableColumnsHeaders;
    }

    /**
     * Retrieve headers of sortable columns
     *
     * @return array
     */
    public function getSortableColumnsHeaders()
    {
        return $this->sortableColumnsHeaders;
    }

    /**
     * Retrieve reviews data
     *
     * @return array
     */
    public function getReviewsData()
    {
        $reviewsData = [];
        $this->reviewDataProvider->addOrder(ReviewInterface::CREATED_AT, 'desc');
        $data = $this->reviewDataProvider->getData();
        if (is_array($data) && isset($data['items']) && is_array($data['items'])) {
            $reviewsData = $data['items'];
        }
        return $reviewsData;
    }

    /**
     * Retrieve nickname
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewNickname($reviewsDataRow)
    {
        return isset($reviewsDataRow[ReviewInterface::NICKNAME]) ? $reviewsDataRow[ReviewInterface::NICKNAME] : '';
    }

    /**
     * Retrieve advantages
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewAdvantages($reviewsDataRow)
    {
        return (
            isset($reviewsDataRow[ReviewInterface::PROS])
            &&
            $this->hasVisibleCharacters($reviewsDataRow[ReviewInterface::PROS])
        ) ? $reviewsDataRow[ReviewInterface::PROS]
            : '';
    }

    /**
     * Retrieve disadvantages
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewDisadvantages($reviewsDataRow)
    {
        return (
            isset($reviewsDataRow[ReviewInterface::CONS])
            &&
            $this->hasVisibleCharacters($reviewsDataRow[ReviewInterface::CONS])
        ) ? $reviewsDataRow[ReviewInterface::CONS]
            : '';
    }

    /**
     * Retrieve content
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewContent($reviewsDataRow)
    {
        return isset($reviewsDataRow[ReviewInterface::CONTENT]) ? $reviewsDataRow[ReviewInterface::CONTENT] : '';
    }

    /**
     * Retrieve summary
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewSummary($reviewsDataRow)
    {
        return isset($reviewsDataRow[ReviewInterface::SUMMARY]) ? $reviewsDataRow[ReviewInterface::SUMMARY] : '';
    }

    /**
     * Retrieve verified buyer label
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewVerifiedBuyerLabel($reviewsDataRow)
    {
        return isset($reviewsDataRow[ReviewInterface::IS_VERIFIED_BUYER])
            ? $reviewsDataRow[ReviewInterface::IS_VERIFIED_BUYER]
            : '';
    }

    /**
     * Retrieve product recommended label
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewProductRecommendedLabel($reviewsDataRow)
    {
        return isset($reviewsDataRow[ReviewInterface::PRODUCT_RECOMMENDED . '_label'])
            ? $reviewsDataRow[ReviewInterface::PRODUCT_RECOMMENDED . '_label']
            : '';
    }

    /**
     * Retrieve absolute value of review rating
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewRatingAbsoluteValue($reviewsDataRow)
    {
        return isset($reviewsDataRow[ReviewInterface::RATING . '_absolute_value'])
            ? $reviewsDataRow[ReviewInterface::RATING . '_absolute_value']
            : '';
    }

    /**
     * Retrieve review rating maximum absolute value
     *
     * @return int
     */
    public function getReviewRatingMaximumAbsoluteValue()
    {
        return $this->reviewRatingResolver->getRatingMaximumAbsoluteValue();
    }

    /**
     * Retrieve review rating minimum absolute value
     *
     * @return int
     */
    public function getReviewRatingMinimumAbsoluteValue()
    {
        return $this->reviewRatingResolver->getRatingMinimumAbsoluteValue();
    }

    /**
     * Retrieve product url
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewProductUrl($reviewsDataRow)
    {
        return isset($reviewsDataRow[Collection::PRODUCT_NAME_COLUMN_NAME . '_url'])
            ? $reviewsDataRow[Collection::PRODUCT_NAME_COLUMN_NAME . '_url']
            : '';
    }

    /**
     * Retrieve product label
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewProductLabel($reviewsDataRow)
    {
        return isset($reviewsDataRow[Collection::PRODUCT_NAME_COLUMN_NAME . '_label'])
            ? $reviewsDataRow[Collection::PRODUCT_NAME_COLUMN_NAME . '_label']
            : '';
    }

    /**
     * Retrieve created at date
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewCreatedAt($reviewsDataRow)
    {
        return isset($reviewsDataRow[ReviewInterface::CREATED_AT])
            ? $reviewsDataRow[ReviewInterface::CREATED_AT]
            : '';
    }

    /**
     * Retrieve created at date in ISO format
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewCreatedAtInIsoFormat($reviewsDataRow)
    {
        return isset($reviewsDataRow[ReviewInterface::CREATED_AT . '_in_iso_format'])
            ? $reviewsDataRow[ReviewInterface::CREATED_AT . '_in_iso_format']
            : '';
    }

    /**
     * Retrieve attachments
     *
     * @param array $reviewsDataRow
     * @return array
     */
    public function getReviewAttachments($reviewsDataRow)
    {
        return (isset($reviewsDataRow[ReviewInterface::ATTACHMENTS])
            && is_array($reviewsDataRow[ReviewInterface::ATTACHMENTS]))
            ? $reviewsDataRow[ReviewInterface::ATTACHMENTS]
            : [];
    }

    /**
     * Retrieve attachment url
     *
     * @param array $reviewAttachmentData
     * @return string
     */
    public function getReviewAttachmentUrl($reviewAttachmentData)
    {
        return (isset($reviewAttachmentData['url']))
            ? $reviewAttachmentData['url']
            : '';
    }

    /**
     * Retrieve attachment title
     *
     * @param array $reviewAttachmentData
     * @return string
     */
    public function getReviewAttachmentTitle($reviewAttachmentData)
    {
        $attachmentTitle = '';
        if ($this->isReviewAttachmentImage($reviewAttachmentData)) {
            $attachmentTitle = (isset($reviewAttachmentData['image_title']))
                ? $reviewAttachmentData['image_title']
                : '';
        }
        return $attachmentTitle;
    }

    /**
     * Check if review attachment is image
     *
     * @param array $reviewAttachmentData
     * @return bool
     */
    public function isReviewAttachmentImage($reviewAttachmentData)
    {
        return $this->getReviewAttachmentPreviewType($reviewAttachmentData) === 'image';
    }

    /**
     * Retrieve votes positive count
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewVotesPositive($reviewsDataRow)
    {
        return isset($reviewsDataRow[ReviewInterface::VOTES_POSITIVE])
            ? $reviewsDataRow[ReviewInterface::VOTES_POSITIVE]
            : '';
    }

    /**
     * Retrieve votes negative count
     *
     * @param array $reviewsDataRow
     * @return string
     */
    public function getReviewVotesNegative($reviewsDataRow)
    {
        return isset($reviewsDataRow[ReviewInterface::VOTES_NEGATIVE])
            ? $reviewsDataRow[ReviewInterface::VOTES_NEGATIVE]
            : '';
    }

    /**
     * Retrieve attachment preview type
     *
     * @param array $reviewAttachmentData
     * @return string
     */
    private function getReviewAttachmentPreviewType($reviewAttachmentData)
    {
        if (empty($reviewAttachmentData['type'])) {
            return 'document';
        }

        $type = explode('/', $reviewAttachmentData['type']);
        return ($type[0] !== 'image' && $type[0] !== 'video')
            ? 'document'
            : $type[0];
    }

    /**
     * Retrieve comments
     *
     * @param array $reviewsDataRow
     * @return array
     */
    public function getReviewComments($reviewsDataRow)
    {
        return (isset($reviewsDataRow[ReviewInterface::COMMENTS])
            && is_array($reviewsDataRow[ReviewInterface::COMMENTS]))
            ? $reviewsDataRow[ReviewInterface::COMMENTS]
            : [];
    }

    /**
     * Retrieve comment author nickname
     *
     * @param array $reviewCommentData
     * @return string
     */
    public function getReviewCommentNickname($reviewCommentData)
    {
        return (isset($reviewCommentData[CommentInterface::NICKNAME]))
            ? $reviewCommentData[CommentInterface::NICKNAME]
            : '';
    }

    /**
     * Retrieve comment created at date
     *
     * @param array $reviewCommentData
     * @return string
     */
    public function getReviewCommentCreatedAt($reviewCommentData)
    {
        return (isset($reviewCommentData[CommentInterface::CREATED_AT]))
            ? $reviewCommentData[CommentInterface::CREATED_AT]
            : '';
    }

    /**
     * Retrieve comment content
     *
     * @param array $reviewCommentData
     * @return string
     */
    public function getReviewCommentContent($reviewCommentData)
    {
        return (isset($reviewCommentData[CommentInterface::CONTENT]))
            ? $reviewCommentData[CommentInterface::CONTENT]
            : '';
    }

    /**
     * Returns has review's additional description any visible character
     *
     * @param string $reviewAdditionalDescription
     * @return bool
     */
    public function hasVisibleCharacters($reviewAdditionalDescription)
    {
        return (ctype_space($reviewAdditionalDescription) === false)
            ? true
            : false;
    }
}
