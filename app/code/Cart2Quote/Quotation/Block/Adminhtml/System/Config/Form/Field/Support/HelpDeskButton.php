<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Support;

use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * Class HelpDeskButton
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Support
 */
class HelpDeskButton extends FormField implements RendererInterface
{
    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        //the text on the button itself
        $buttonText = '';

        // the full verb to add in the instructions
        $fullActionText = '';

        //the result of filling out the form (bug report or support ticket)
        $resultText = '';

        switch ($element->getLabel()) {
            case 'Create Ticket':
                $buttonText = __('Create Ticket');
                $fullActionText = __('Create a Ticket');
                $resultText = __('Support Ticket');
                break;
            case 'Report Bug':
                $buttonText = __('Report Bug');
                $fullActionText = __('Report a Bug');
                $resultText = __('Bug Report');
                break;
        }

        $element->setValue($buttonText);
        $class = 'action-default scalable ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only';
        $element->setClass($class);
        $element->addCustomAttribute(
            "onClick",
            "window.open('https://cart2quote.zendesk.com/hc/en-us/requests/new', '_blank')"
        );

        $html = '<p>';
        $html .= __('Clicking the button below will open a new page where you can ');
        $html .= $fullActionText;
        $html .= __('. Please fill out the form completely. In order to handle your ');
        $html .= $resultText;
        $html .= __(' properly we need as much information as you can provide.');
        $html .= '</p>';
        $html .= $this->_renderValue($element);

        return $this->_decorateRowHtml($element, $html);
    }
}
