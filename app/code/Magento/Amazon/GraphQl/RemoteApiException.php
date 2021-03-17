<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl;

class RemoteApiException extends \Exception implements \GraphQL\Error\ClientAware
{
    public function __construct($message, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    /**
     * @inheritDoc
     */
    public function isClientSafe()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return 'remoteApiException';
    }
}
