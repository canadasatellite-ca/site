<?php

namespace MW\Onestepcheckout\Controller\Index;

class Updatetimepicker extends \MW\Onestepcheckout\Controller\Checkout\Onepage
{
	public function execute()
	{
		$dateIsNow            	= $this->getRequest()->getPost('now');
        $startTime            	= $this->getRequest()->getPost('stime');
        $startTimeArray       	= explode(":", $startTime);
        $countStartTimeToMinutes= $startTimeArray[0] * 60 + $startTimeArray[1];
        $endTime              	= $this->getRequest()->getPost('etime');
        $endTimeArray         	= explode(":", $endTime);
        $countEndTimeToMinutes 	= $endTimeArray[0] * 60 + $endTimeArray[1];
        $countTimeNow 			= date("G", (new \DateTime())->getTimestamp()) * 60 + date("i", (new \DateTime())->getTimestamp());
        if ($dateIsNow) {
            if ($countTimeNow >= $countStartTimeToMinutes) {
                if ($countTimeNow < $countEndTimeToMinutes) {
                	// Apply print current time on timepickercho
                    echo date("G", (new \DateTime())->getTimestamp()) . ":" . date("i", (new \DateTime())->getTimestamp());
                } else {
                    echo "";
                }
            } else {
                echo $startTime;
            }
        } else {
            echo $startTime;
        }
	}
}
