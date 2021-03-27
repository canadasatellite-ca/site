<?php
namespace Mageplaza\Blog\Block\Sidebar;

use Mageplaza\Blog\Block\Frontend;

class Search extends Frontend
{
	/**
	 * get search blog's data
	 */
	public function getSearchBlogData()
	{
		$result = [];
		# 2021-03-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		# «Invalid argument supplied for foreach() in app/code/Mageplaza/Blog/Block/Sidebar/Search.php on line 17»:
		# https://github.com/canadasatellite-ca/site/issues/53
		if ($posts = $this->helperData->getPostList()) {
			$limitDesc = $this->getSidebarConfig('search/description') ?: 100;
			foreach ($posts as $item) {
				$tmp = array(
					'value' => $item->getName(),
					'url'	=> $this->getUrlByPost($item),
					'image'	=> $item->getImage() ? $this->getImageUrl($item->getImage()) : $this->getDefaultImageUrl(),
					'desc'	=> $item->getShortDescription() ? substr($item->getShortDescription(),0, $limitDesc)
						: 'No description'
				);
				array_push($result, $tmp);
			}
		}
		return json_encode($result);
	}
}