<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;

interface HandlerInterface
{
    /**
     * @param array $updates
     * @param AccountInterface $account
     * @return array ids of persisted logs
     */
    public function handle(array $updates, \Magento\Amazon\Api\Data\AccountInterface $account): array;
}
