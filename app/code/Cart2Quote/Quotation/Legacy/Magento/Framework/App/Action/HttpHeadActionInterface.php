<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Cart2Quote\Quotation\Legacy\Magento\Framework\App\Action;

use Magento\Framework\App\ActionInterface;

/**
 * Marker for actions processing HEAD requests.
 *
 * @deprecated 102.0.2 Both GET and HEAD requests map to HttpGetActionInterface
 */
interface HttpHeadActionInterface extends ActionInterface
{

}
