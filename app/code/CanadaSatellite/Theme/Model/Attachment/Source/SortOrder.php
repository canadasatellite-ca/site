<?php

namespace CanadaSatellite\Theme\Model\Attachment\Source;

use MageWorx\Downloads\Model\Source;
use MageWorx\Downloads\Model\ResourceModel\Attachment\Collection;
use CanadaSatellite\Theme\Plugin\Model\AttachmentCollection;

class SortOrder extends Source
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    function toOptionArray()
    {
        return [
            [
                'value' => Collection::SORT_BY_ALPHABETICAL,
                'label' => __('Alphabetical')
            ],
            [
                'value' => Collection::SORT_BY_UPLOAD_DATE,
                'label' => __('Upload Date')
            ],
            [
                'value' => Collection::SORT_BY_SIZE,
                'label' => __('Size')
            ],
            [
                'value' => Collection::SORT_BY_DOWNLOADS,
                'label' => __('Downloads')
            ],
            [
                'value' => AttachmentCollection::SORT_BY_ORDER_ID,
                'label' => __('Order Id')
            ],
        ];
    }
}