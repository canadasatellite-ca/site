<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Framework\View\Model\Layout;

/**
 * Class Merge
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Framework\View\Model\Layout;
 */
class Merge extends \Magento\Framework\View\Model\Layout\Merge
{
    /**
     * Array of specific update_handles that should be called on our own handles.
     * Note: By extending \Magento\Framework\View\Model\Layout\Merge we can access protected variables and methods.
     *
     * @var \string[][]
     */
    private $selectiveHandles = [
        [
            //<update handle="Vertex_AddressValidation::checkout_index_index"/>
            'quotation_handle' => 'quotation_quote_checkout',
            'external_handle' => 'checkout_index_index',
            'hasString' => 'Vertex_AddressValidation'
        ],
    ];

    /**
     * Plugin on the asString() method of the layout merger
     *
     * @param \Magento\Framework\View\Model\Layout\Merge $subject
     * @return array
     */
    public function beforeAsString(
        \Magento\Framework\View\Model\Layout\Merge $subject
    ) {
        $handles = $subject->allHandles;
        foreach ($this->selectiveHandles as $selectiveHandle) {
            foreach ($handles as $handle => $handleState) {
                if ($handle == $selectiveHandle['quotation_handle']) {
                    $this->selectiveMerge(
                        $subject,
                        $selectiveHandle['external_handle'],
                        $selectiveHandle['hasString']
                    );

                    break;
                }
            }
        }

        return [];
    }

    /**
     * Like the _merge() function but with our own fetchPackageLayoutUpdates() call
     * Note: No support for fetchDbLayoutUpdates() but that would be easy to add
     *
     * @param \Magento\Framework\View\Model\Layout\Merge $subject
     * @param string $handle
     * @param string $hasString
     * @return $this
     */
    private function selectiveMerge(
        \Magento\Framework\View\Model\Layout\Merge $subject,
        $handle,
        $hasString = ''
    ) {
        if (!isset($subject->allHandles[$handle])) {
            $subject->allHandles[$handle] = $subject->handleProcessing;
            $this->fetchPackageLayoutUpdates($subject, $handle, $hasString);
            $subject->allHandles[$handle] = $subject->handleProcessed;
        }

        return $this;
    }

    /**
     * Like the original _fetchPackageLayoutUpdates function but with the xmlContains check added.
     *
     * @param \Magento\Framework\View\Model\Layout\Merge $subject
     * @param string $handle
     * @param string $hasString
     * @return bool
     * @throws \Exception
     */
    private function fetchPackageLayoutUpdates(
        \Magento\Framework\View\Model\Layout\Merge $subject,
        $handle,
        $hasString = ''
    ) {
        $_profilerKey = 'layout_package_update:' . $handle;
        \Magento\Framework\Profiler::start($_profilerKey);

        $layout = $subject->getFileLayoutUpdatesXml();
        foreach ($layout->xpath("*[self::handle or self::layout][@id='{$handle}']") as $updateXml) {
            $this->fetchRecursiveUpdates($updateXml, $subject, $hasString);
            $updateInnerXml = $updateXml->innerXml();
            $subject->validateUpdate($handle, $updateInnerXml);

            //check if this new XML contains a given string
            if (!empty($hasString) && !$this->xmlContains($updateInnerXml, $hasString)) {
                continue;
            }

            $subject->addUpdate($updateInnerXml);
        }

        \Magento\Framework\Profiler::stop($_profilerKey);
        return true;
    }

    /**
     * Add handles declared as '<update handle="handle_name"/>' directives
     *
     * @param \SimpleXMLElement $updateXml
     * @param \Magento\Framework\View\Model\Layout\Merge $subject
     * @param string $hasString
     * @return $this|\Cart2Quote\Quotation\Plugin\Magento\Framework\View\Model\Layout\Merge
     */
    protected function fetchRecursiveUpdates($updateXml, $subject, $hasString)
    {
        foreach ($updateXml->children() as $child) {
            if (strtolower($child->getName()) == 'update' && isset($child['handle'])) {
                $this->selectiveMerge($subject, (string)$child['handle'], $hasString);
            }
        }
        if (isset($updateXml['layout'])) {
            //Layout should not be changed in this after plugin
            //$subject->pageLayout = (string)$updateXml['layout'];
        }
        return $this;
    }

    /**
     * Simple string contain checker
     *
     * @param string $updateInnerXml
     * @param string $hasString
     * @return bool
     */
    private function xmlContains($updateInnerXml, $hasString)
    {
        if (stripos($updateInnerXml, $hasString) === false) {
            return false;
        }

        return true;
    }
}
