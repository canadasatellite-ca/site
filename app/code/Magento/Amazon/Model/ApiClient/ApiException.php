<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\ApiClient;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Used for API communication exceptions, not related to business logic,
 * e.g. server error
 */
class ApiException extends LocalizedException
{
}
