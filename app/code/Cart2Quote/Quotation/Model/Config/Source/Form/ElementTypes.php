<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Cart2Quote
 * Used in creating options for Form element types config value selection
 *
 */

namespace Cart2Quote\Quotation\Model\Config\Source\Form;

/**
 * Class ElementTypes
 *
 * @package Cart2Quote\Quotation\Model\Config\Source\Form
 */
class ElementTypes implements \Magento\Framework\Option\ArrayInterface
{
    use \Cart2Quote\Features\Traits\Model\Config\Source\Form\ElementTypes {
        toOptionArray as private traitToOptionArray;
        toArray as private traitToArray;
    }

    /**
     * @var array
     */
    protected $_options;

    /**
     * Standard library element types
     *
     * @var string[]
     * @see \Magento\Framework\Data\Form\Element\Factory::$_standardTypes
     */
    protected $_standardTypes = [
//        'button',
//        'checkbox',
//        'checkboxes',
//        'column',
//        'date',
//        'editablemultiselect',
//        'editor',
//        'fieldset',
//        'file',
//        'gallery',
//        'hidden',
//        'image',
//        'imagefile',
//        'label',
//        'link',
//        'multiline',
//        'multiselect',
//        'note',
//        'obscure',
//        'password',
//        'radio',
//        'radios',
//        'reset',
//        'select',
//        'submit',
        'text',
        'textarea',
//        'time',
    ];

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->traitToOptionArray();
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->traitToArray();
    }
}
