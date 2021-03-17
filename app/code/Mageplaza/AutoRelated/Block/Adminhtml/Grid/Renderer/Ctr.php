<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AutoRelated
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AutoRelated\Block\Adminhtml\Grid\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Mageplaza\AutoRelated\Helper\Data;

/**
 * Class Ctr
 * @package Mageplaza\AutoRelated\Block\Adminhtml\Grid\Renderer
 */
class Ctr extends AbstractRenderer
{
    /**
     * @var \Mageplaza\AutoRelated\Helper\Data
     */
    protected $autoRelatedHelper;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Mageplaza\AutoRelated\Helper\Data $autoRelatedHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $autoRelatedHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->autoRelatedHelper = $autoRelatedHelper;
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(DataObject $row)
    {
        if ($row) {
            $click      = (int)$row->getTotalClick();
            $impression = (int)$row->getTotalImpression();
            if ($impression && $click) {
                return $this->autoRelatedHelper->getCtr($click, $impression);
            }
        }

        return '0%';
    }
}
