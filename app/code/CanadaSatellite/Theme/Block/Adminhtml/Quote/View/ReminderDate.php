<?php

namespace CanadaSatellite\Theme\Block\Adminhtml\Quote\View;

use Cart2Quote\Quotation\Block\Adminhtml\Quote\View\ReminderDate as VendorReminderDate;

/**
 * View quote expiry date calendar
 */
class ReminderDate
{
    /**
     * Check reminder date active status
     * @return string
     */
    function afterIsActiveReminderDate(VendorReminderDate $subject, $result)
    {
        return '';
    }
}