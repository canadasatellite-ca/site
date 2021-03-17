<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Core\Block\Adminhtml\Progress;

/**
 * Class Status
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Wyomind\Core\Helper\Progress
     */
    private $helperProgress;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $coreDate;
    /**
     * Name of the column that stores the scheduled tasks settings
     * @var null
     */
    private $field;
    /**
     * @var string
     */
    private $module;

    /**
     * Status constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\Backend\Block\Context $context
     * @param string $module
     * @param null $field
     * @param array $data
     */
    public function __construct(

        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Backend\Block\Context $context,
        $module="Core",
        $field=null,
        array $data=[]
    )
    {

        parent::__construct($context, $data);

        $this->coreDate=$coreDate;

        $this->helperProgress=$objectManager->create("Wyomind\\" . $module . "\Helper\Progress");
        $this->field=$field;
        $this->module=$module;
    }


    public function render(\Magento\Framework\DataObject $row)
    {

        try {

            $line=$this->helperProgress->readFlag($row->getId());

            $stats=$this->helperProgress->getStats($row->getId());
            if ($line["status"] == $this->helperProgress::SUCCEEDED) {
                $line["status"]=$this->checkCronTasks($line["status"], $row, $stats["mtime"]);
            }


            switch ($line["status"]) {
                case $this->helperProgress::SUCCEEDED:
                    $severity='notice';
                    $status=__($line["status"]);
                    break;
                case $this->helperProgress::PENDING:
                    $severity='minor';
                    $status=__($line["status"]);
                    break;
                case $this->helperProgress::PROCESSING:
                    $percent=round($line["percent"]);
                    $severity='minor';
                    $status=__($line["status"]) . " [" . $percent . "%]";
                    break;
                case $this->helperProgress::HOLD:
                    $severity='major';
                    $status=__($line["status"]);
                    break;
                case $this->helperProgress::FAILED:
                    $severity='critical';
                    $status=__($line["status"]);
                    break;
                default :
                    $severity='critical';
                    $status=__($this->helperProgress::ERROR);
                    break;
            }
        } catch (\Exception $exception) {
            $severity='minor';
            $line["message"]=$exception->getMessage();
            $status=__($this->helperProgress::PENDING);
        }


        $tooltip=null;
        $tooltipClass=null;
        if (isset($line["message"]) && $line["message"] != "") {
            $tooltip="<div class=\"tooltip-content\">" . $line["message"] . "</div>";
            $tooltipClass="tooltip";
        }
        if ($status == $this->helperProgress::PENDING) {
            $tooltipClass="tooltip";
            $tooltip="<div class=\"tooltip-content\">" . __("Next Schedule") . " " . $this->getNextSchedule($row) . " </div > ";
        }
        $script="<script language='javascript' type='text/javascript'>var updater_url='" . $this->getUrl('wyomind/progress/updater') . "'</script>";
        return $script . "<span class='$tooltipClass grid-severity-$severity updater' data-module='" . $this->module . "' data-field='" . $this->field . "' data-cron='" . $row->getData($this->field) . "' data-id='" . $row->getId() . "' ><span > " . ($status) . "</span > " . $tooltip . "</span > ";
    }

    /**
     * @param $status
     * @param \Magento\Framework\DataObject $row
     * @param $mtime
     * @return mixed
     */

    protected function checkCronTasks($status, \Magento\Framework\DataObject $row, $mtime)
    {
        $cron=array();
        $cron['current']['localTime']=$this->coreDate->timestamp();
        $cron['file']['localTime']=$this->coreDate->timestamp($mtime);
        $cronExpr=json_decode($row->getData($this->field));
        $i=0;
        foreach ($cronExpr->days as $day) {
            foreach ($cronExpr->hours as $hour) {
                $time=explode(':', $hour);

                if ($this->coreDate->date('l') == $day) {
                    $cron['tasks'][$i]['localTime']=strtotime($this->coreDate->date('Y-m-d')) + ($time[0] * 60 * 60) + ($time[1] * 60);
                } else {
                    $cron['tasks'][$i]['localTime']=strtotime("last " . $day, $cron['current']['localTime']) + ($time[0] * 60 * 60) + ($time[1] * 60);
                }

                if ($cron['tasks'][$i]['localTime'] >= $cron['file']['localTime'] && $cron['tasks'][$i]['localTime'] <= $cron['current']['localTime']
                ) {


                    $status=$this->helperProgress::PENDING;
                    continue 2;
                }
                $i++;
            }
        }

        return $status;
    }

    protected function getNextSchedule($row)
    {
        $cron['current']['localTime']=$this->coreDate->timestamp();
        $cronExpr=json_decode($row->getData($this->field));
        $time=false;
        if (isset($cronExpr->days)) {
            foreach ($cronExpr->days as $day) {
                foreach ($cronExpr->hours as $hour) {
                    $time=explode(':', $hour);

                    if ($this->coreDate->date('l') == $day) {
                        $time=strtotime($this->coreDate->date('Y-m-d')) + ($time[0] * 60 * 60) + ($time[1] * 60);
                        break 2;
                    } else {
                        $time=strtotime("next " . $day, $cron['current']['localTime']) + ($time[0] * 60 * 60) + ($time[1] * 60);
                        break 2;
                    }

                }
            }
            if ($time) {
                return date("d/m/Y H:i", $time);
            } else {
                return __("- Not set -");
            }
        }
    }
}