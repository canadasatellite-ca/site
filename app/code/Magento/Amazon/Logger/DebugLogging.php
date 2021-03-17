<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Logger;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State as AppState;

class DebugLogging
{
    const CONFIG_PATH = 'saleschannels/general/debug_logging';

    const DEBUG_ENABLED = 1;

    /**
     * @var bool|null
     */
    private $isEnabled;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var AppState
     */
    private $appState;

    public function __construct(
        AppState $appState,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->appState = $appState;
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        if (null === $this->isEnabled) {
            $this->isEnabled = AppState::MODE_DEVELOPER === $this->appState->getMode()
                || self::DEBUG_ENABLED === (int)$this->scopeConfig->getValue(self::CONFIG_PATH);
        }
        return $this->isEnabled;
    }
}
