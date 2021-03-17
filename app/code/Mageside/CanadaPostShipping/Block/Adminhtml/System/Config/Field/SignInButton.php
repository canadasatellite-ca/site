<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */

namespace Mageside\CanadaPostShipping\Block\Adminhtml\System\Config\Field;

use Magento\Backend\Model\UrlInterface;

class SignInButton extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'system/config/button/button-signin.phtml';

    /**
     * @var \Mageside\CanadaPostShipping\Helper\Carrier
     */
    protected $configHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * SignInButton constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Mageside\CanadaPostShipping\Helper\Carrier $configHelper
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Mageside\CanadaPostShipping\Helper\Carrier $configHelper,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $websiteId = $this->getRequest()->getParam('website', 0);
        $this->addData(
            [
                'id'            => 'button_signin',
                'button_label'  => __('Sign in to Canada Post'),
                'signin_url'    => $this->urlBuilder->getUrl('canadapost/registration/signin', ['website' => $websiteId])
            ]
        );

        return $this->_toHtml();
    }
}
