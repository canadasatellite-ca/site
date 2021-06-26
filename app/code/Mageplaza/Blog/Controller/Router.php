<?php
namespace Mageplaza\Blog\Controller;
use Magento\Framework\App\RequestInterface as IRequest;
use Mageplaza\Blog\Helper\Data as H;
class Router implements \Magento\Framework\App\RouterInterface {
	/**
	 * @var \Magento\Framework\App\ActionFactory
	 */
	public $actionFactory;

	/**
	 * @var H
	 */
	public $helper;

	protected $_request;

	/**
	 * @param \Magento\Framework\App\ActionFactory $actionFactory
	 * @param H $helper
	 */
	function __construct(\Magento\Framework\App\ActionFactory $actionFactory, H $helper) {
		$this->actionFactory = $actionFactory;
		$this->helper = $helper;
	}

	/**
	 * @param $controller
	 * @param $action
	 * @param array $params
	 * @return \Magento\Framework\App\ActionInterface
	 */
	function _forward($controller, $action, $params = []) {
		$this->_request->setControllerName($controller)
			->setActionName($action)
			->setPathInfo('/mpblog/' . $controller . '/' . $action);
		foreach ($params as $key => $value) {
			$this->_request->setParam($key, $value);
		}
		return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
	}

	/**
	 * @override
	 * @see \Magento\Framework\App\RouterInterface::match()
	 * @used-by \Magento\Framework\App\FrontController::dispatch()
	 * @param IRequest $req
	 * @return bool
	 */
	function match(IRequest $req) {
		$identifier = trim($req->getPathInfo(), '/');
		$routePath  = explode('/', $identifier);
		$urlPrefix = $this->helper->getBlogConfig('general/url_prefix') ?: H::DEFAULT_URL_PREFIX;
		$routeSize  = sizeof($routePath);
		if (!$this->helper->isEnabled() ||
			!$routeSize || ($routeSize > 3) ||
			(array_shift($routePath) != $urlPrefix)
		) {
			return null;
		}
		$req->setModuleName('mpblog')
			->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
		$this->_request = $req;
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