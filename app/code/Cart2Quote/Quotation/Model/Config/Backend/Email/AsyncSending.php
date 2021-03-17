<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Backend\Email;

/**
 * Backend model for global configuration value
 * 'sales_email/general/async_sending'.
 */
class AsyncSending extends \Magento\Sales\Model\Config\Backend\Email\AsyncSending
{
use \Cart2Quote\Features\Traits\Model\Config\Backend\Email\AsyncSending {
    }

    }
