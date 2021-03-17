<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

use \Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * Class License
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
 */
class License extends Template implements RendererInterface
{
    /**
     * Color red
     */
    const COLOR_RED = 'license-status-red';

    /**
     * Color orange
     */
    const COLOR_ORANGE = 'license-status-orange';

    /**
     * Color green
     */
    const COLOR_GREEN = 'license-status-green';

    /**
     * Color yellow
     */
    const COLOR_YELLOW = 'license-status-yellow';

    /**
     * Maximum allowed proposal amount
     */
    const LIMIT_REACHED_AMOUNT = 15;

    /**
     * Notice level proposal amount
     */
    const LIMIT_NOTICE_AMOUNT = 10;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * License constructor.
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Cart2Quote\Quotation\Helper\Data\LicenseInterface $license
     * @param \Cart2Quote\Quotation\Helper\Data\Metadata $metadata
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Cart2Quote\Quotation\Helper\Data\LicenseInterface $license,
        \Cart2Quote\Quotation\Helper\Data\Metadata $metadata,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($license, $metadata, $context, $data);
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<tr><th align="left">Name</th><th align="left">Version/Data</th></tr>';

        $html .= sprintf(
            '<tr><td>%s</td><td>%s</td></tr>',
            __('PHP Version:'),
            $this->metadata->getPhpVersion()
        );

        if ($icVersion = $this->metadata->getIoncubeVersion()) {
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
            $this->metadata->getMagentoVersion()
        );

        $html .= sprintf(
            '<tr><td>%s</td><td>%s</td></tr>',
            __('Magento Edition:'),
            $this->metadata->getMagentoEdition()
        );

        $html .= '<tr><td></td></tr>';

        if ($this->moduleManager->isEnabled('Cart2Quote_AutoProposal')) {
            $html .=
                sprintf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    __('AutoProposal Version:'),
                    $this->metadata->getAutoProposalVersion()
                );
        }

        if ($this->moduleManager->isEnabled('Cart2Quote_Desk')) {
            $html .=
                sprintf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    __('SupportDesk Version:'),
                    $this->metadata->getSupportDeskVersion()
                );
        }

        if ($this->moduleManager->isEnabled('Cart2Quote_DeskEmail')) {
            $html .=
                sprintf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    __('DeskEmail Version:'),
                    $this->metadata->getDeskEmailVersion()
                );
        }

        if ($this->moduleManager->isEnabled('Cart2Quote_Not2Order')) {
            $html .=
                sprintf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    __('Not2Order Version:'),
                    $this->metadata->getNot2OrderVersion()
                );
        }

        if ($this->moduleManager->isEnabled('Cart2Quote_SalesRep')) {
            $html .=
                sprintf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    __('SalesRep Version:'),
                    $this->metadata->getSalesRepVersion()
                );
        }

        $html .= '<tr><td>&nbsp;</td></tr>';

        $html .= sprintf('<tr><th align="left" colspan="2">%s</th></tr>', __('License'));
        $html .= sprintf(
            '<tr><td>%s</td><td>%s</td></tr>',
            __('Current Domain:'),
            $this->license->getDomain()
        );
        $html .= sprintf(
            '<tr><td>%s</td><td>%s</td></tr>',
            __('Cart2Quote Edition:'),
            $this->getCart2QuoteEditionHtml()
        );
        $html .= sprintf(
            '<tr><td>%s</td><td>%s</td></tr>',
            __('Cart2Quote Version:'),
            $this->metadata->getCart2QuoteVersion()
        );
        $html .= sprintf(
            '<tr><td>%s</td><td>%s</td></tr>',
            __('Cart2Quote License Status:'),
            $this->getCart2QuoteStateHtml()
        );
        $html .= sprintf(
            '<tr><td>%s</td><td>%s</td></tr>',
            __('Cart2Quote License Type:'),
            $this->getCart2QuoteTypeHtml()
        );

        if ($this->license->getEdition() == 'lite') {
            $html .= sprintf(
                '<tr><td>%s</br>%s</td><td>%s</td></tr>',
                __('Proposals sent this month:'),
                __('(Resets the first day of the month)'),
                $this->getProposalSentAmountHtml()
            );
        }

        $html .= '<tr><td></td></tr>';
        $html .= $this->_renderValue($element);

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Get the C2Q type html
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getCart2QuoteTypeHtml()
    {
        if (!$this->license->isUnreachable()) {
            return ucfirst(__($this->license->getLicenseType()));
        }

        return __('<b class="%1">%2</b>', self::COLOR_ORANGE, ucfirst($this->license->getLicenseType()));
    }

    /**
     * Get the Cart2Quote license
     *
     * @return string
     */
    public function getCart2QuoteEditionHtml()
    {
        if (!$this->license->isUnreachable()) {
            return ucfirst(__($this->license->getEdition()));
        }

        return __('<b class="%1">%2</b>', self::COLOR_ORANGE, ucfirst(__($this->license->getEdition())));
    }

    /**
     * Get the Cart2Quote state
     *
     * @return string
     */
    public function getCart2QuoteStateHtml()
    {
        $color = null;
        if ($this->license->isActiveState()) {
            $color = self::COLOR_GREEN;
        } elseif ($this->license->isInactiveState()) {
            $color = self::COLOR_RED;
        } elseif ($this->license->isPendingState() || $this->license->isUnreachableState()) {
            $color = self::COLOR_ORANGE;
        }

        return __('<b class="%1">%2</b>', $color, ucfirst($this->license->getLicenseState()));
    }

    /**
     * Get sent proposal amount
     *
     * @return string
     */
    public function getProposalSentAmountHtml()
    {
        $amount = $this->license->getProposalAmount();
        if (!isset($amount)) {
            $amount = 0;
        }
        $color = self::COLOR_GREEN;
        if ($amount >= self::LIMIT_NOTICE_AMOUNT && $amount < self::LIMIT_REACHED_AMOUNT) {
            $color = self::COLOR_YELLOW;
        } elseif ($amount >= self::LIMIT_REACHED_AMOUNT) {
            $color = self::COLOR_ORANGE;
        }

        return sprintf('<b class="%s">%s / %s</b>', $color, $amount, self::LIMIT_REACHED_AMOUNT);
    }
}
