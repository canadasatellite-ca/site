<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
* Class FormBuilderInformation
*
* @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
*/
class FormBuilderInformation extends FormField implements RendererInterface
{
    /**
     * FormBuilderInformation constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param string $template
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        $template = 'Cart2Quote_Quotation::system/config/form/field/formbuilder.phtml'
    ) {
        parent::__construct($context);
        $this->_template = $template;
    }

    /**
     * Render
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * Render HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
