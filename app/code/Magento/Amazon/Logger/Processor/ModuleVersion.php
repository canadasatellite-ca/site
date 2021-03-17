<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Logger\Processor;

use Magento\Amazon\Model\ModuleVersionResolver;

class ModuleVersion implements \Monolog\Processor\ProcessorInterface
{
    /**
     * @var ModuleVersionResolver
     */
    private $versionResolver;

    public function __construct(ModuleVersionResolver $versionResolver)
    {
        $this->versionResolver = $versionResolver;
    }

    /**
     * @param array $records
     * @return array The processed records
     */
    public function __invoke(array $records)
    {
        $records['extra']['module_version'] = $this->versionResolver->getVersion();

        return $records;
    }
}
