<?php

/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Model\System\Config\Frontend;

class Reminder extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Registry                                $coreRegistry
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= "<script type='text/javascript'>
        require([
'jquery', 
'jquery/ui', 
'jquery/validate', 
'mage/translate'
], function($){ 
$.validator.addMethod(
'reminder-greater', function (value) { 
var mdupdatebefore = document.getElementById('md_subscribenow_general_update_profile_before').value;
var mdreminder = parseInt(value);
if(!mdreminder){
return true;
}
if(mdreminder > mdupdatebefore){
return true;
}else{
return false;
}
}, $.mage.__('Reminder days must be greater then allow to update profile before'));
});
            </script>";

        return $html;
    }
}
