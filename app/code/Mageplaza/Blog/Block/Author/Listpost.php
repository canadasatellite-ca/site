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
namespace Mageplaza\Blog\Block\Author;

use Mageplaza\Blog\Block\Frontend;

class Listpost extends Frontend
{
	function getPostList()
	{
		return $this->getBlogPagination(\Mageplaza\Blog\Helper\Data::AUTHOR, $this->getRequest()->getParam('id'));
	}
	function checkRss()
	{
		return $this->helperData->getBlogUrl('post/rss');
	}
}
