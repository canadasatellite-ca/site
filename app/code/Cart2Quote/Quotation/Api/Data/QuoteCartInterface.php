<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Api\Data;

use Magento\Quote\Api\Data\CartInterface;

/**
 * Quote interface
 *
 * @package Cart2Quote\Quotation\Api\Data
 */
interface QuoteCartInterface extends CartInterface, QuoteInterface
{
}
