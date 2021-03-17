<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Review\Frontend\Listing;

use Magento\Ui\Component\Paging as UiPaging;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReviews\Model\Config;

/**
 * Class Paging
 *
 * @package Aheadworks\AdvancedReviews\Ui\Component\Review\Frontend\Listing
 */
class Paging extends UiPaging
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param Config $config
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Config $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->prepareDefaultPageSize();
        parent::prepare();
    }

    protected function prepareDefaultPageSize()
    {
        $config = $this->getData('config');
        $defaultPageSize = $this->config->getDefaultPageSizeForProductReviewList();
        $config['defaultPageSize'] = empty($defaultPageSize) ? $config['pageSize'] : $defaultPageSize;
        $this->setData('config', $config);
        return $this;
    }
}
