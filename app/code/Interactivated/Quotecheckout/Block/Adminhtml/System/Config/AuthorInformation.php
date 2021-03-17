<?php

namespace Interactivated\Quotecheckout\Block\Adminhtml\System\Config;

class AuthorInformation extends \Magento\Config\Block\System\Config\Form\Field
{
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {    	    
		$html = $this->getLayout()
			->createBlock('Magento\Framework\View\Element\Template')
			->setTemplate('Interactivated_Quotecheckout::system/config/author.phtml')
			->toHtml();

        return $html;
    }
}
