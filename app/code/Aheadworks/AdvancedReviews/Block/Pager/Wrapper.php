<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\Pager;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Theme\Block\Html\Pager;
use Magento\Framework\Data\Collection;

/**
 * Class Wrapper
 *
 * @package Aheadworks\AdvancedReviews\Block\Pager
 */
class Wrapper extends Template
{
    /**
     * Native pager child block alias
     */
    const PAGER_BLOCK_ALIAS = 'pager';

    /**
     * @var AbstractDataProvider
     */
    private $dataProvider;

    /**
     * @param Context $context
     * @param AbstractDataProvider $dataProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        AbstractDataProvider $dataProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataProvider = $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->applyPagingSettings();
        return $this;
    }

    /**
     * Apply paging settings to Data Provider
     *
     * @return $this
     */
    private function applyPagingSettings()
    {
        /** @var Pager|bool $pager */
        $pager = $this->getChildBlock(self::PAGER_BLOCK_ALIAS);
        if ($pager) {
            $collection = $this->dataProvider->getCollection();
            if ($collection instanceof Collection) {
                $pager->setCollection($collection);
            }
        }
        return $this;
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml(self::PAGER_BLOCK_ALIAS);
    }
}
