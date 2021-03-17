<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Captcha;

/**
 * Interface CaptchaAdapterInterface
 * @package Aheadworks\AdvancedReviews\Model\Captcha
 */
interface CaptchaAdapterInterface
{
    /**#@+
     * Form ids
     */
    const REVIEW_FORM_ID = 'aw-ar-product-review';
    const COMMENT_FORM_ID_BASE = 'comment-form-';
    const DEFAULT_FORM_ID = 'default-form';
    /**#@-*/

    /**
     * Check if is enabled
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Retrieve layout config
     *
     * @return array
     */
    public function getLayoutConfig();

    /**
     * Retrieve config data
     *
     * @return array
     */
    public function getConfigData();

    /**
     * Check if is valid
     *
     * @return bool
     */
    public function isValid();
}
