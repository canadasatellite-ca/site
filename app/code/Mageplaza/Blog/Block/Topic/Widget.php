<?php
/**
 * Created by PhpStorm.
 * User: HoangKuty
 * Date: 4/7/2017
 * Time: 9:15 AM
 */
namespace Mageplaza\Blog\Block\Topic;

use Mageplaza\Blog\Block\Frontend;

class Widget extends Frontend
{
	function getTopicList()
	{
		return $this->helperData->getTopicList();
	}

	function getTopicUrl($topic)
	{
		return $this->helperData->getTopicUrl($topic);
	}
}
