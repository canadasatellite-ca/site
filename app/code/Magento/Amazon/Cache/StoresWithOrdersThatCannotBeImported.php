<?php

declare(strict_types=1);

namespace Magento\Amazon\Cache;

use Magento\Framework\Serialize\Serializer\Base64Json;

class StoresWithOrdersThatCannotBeImported
{
    private const IDENTIFIER = 'asc_stores_with_incomplete_orders';

    /**
     * @var array|null
     */
    private $accountUuids;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;
    /**
     * @var Base64Json
     */
    private $base64Json;

    public function __construct(\Magento\Framework\App\CacheInterface $cache, Base64Json $base64Json)
    {
        $this->cache = $cache;
        $this->base64Json = $base64Json;
    }

    public function clean()
    {
        $this->accountUuids = [];
    }

    public function get(): array
    {
        $this->load();
        return $this->accountUuids;
    }

    public function add(\Magento\Amazon\Api\Data\AccountInterface $account)
    {
        $this->load();
        $this->accountUuids[$account->getUuid()] = $account->getName();
    }

    public function persist()
    {
        $this->load();
        $this->cache->save($this->base64Json->serialize($this->accountUuids), self::IDENTIFIER);
    }

    private function load(): void
    {
        if ($this->accountUuids === null) {
            $cacheTag = self::IDENTIFIER;
            $cachedData = $this->cache->load($cacheTag);
            $accountUuid = $cachedData ? $this->base64Json->unserialize($cachedData) : [];
            $this->accountUuids = $accountUuid;
        }
    }
}
