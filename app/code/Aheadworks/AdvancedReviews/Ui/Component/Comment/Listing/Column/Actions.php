<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Comment\Listing\Column;

use Aheadworks\AdvancedReviews\Ui\Component\Listing\Column\AbstractActions;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment\Collection;

/**
 * Class Actions
 * @package Aheadworks\AdvancedReviews\Ui\Component\Comment\Listing\Column
 */
class Actions extends AbstractActions
{
    /**
     * {@inheritdoc}
     */
    protected function getActionsDataForItem($item)
    {
        $actionsData = [];
        $actionsConfig = $this->getActionsConfig();
        foreach ($actionsConfig as $actionName => $actionConfigData) {
            if ($actionName == 'abuse_ignore' && !$this->isNeedToAddIgnoreAction($item)) {
                continue;
            } else {
                $currentActionData = $this->getDataForAction($actionConfigData, $item);
            }
            if (!empty($currentActionData)) {
                $actionsData[$actionName] = $currentActionData;
            }
        }
        return $actionsData;
    }

    /**
     * Check is need to add ignore action
     *
     * @param array $item
     * @return bool
     */
    private function isNeedToAddIgnoreAction($item)
    {
        return !empty($item[Collection::NEW_ABUSE_REPORTS_COUNT]);
    }
}
