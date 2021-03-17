<?php

namespace CanadaSatellite\Theme\Block\Adminhtml\Quote\View;

use Cart2Quote\Quotation\Block\Adminhtml\Quote\View\ExpiryDate as VendorExpiryDate;

/**
 * View quote expiry date calendar
 */
class ExpiryDate
{
    /**
     * Check expiry date active status
     * @return string
     */
    public function afterIsActiveExpiryDate(VendorExpiryDate $subject, $result)
    {
        return '';
    }
}
