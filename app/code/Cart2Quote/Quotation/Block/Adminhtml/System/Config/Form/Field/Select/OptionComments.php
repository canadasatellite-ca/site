<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Select;

use \Cart2Quote\Quotation\Model\Config\Source\Form\Field\Select\OptionInterface;

/**
 * Class OptionComments
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Select
 */
class OptionComments extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Render option comment
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $sourceModel = \Magento\Framework\App\ObjectManager::getInstance()->get(
            $element->getFieldConfig()['source_model']
        );
        if ($sourceModel instanceof \Cart2Quote\Quotation\Model\Config\Source\Options) {
            $comment = $element->getComment();
            /**
             * @var OptionInterface $option
             */
            foreach ($sourceModel->getOptions() as $option) {
                $optionComment = $option->getComment();
                if (!empty($optionComment)) {
                    $comment .= $optionComment . '<br />';
                }
            }
            $element->setComment(rtrim($comment, '<br />'));
        }

        return parent::_renderValue($element);
    }
}
