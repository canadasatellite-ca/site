<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Api\Data;

/**
 * Quote status history interface.
 * An quote is a document that a web store issues to a customer.
 * Magento generates a quotation quote that lists the product items, billing and shipping addresses,
 * and shipping and payment methods. A corresponding external document, known as
 * a quote proposal, is emailed to the customer.
 * @api
 */
interface QuoteStatusHistoryInterface extends \Magento\Sales\Api\Data\OrderStatusHistoryInterface
{
}
