<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Review\Listing;

use Magento\Ui\Component\Bookmark as UiBookmark;

/**
 * Class Bookmark
 *
 * @package Aheadworks\AdvancedReviews\Ui\Component\Review\Listing
 */
class Bookmark extends UiBookmark
{
    /**
     * @var string
     */
    const IS_NEED_TO_IGNORE_BOOKMARKS_REQUEST_PARAM_KEY = 'is_need_to_ignore_bookmarks';

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $isNeedToIgnoreBookmarks = (bool)$this->getContext()->getRequestParam(
            self::IS_NEED_TO_IGNORE_BOOKMARKS_REQUEST_PARAM_KEY,
            false
        );
        if ($isNeedToIgnoreBookmarks) {
            $this->disableComponent();
        } else {
            parent::prepare();
        }
    }

    /**
     * Disable current bookmark component
     */
    protected function disableComponent()
    {
        $config = $this->getConfiguration();
        $config['componentDisabled'] = true;
        $this->setData('config', $config);
    }
}
