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
namespace Mageplaza\Blog\Controller;

/**
 * Class Router
 * @package Mageplaza\Blog\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{
	/**
	 * @var \Magento\Framework\App\ActionFactory
	 */
	public $actionFactory;

	/**
	 * @var \Mageplaza\Blog\Helper\Data
	 */
	public $helper;

	protected $_request;

	/**
	 * @param \Magento\Framework\App\ActionFactory $actionFactory
	 * @param \Mageplaza\Blog\Helper\Data $helper
	 */
	function __construct(
		\Magento\Framework\App\ActionFactory $actionFactory,
		\Mageplaza\Blog\Helper\Data $helper
	)
	{

		$this->actionFactory = $actionFactory;
		$this->helper        = $helper;
	}

	/**
	 * @param $controller
	 * @param $action
	 * @param array $params
	 * @return \Magento\Framework\App\ActionInterface
	 */
	function _forward($controller, $action, $params = [])
	{
		$this->_request->setControllerName($controller)
			->setActionName($action)
			->setPathInfo('/mpblog/' . $controller . '/' . $action);

		foreach ($params as $key => $value) {
			$this->_request->setParam($key, $value);
		}

		return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
	}

	/**
	 * Validate and Match Cms Page and modify request
	 *
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @return bool
	 */
	function match(\Magento\Framework\App\RequestInterface $request)
	{
		$identifier = trim($request->getPathInfo(), '/');
		$routePath  = explode('/', $identifier);
		$urlPrefix = $this->helper->getBlogConfig('general/url_prefix') ?: \Mageplaza\Blog\Helper\Data::DEFAULT_URL_PREFIX;
		$routeSize  = sizeof($routePath);
		if (!$this->helper->isEnabled() ||
			!$routeSize || ($routeSize > 3) ||
			(array_shift($routePath) != $urlPrefix)
		) {
			return null;
		}

		$request->setModuleName('mpblog')
			->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

		$this->_request = $request;
		$params     = [];
		$controller = array_shift($routePath);

		if (!$controller) {
			return $this->_forward('post', 'index');
		}
		/**
		 * 2021-06-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * "`Mageplaza_Blog` does not properly handle `/blog/tag/<tag>/` requests for an existing `<tag>`
		 * if it contains a space (e.g.: `/blog/tag/satellite%20tv/`)":
		 * https://github.com/canadasatellite-ca/site/issues/189
		 * @return string
		 */
		$pathF = function() use(&$routePath) {return urldecode(array_shift($routePath));};
		switch ($controller) {
			case 'post':
				$action = $pathF() ?: 'index';
				if (!in_array($action, ['index', 'rss'])) {
					$post = $this->helper->getPostByUrl($action);
					$action = 'view';
					$params = ['id' => $post->getId()];
				}
				break;
			case 'category':
				$action = $pathF() ?: 'index';
				if (!in_array($action, ['index', 'rss'])) {
					$category = $this->helper->getCategoryByParam('url_key', $action);
					$action = 'view';
					$params = ['id' => $category->getId()];
				}
				break;
			case 'tag':
				$tag = $this->helper->getTagByParam('url_key', $pathF());
				$action = 'view';
				$params = ['id' => $tag->getId()];
				break;
			case 'topic':
				$topic = $this->helper->getTopicByParam('url_key', $pathF());
				$action = 'view';
				$params = ['id' => $topic->getId()];
				break;
			case 'sitemap':
				$action = 'index';
				break;
			case 'author':
				$author  = $this->helper->getAuthorByParam('url_key', $pathF());
				$action = 'view';
				$params = ['id' => $author->getId()];
				break;
			case 'month':
				$author  = $this->helper->getAuthorByParam('url_key', $pathF());
				$action = 'view';
				$params = ['id' => $author->getId()];
				break;
			default:
				$post = $this->helper->getPostByUrl($controller);
				$controller = 'post';
				$action = 'view';
				$params = ['id' => $post->getId()];
		}
		return $this->_forward($controller, $action, $params);
	}
}
