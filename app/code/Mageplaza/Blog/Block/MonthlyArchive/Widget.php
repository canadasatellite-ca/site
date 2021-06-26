<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Blog\Block\MonthlyArchive;

use Mageplaza\Blog\Block\Frontend;

class Widget extends Frontend
{

	function getDateArrayCount()
	{
		return $this->helperData->getDateArrayCount();
	}

	function getDateArrayUnique()
	{
		return $this->helperData->getDateArrayUnique();
	}

	function getDateCount()
	{
		return $this->helperData->getDateCount();
	}

	function getMonthlyUrl($month)
	{
		return $this->helperData->getMonthlyUrl($month);
	}

	function getDateLabel()
	{
		return $this->helperData->getDateLabel();
	}

}
