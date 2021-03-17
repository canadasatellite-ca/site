<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;

/**
 * Interface QuoteLoaderInterface
 *
 * @package Cart2Quote\Quotation\Controller\AbstractController
 */
interface QuoteLoaderInterface
{
    /**
     * Load quote
     *
     * @param RequestInterface $request
     * @return bool|\Magento\Framework\Controller\ResultInterface
     */
    public function load(RequestInterface $request);
}
