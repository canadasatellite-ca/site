<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data;

/**
 * Interface VoteResultInterface
 * @package Aheadworks\AdvancedReviews\Api\Data
 */
interface VoteResultInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LIKES_COUNT = 'likes_count';
    const DISLIKES_COUNT = 'dislikes_count';
    /**#@-*/

    /**
     * Get likes count
     *
     * @return int
     */
    public function getLikesCount();

    /**
     * Set likes count
     *
     * @param int $likesCount
     * @return $this
     */
    public function setLikeCount($likesCount);

    /**
     * Get dislikes count
     *
     * @return int
     */
    public function getDislikesCount();

    /**
     * Set dislikes count
     *
     * @param int $dislikesCount
     * @return $this
     */
    public function setDislikeCount($dislikesCount);
}
