<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email;

use Magento\Framework\UrlInterface;
use Magento\Backend\Model\UrlInterface as BackendUrlInterface;

/**
 * Class UrlBuilder
 * @package Aheadworks\AdvancedReviews\Model\Email
 */
class UrlBuilder
{
    /**
     * @var BackendUrlInterface
     */
    private $backendUrlBuilder;

    /**
     * @var UrlInterface
     */
    private $frontendUrlBuilder;

    /**
     * @param BackendUrlInterface $backendUrlBuilder
     * @param UrlInterface $frontendUrlBuilder
     */
    public function __construct(
        BackendUrlInterface $backendUrlBuilder,
        UrlInterface $frontendUrlBuilder
    ) {
        $this->backendUrlBuilder = $backendUrlBuilder;
        $this->frontendUrlBuilder = $frontendUrlBuilder;
    }

    /**
     * Get backend url
     *
     * @param string $routePath
     * @param string $scope
     * @param array $params
     * @return string
     */
    public function getBackendUrl($routePath, $scope, $params)
    {
        $this->backendUrlBuilder->setScope($scope);
        $href = $this->backendUrlBuilder->getUrl(
            $routePath,
            $params
        );

        return $href;
    }

    /**
     * Get frontend url
     *
     * @param string $routePath
     * @param string $scope
     * @param array $params
     * @return string
     */
    public function getFrontendUrl($routePath, $scope, $params)
    {
        return $this->frontendUrlBuilder
            ->setScope($scope)
            ->getUrl(
                $routePath,
                $params
            );
    }
}
