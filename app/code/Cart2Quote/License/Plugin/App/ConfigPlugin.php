<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\License\Plugin\App;

/**
 * Class ConfigPlugin
 *
 * @package Cart2Quote\License\Plugin\App
 * @SuppressWarnings(PHPMD.FinalImplementation)
 */
final class ConfigPlugin
{
    /**
     * @var \Cart2Quote\License\Cache\Type\License
     */
    private $cache;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Framework\App\Config\ScopeCodeResolver
     */
    private $scopeCodeResolver;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * ConfigPlugin constructor
     *
     * @param \Cart2Quote\License\Cache\Type\License $cache
     * @param \Magento\Framework\App\Config\ScopeCodeResolver $scopeCodeResolver
     * @param \Magento\Framework\Serialize\SerializerInterface|null $serializer
     */
    public function __construct(
        \Cart2Quote\License\Cache\Type\License $cache,
        \Magento\Framework\App\Config\ScopeCodeResolver $scopeCodeResolver,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Serialize\SerializerInterface $serializer = null
    ) {
        $this->cache = $cache;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\SerializerInterface::class);
        $this->scopeCodeResolver = $scopeCodeResolver;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @param \Magento\Framework\App\Config $subject
     * @param callable $proceed
     * @param null $path
     * @param $scope
     * @param null $scopeCode
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    final public function aroundGetValue(
        \Magento\Framework\App\Config $subject,
        callable $proceed,
        $path = null,
        $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ) {
        //get scope code
        if (is_numeric($scopeCode) || $scopeCode === null) {
            $scopeCode = $this->scopeCodeResolver->resolve($scope, $scopeCode);
        } elseif ($scopeCode instanceof \Magento\Framework\App\ScopeInterface) {
            $scopeCode = $scopeCode->getCode();
        }

        //get cache key
        $cacheKey = 'changedByisConfigAllowed' . '_' . (string)$path . '_' . (string)$scope . '_' . (string)$scopeCode;

        //return result from registry if available
        if ($this->coreRegistry->registry($cacheKey) !== null) {
            return $this->coreRegistry->registry($cacheKey);
        }

        //get value
        $value = $proceed($path, $scope, $scopeCode);

        //check cache
        $cachedData = $this->cache->load($cacheKey);
        if ($cachedData && !$this->serializer->unserialize($cachedData)) {
            return $value;
        }

        $orgValue = $value;
        \Cart2Quote\Features\Feature\FeatureList::getInstance($this)->isConfigAllowed($path, $value);

        //save impact to cache
        if ($orgValue != $value) {
            $this->cache->save($this->serializer->serialize(true), $cacheKey);
        } else {
            $this->cache->save($this->serializer->serialize(false), $cacheKey);
        }

        //save result in registry
        $this->coreRegistry->register(
            $cacheKey,
            $value,
            true
        );

        return $value;
    }
}
