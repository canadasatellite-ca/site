<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class LicenseStatus
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
 */
class LicenseStatus extends Template
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $date;

    /**
     * LicenseStatus constructor
     *
     * @param \Cart2Quote\Quotation\Helper\Data\LicenseInterface $license
     * @param \Cart2Quote\Quotation\Helper\Data\Metadata $metadata
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data\LicenseInterface $license,
        \Cart2Quote\Quotation\Helper\Data\Metadata $metadata,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct(
            $license,
            $metadata,
            $context,
            $data
        );

        $this->date = $context->getLocaleDate();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonHtml()
    {
        $label = null;
        switch ($this->license->getSimplifiedLicenseState()) {
            default:
            case \Cart2Quote\Quotation\Helper\Data\License::NOT_VALID:
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_TRIAL:
            case \Cart2Quote\Quotation\Helper\Data\License::EXPIRED_TRIAL:
            case \Cart2Quote\Quotation\Helper\Data\License::CANCELED_SUBSCRIPTION:
                $label = __('Purchase License');
                $url = $this->getCart2quoteComparisonUrl();
                break;
            case \Cart2Quote\Quotation\Helper\Data\License::OPENSOURCE:
            case \Cart2Quote\Quotation\Helper\Data\License::SERVER_UNREACHABLE:
            case \Cart2Quote\Quotation\Helper\Data\License::PENDING_LICENSE:
                return '';
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_SUBSCRIPTION:
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_LICENSE:
                $label = __('Update');
                $url = $this->getCart2QuoteUpdateUrl();
                break;
            case \Cart2Quote\Quotation\Helper\Data\License::EXPIRED_LICENSE:
                $label = __('Renew update subscription');
                $url = $this->getCart2QuoteUpgradeUrl();
                break;
        }

        return $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            [
                'id' => 'purchase_license',
                'label' => $label,
                'class' => 'button-license',
                'onclick' => \sprintf('window.open("%s")', $url)
            ]
        )->toHtml();
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    private function getCaption()
    {
        switch ($this->license->getSimplifiedLicenseState()) {
            default:
            case \Cart2Quote\Quotation\Helper\Data\License::NOT_VALID:
                return __(
                    'Enter a valid Order # or %1',
                    $this->getLink($this->getCart2quoteComparisonUrl(), __('Purchase a valid license'))
                );
            case \Cart2Quote\Quotation\Helper\Data\License::SERVER_UNREACHABLE:
                return '';
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_TRIAL:
                return __('A trial license is active, no order # is necessary.');
            case \Cart2Quote\Quotation\Helper\Data\License::EXPIRED_TRIAL:
                return __('Purchase a license to re-activate Cart2Quote. No re-install is necessary.');
            case \Cart2Quote\Quotation\Helper\Data\License::PENDING_LICENSE:
                return __('A temporary license has been issued. Request is in process.');
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_SUBSCRIPTION:
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_LICENSE:
                return __('Your update subscription is valid. Request and install the latest version');
            case \Cart2Quote\Quotation\Helper\Data\License::EXPIRED_LICENSE:
                return __('Profit from our discounts when extending your update subscription.');
            case \Cart2Quote\Quotation\Helper\Data\License::CANCELED_SUBSCRIPTION:
                return __('Reactivate Cart2Quote by purchasing a license.');
            case \Cart2Quote\Quotation\Helper\Data\License::OPENSOURCE:
                return __('Developer Version licence is activated.');
        }
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     */
    protected function applyElementConfig(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (!$element->getValue()) {
            $element->setValue($this->getValue());
        }
        parent::applyElementConfig($element);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    private function getValue()
    {
        $expiryDate = $this->license->getExpiryDate();
        $date = $this->date->date($expiryDate, null, false, true)->format('d-m-Y');

        switch ($this->license->getSimplifiedLicenseState()) {
            case \Cart2Quote\Quotation\Helper\Data\License::OPENSOURCE:
                return __('Valid');
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_TRIAL:
                return __('Trial license valid till %1', $date);
            case \Cart2Quote\Quotation\Helper\Data\License::EXPIRED_TRIAL:
                return __('Trial license expired on %1', $date);
            case \Cart2Quote\Quotation\Helper\Data\License::PENDING_LICENSE:
                return __('Pending');
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_LICENSE:
                return __('Valid till %1', $date);
            case \Cart2Quote\Quotation\Helper\Data\License::EXPIRED_LICENSE:
                return __('Expired on %1', $date);
            case \Cart2Quote\Quotation\Helper\Data\License::CANCELED_SUBSCRIPTION:
                return __('Cancelled');
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_SUBSCRIPTION:
                return __('Active');
            case \Cart2Quote\Quotation\Helper\Data\License::SERVER_UNREACHABLE:
                return
                    __('Server is unreachable please try again later.') . ' ' .
                    __('If the problem persists please contact us at support@cart2quote.com');
            case \Cart2Quote\Quotation\Helper\Data\License::NOT_VALID:
            default:
                return __('No valid Order # entered');
        }
    }

    /**
     * @return string|null
     */
    protected function getColorClass()
    {
        $color = null;
        switch ($this->license->getSimplifiedLicenseState()) {
            case \Cart2Quote\Quotation\Helper\Data\License::OPENSOURCE:
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_SUBSCRIPTION:
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_LICENSE:
            case \Cart2Quote\Quotation\Helper\Data\License::VALID_TRIAL:
                $color = 'license-status-green';
                break;
            case \Cart2Quote\Quotation\Helper\Data\License::PENDING_LICENSE:
            case \Cart2Quote\Quotation\Helper\Data\License::EXPIRED_LICENSE:
                $color = 'license-status-orange';
                break;
            case \Cart2Quote\Quotation\Helper\Data\License::NOT_VALID:
            case \Cart2Quote\Quotation\Helper\Data\License::EXPIRED_TRIAL:
            case \Cart2Quote\Quotation\Helper\Data\License::CANCELED_SUBSCRIPTION:
            case \Cart2Quote\Quotation\Helper\Data\License::SERVER_UNREACHABLE:
                $color = 'license-status-red';
                break;
            default:
                $color = null;
                break;
        }

        return $color;
    }

    /**
     * @return string
     */
    protected function beforeGetElementHtml()
    {
        $html = parent::beforeGetElementHtml();
        $html .= '<div class="value-container">';
        $html .= \sprintf('<div class="%s">', $this->getColorClass());

        return $html;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function afterGetElementHtml()
    {
        $html = parent::afterGetElementHtml();
        $html .= '<p class="note">' . $this->getCaption();
        $html .= $this->getButtonHtml();
        $html .= '</p>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
