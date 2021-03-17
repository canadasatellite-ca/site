<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Order;

use Magento\Amazon\Msi\MsiChecker;

class OrderHandlerResolver
{
    /**
     * @var MsiChecker
     */
    private $msiChecker;

    /**
     * @var array
     */
    private $orderHandlerPool;

    private $selectedOrderHandler;

    /**
     * @param MsiChecker $msiChecker
     * @param array $orderHandlers
     */
    public function __construct(
        MsiChecker $msiChecker,
        array $orderHandlers = []
    ) {
        $this->msiChecker = $msiChecker;
        $this->orderHandlerPool = $orderHandlers;
    }

    /**
     * @return OrderHandlerInterface
     */
    public function resolve(): OrderHandlerInterface
    {
        if (null === $this->selectedOrderHandler) {
            $this->selectedOrderHandler = $this->msiChecker->isMsiEnabled()
                ? $this->orderHandlerPool['msi_order_handler']
                : $this->orderHandlerPool['legacy_order_handler'];
        }
        return $this->selectedOrderHandler;
    }
}
