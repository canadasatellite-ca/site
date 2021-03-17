<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model;

/**
 * Class Flag
 * @package Cart2Quote\Quotation\Model
 */
class Flag extends \Magento\Reports\Model\Flag
{
    use \Cart2Quote\Features\Traits\Model\Flag {
    }

    /**
     * Quotation aggregation flag code
     */
    const REPORT_QUOTATION_FLAG_CODE = 'report_quotation_aggregated';
}