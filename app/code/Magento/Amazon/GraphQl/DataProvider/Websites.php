<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;

class Websites
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var array
     */
    private $websites;

    /**
     * Website constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    public function getWebsite(?string $websiteId): ?array
    {
        $allWebsites = $this->getAllWebsites();
        return $allWebsites[$websiteId] ?? null;
    }

    public function getAllWebsites(): array
    {
        if (null === $this->websites) {
            $this->websites = array_map(static function (WebsiteInterface $website) {
                return [
                    'code' => $website->getCode(),
                    'name' => $website->getName(),
                    'id' => $website->getId(),
                ];
            }, $this->storeManager->getWebsites());
        }
        return $this->websites;
    }
}
