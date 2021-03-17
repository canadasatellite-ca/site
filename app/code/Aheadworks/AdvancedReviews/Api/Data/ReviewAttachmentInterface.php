<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data;

/**
 * Thread message attachment interface
 * @api
 */
interface ReviewAttachmentInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const REVIEW_ID = 'review_id';
    const NAME = 'name';
    const FILE_NAME = 'file_name';
    /**#@-*/

    /**
     * Get review id
     *
     * @return int
     */
    public function getReviewId();

    /**
     * Set review id
     *
     * @param int $reviewId
     * @return $this
     */
    public function setReviewId($reviewId);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName();

    /**
     * Set file name
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName);
}
