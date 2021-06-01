<?php

namespace CanadaSatellite\Theme\Plugin\Model;

use MageWorx\Downloads\Model\ResourceModel\Attachment\Collection;
use MageWorx\Downloads\Helper\Data;


class AttachmentCollection
{

    const SORT_BY_ORDER_ID = 5;

    /**
     * @var Data
     */
    private $helperData;

    function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    function aroundAddSortOrder(
        Collection $subject,
        callable $proceed,
        $sort = null
    ) {
        if (!$sort) {
            $sort = $this->helperData->getSortOrder();
        }
        switch ($sort) {
            case $subject::SORT_BY_ALPHABETICAL:
                $order = 'name asc';
                break;
            case $subject::SORT_BY_UPLOAD_DATE:
                $order = 'date_added desc';
                break;
            case $subject::SORT_BY_SIZE:
                $order = 'size desc';
                break;
            case $subject::SORT_BY_DOWNLOADS:
                $order = 'downloads desc';
                break;
            case self::SORT_BY_ORDER_ID:
                $order = 'sort_order_id asc';
                break;
            default:
                $order = 'id asc';
        }
        $subject->getSelect()->order($order);
        return $subject;
    }

}
