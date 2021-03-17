<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Reports\Model\ResourceModel\Refresh;

/**
 * Class Collection
 * @package Cart2Quote\Quotation\Plugin\Magento\Reports\Model\ResourceModel\Refresh
 */
class Collection extends \Magento\Framework\Data\Collection
{
    /**
     * @var \Magento\Reports\Model\FlagFactory
     */
    protected $reportsFlagFactory;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Reports\Model\FlagFactory $reportsFlagFactory
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Reports\Model\FlagFactory $reportsFlagFactory
    ) {
        parent::__construct($entityFactory);
        $this->reportsFlagFactory = $reportsFlagFactory;
    }

    /**
     * Get if updated
     *
     * @param string $reportCode
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUpdatedAt($reportCode)
    {
        $flag = $this->reportsFlagFactory->create()->setReportFlagCode($reportCode)->loadSelf();

        return $flag->hasData() ? $flag->getLastUpdate() : '';
    }

    /**
     * After load data
     *
     * @param \Magento\Reports\Model\ResourceModel\Refresh\Collection $subject
     * @param \Magento\Reports\Model\ResourceModel\Refresh\Collection $result
     * @param bool $printQuery
     * @param bool $logQuery
     * @return \Magento\Reports\Model\ResourceModel\Refresh\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterLoadData($subject, $result, $printQuery = false, $logQuery = false)
    {
        if (!count($this->_items)) {
            $data = [
                [
                    'id' => 'quotation',
                    'report' => __('Quotes'),
                    'comment' => __('Quotes Report'),
                    'updated_at' => $this->_getUpdatedAt(\Cart2Quote\Quotation\Model\Flag::REPORT_QUOTATION_FLAG_CODE)
                ],
            ];

            foreach ($data as $value) {
                $item = new \Magento\Framework\DataObject();
                $item->setData($value);
                $this->addItem($item);
                $subject->addItem($item);
            }
        }

        return $subject;
    }
}