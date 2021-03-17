<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorProvider;

/**
 * Class Base
 *
 * @method \Magento\Framework\View\Element\Block\ArgumentInterface getViewModel()
 *
 * @package Aheadworks\AdvancedReviews\Block
 */
class Base extends Template
{
    /**
     * @var LayoutProcessorProvider
     */
    protected $layoutProcessorProvider;

    /**
     * @param Context $context
     * @param LayoutProcessorProvider $layoutProcessorProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        LayoutProcessorProvider $layoutProcessorProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layoutProcessorProvider = $layoutProcessorProvider;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessorProvider->getLayoutProcessors() as $layoutProcessor) {
            $this->jsLayout = $layoutProcessor->process($this->jsLayout);
        }

        return \Zend_Json::encode($this->jsLayout);
    }
}
