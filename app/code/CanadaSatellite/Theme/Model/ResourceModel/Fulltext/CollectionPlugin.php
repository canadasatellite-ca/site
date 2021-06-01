<?php

namespace CanadaSatellite\Theme\Model\ResourceModel\Fulltext;

use \Closure;
use Magento\Framework\Exception\StateException;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection as Subject;

class CollectionPlugin
{
    /**
     * @param Subject $subject
     * @param Closure $proceed
     * @param string $field
     * @return array
     */
    function aroundGetFacetedData(
        Subject $subject,
        Closure $proceed,
        $field
    ) {
        try {
            $result = $proceed($field);
        } catch (StateException $e) {
            $result = [];
        }

        return $result;
    }
}
