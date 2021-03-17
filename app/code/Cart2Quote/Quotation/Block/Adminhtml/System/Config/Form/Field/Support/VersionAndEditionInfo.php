<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Support;

use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * Class VersionAndEditionInfo
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Support
 */
class VersionAndEditionInfo extends FormField implements RendererInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data\MetadataInterface
     */
    private $metaData;

    /**
     * VersionAndEditionInfo constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Cart2Quote\Quotation\Helper\Data\MetadataInterface $metaData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        \Cart2Quote\Quotation\Helper\Data\MetadataInterface $metaData
    ) {
        parent::__construct($context);
        $this->moduleManager = $moduleManager;
        $this->metaData = $metaData;
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<tr><th align="left">Name</th><th align="left">Version/Data</th></tr>';

        $html .= sprintf(
            '<tr><td>%s</td><td>%s</td></tr>',
            __('PHP Version:'),
            $this->metaData->getPhpVersion()
        );

        if ($icVersion = $this->metaData->getIoncubeVersion()) {
            $html .= sprintf(
                '<tr><td>%s</td><td>%s</td></tr>',
                __('IonCube Version:'),
                $icVersion
            );
        }


        $html .= '<tr><td></td></tr>';

        $html .= sprintf(
            '<tr><td>%s</td><td>%s</td></tr>',
            __('Magento Version:'),
            $this->metaData->getMagentoVersion()
        );

        $html .= sprintf(
            '<tr><td>%s</td><td>%s</td></tr>',
            __('Magento Edition:'),
            $this->metaData->getMagentoEdition()
        );

        $html .= '<tr><td></td></tr>';

        if ($this->moduleManager->isEnabled('Cart2Quote_AutoProposal')) {
            $html .=
                sprintf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    __('AutoProposal Version:'),
                    $this->metaData->getAutoProposalVersion()
                );
        }

        if ($this->moduleManager->isEnabled('Cart2Quote_Desk')) {
            $html .=
                sprintf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    __('SupportDesk Version:'),
                    $this->metaData->getSupportDeskVersion()
                );
        }

        if ($this->moduleManager->isEnabled('Cart2Quote_DeskEmail')) {
            $html .=
                sprintf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    __('DeskEmail Version:'),
                    $this->metaData->getDeskEmailVersion()
                );
        }

        if ($this->moduleManager->isEnabled('Cart2Quote_Not2Order')) {
            $html .=
                sprintf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    __('Not2Order Version:'),
                    $this->metaData->getNot2OrderVersion()
                );
        }

        if ($this->moduleManager->isEnabled('Cart2Quote_SalesRep')) {
            $html .=
                sprintf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    __('SalesRep Version:'),
                    $this->metaData->getSalesRepVersion()
                );
        }

        $html .= '<tr><td>&nbsp;</td></tr>';

        $html .= $this->_renderValue($element);

        return $this->_decorateRowHtml($element, $html);
    }
}
