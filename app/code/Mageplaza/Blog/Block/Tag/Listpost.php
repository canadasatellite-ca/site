<?php
namespace Mageplaza\Blog\Block\Tag;
use Mageplaza\Blog\Block\Frontend;
class Listpost extends Frontend {
	/**
	 * @used-by app/code/Mageplaza/Blog/view/frontend/templates/category/post/list.phtml
	 * @return mixed
	 */
	function getPostList() {return
		$this->getBlogPagination(\Mageplaza\Blog\Helper\Data::TAG, $this->getRequest()->getParam('id'))
	;}

	function checkRss() {return $this->helperData->getBlogUrl('post/rss');}
}