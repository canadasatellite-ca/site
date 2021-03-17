<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Comment;

use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Status as CommentStatusSource;

/**
 * Class Resolver
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Comment
 */
class Resolver
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Check if need to show on the frontend comment with specified status
     *
     * @param int $status
     * @return bool
     */
    public function isNeedToShowOnFrontend($status)
    {
        return in_array($status, CommentStatusSource::getDisplayStatuses());
    }

    /**
     * Resolve comment nickname for backend
     *
     * @param string $nickname
     * @return string
     */
    public function getNicknameForBackend($nickname)
    {
        return empty($nickname)
            ? $this->getAdminCommentCaption()
            : $nickname;
    }

    /**
     * Resolve comment nickname for storefront
     *
     * @param string $nickname
     * @param int|null $storeId
     * @return string
     */
    public function getNicknameForFrontend($nickname, $storeId = null)
    {
        return empty($nickname)
            ? __('Response from %1', $this->getAdminCommentCaption($storeId))
            : $nickname;
    }

    /**
     * Retrieve admin nickname from config
     *
     * @param int|null $storeId
     * @return string
     */
    private function getAdminCommentCaption($storeId = null)
    {
        return $this->config->getAdminCommentCaption($storeId);
    }
}
