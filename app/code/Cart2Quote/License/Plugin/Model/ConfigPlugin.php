<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\License\Plugin\Model;

/**
 * Class ConfigPlugin
 * @package Cart2Quote\License\Plugin\Model
 * @SuppressWarnings(PHPMD.FinalImplementation)
 */
final class ConfigPlugin
{
    /**
     * @param \Magento\Config\Model\Config $subject
     * @param callable $proceed
     *
     * @return \Magento\Config\Model\Config
     */
    final public function aroundSave(\Magento\Config\Model\Config $subject, callable $proceed)
    {
        $sectionId = $subject->getSection();
        $groups    = $subject->getGroups();
        if (is_array($groups)) {
            foreach ($groups as $groupId => &$groupData) {
                $this->processGroup($groupId, $groupData, $sectionId);
            }
            $subject->setGroups($groups);
        }

        return $proceed();
    }

    /**
     * @param $groupId
     * @param $groupData
     * @param $sectionPath
     */
    final private function processGroup($groupId, $groupData, $sectionPath)
    {
        $groupPath = $sectionPath . '/' . $groupId;

        if (isset($groupData['fields'])) {
            foreach ($groupData['fields'] as $fieldId => &$fieldData) {
                $path = $groupPath . '/' . $fieldId;
                \Cart2Quote\Features\Feature\FeatureList::getInstance($this)->isConfigAllowed(
                    $path,
                    $fieldData['value'],
                    true
                );
            }
        }
        if (isset($groupData['groups'])) {
            foreach ($groupData['groups'] as $subGroupId => $subGroupData) {
                $this->processGroup($subGroupId, $subGroupData, $groupPath);
            }
        }
    }

    /**
     * @param \Magento\Config\Model\Config $subject
     * @param callable $proceed
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    final public function aroundLoad(
        \Magento\Config\Model\Config $subject,
        callable $proceed
    ) {
        $results = $proceed();
        foreach ($results as $path => &$value) {
            \Cart2Quote\Features\Feature\FeatureList::getInstance($this)->isConfigAllowed($path, $value);
        }

        return $results;
    }
}
