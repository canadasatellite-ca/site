<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Helpfulness;

use Magento\Framework\Api\AbstractSimpleObject;
use Aheadworks\AdvancedReviews\Api\Data\VoteResultInterface;

/**
 * Class VoteResult
 * @package Aheadworks\AdvancedReviews\Model\Review\Helpfulness
 */
class VoteResult extends AbstractSimpleObject implements VoteResultInterface
{
    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getLikesCount()
    {
        return $this->_get(self::LIKES_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setLikeCount($likesCount)
    {
        return $this->setData(self::LIKES_COUNT, $likesCount);
    }

    /**
     * {@inheritdoc}
     */
    public function getDislikesCount()
    {
        return $this->_get(self::DISLIKES_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setDislikeCount($dislikesCount)
    {
        return $this->setData(self::DISLIKES_COUNT, $dislikesCount);
    }
}
