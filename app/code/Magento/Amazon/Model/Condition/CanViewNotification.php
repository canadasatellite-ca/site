<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Condition;

use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Layout\Condition\VisibilityConditionInterface;

/**
 * Class CanViewNotification
 *
 * Dynamic validator for UI release notification, manage UI component visibility.
 * Return true if it is a new store integration.
 */
class CanViewNotification implements VisibilityConditionInterface
{
    /** @var string */
    private static $conditionName = 'can_view_notification';

    /** @var Http */
    private $request;

    /**
     * CanViewNotification constructor.
     *
     * @param Http $request
     */
    public function __construct(
        Http $request
    ) {
        $this->request = $request;
    }

    /**
     * Validate if notification popup can be shown
     *
     * @inheritdoc
     */
    public function isVisible(array $arguments)
    {
        // todo: don't user $_SERVER here
        return !$this->request->getParam('merchant_id')
            // phpcs:ignore Magento2.Security.Superglobal
            && $_SERVER['REQUEST_METHOD'] != 'POST'
            && !$this->request->getParam('no_notification');
    }

    /**
     * Get condition name
     *
     * @return string
     */
    public function getName()
    {
        return self::$conditionName;
    }
}
