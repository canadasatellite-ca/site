<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\AbstractModifier;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Model\Review\Comment\Resolver as CommentResolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\AdvancedReviews\Model\DateTime\Formatter as DateTimeFormatter;

/**
 * Class Comments
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend
 */
class Comments extends AbstractModifier
{
    /**
     * @var DateTimeFormatter
     */
    private $dateTimeFormatter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CommentResolver
     */
    private $commentResolver;

    /**
     * @param DateTimeFormatter $dateTimeFormatter
     * @param StoreManagerInterface $storeManager
     * @param CommentResolver $commentResolver
     */
    public function __construct(
        DateTimeFormatter $dateTimeFormatter,
        StoreManagerInterface $storeManager,
        CommentResolver $commentResolver
    ) {
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->storeManager = $storeManager;
        $this->commentResolver = $commentResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->isSetId($data)) {
            $this->prepareComments($data);
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Prepare comments
     *
     * @param array $data
     * @return array
     */
    private function prepareComments(&$data)
    {
        $currentStoreId = $this->getCurrentStoreId();
        $comments = isset($data[ReviewInterface::COMMENTS]) ? $data[ReviewInterface::COMMENTS] : [];
        $preparedComments = [];
        foreach ($comments as $comment) {
            if (is_array($comment)
                && isset($comment[CommentInterface::STATUS])
                && $this->commentResolver->isNeedToShowOnFrontend($comment[CommentInterface::STATUS])
            ) {
                $preparedCommentData = [];
                $preparedCommentData[CommentInterface::ID] = $comment[CommentInterface::ID];
                $preparedCommentData[CommentInterface::TYPE] = $comment[CommentInterface::TYPE];
                $preparedCommentData[CommentInterface::CONTENT] = $comment[CommentInterface::CONTENT];
                $preparedCommentData[CommentInterface::NICKNAME] = $this->commentResolver->getNicknameForFrontend(
                    $comment[CommentInterface::NICKNAME],
                    $currentStoreId
                );
                $preparedCommentData[CommentInterface::CREATED_AT] = $this->dateTimeFormatter->getLocalizedDate(
                    $comment[CommentInterface::CREATED_AT],
                    $currentStoreId
                );
                $preparedComments[] = $preparedCommentData;
            }
        }
        $data[ReviewInterface::COMMENTS] = $preparedComments;
        return $data;
    }

    /**
     * Retrieve current store id
     *
     * @return int|null
     */
    private function getCurrentStoreId()
    {
        try {
            $currentStoreId = $this->storeManager->getStore(true)->getId();
        } catch (NoSuchEntityException $exception) {
            $currentStoreId = null;
        }
        return $currentStoreId;
    }
}
