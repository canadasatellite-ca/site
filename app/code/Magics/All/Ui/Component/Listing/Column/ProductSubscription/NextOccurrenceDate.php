<?php

namespace Magics\All\Ui\Component\Listing\Column\ProductSubscription;

/**
 * Class NextOccurrenceDate
 * @package Magics\All\Ui\Component\Listing\Column\ProductSubscription
 */
class NextOccurrenceDate extends \Magedelight\Subscribenow\Ui\Component\Listing\Column\ProductSubscription\NextOccurrenceDate
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $nextDate = null;
                //subscription_status
                if (isset($item['additional_info'])) {
                    $additionalInfo = !is_array($item['additional_info']) ? unserialize($item['additional_info']) : $item['additional_info'];
                    if (isset($additionalInfo['next_cycle'])) {
                        try {
                            if ($item['subscription_status'] == 7) {
                                $nextDate = null;
                            } else {
                                $nextDate = date('F d,Y', $additionalInfo['next_cycle']);
                            }
                        } catch (\Exception $e) {

                        }
                    }
                }

                if (!empty($nextDate)) {
                    $item['additional_info'] = $nextDate;
                } else {
                    $item['additional_info'] = '';
                }
            }
        }

        return $dataSource;
    }
}
