<?php
namespace Mageplaza\Blog\Controller;
use Magento\Framework\App\Action\Forward as Forward;
use Magento\Framework\App\RequestInterface as IRequest;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Url;
use Mageplaza\Blog\Helper\Data as H;
# 2021-06-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
# "Refactor the `Mageplaza_Blog` module": https://github.com/canadasatellite-ca/site/issues/190
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Router implements \Magento\Framework\App\RouterInterface {
	/**
	 * @var \Magento\Framework\App\ActionFactory
	 */
	public $actionFactory;

	/**
	 * @param \Magento\Framework\App\ActionFactory $actionFactory
	 * @param H $helper
	 */
	function __construct(\Magento\Framework\App\ActionFactory $actionFactory) {$this->actionFactory = $actionFactory;}

	/**
	 * @override
	 * @see \Magento\Framework\App\RouterInterface::match()
	 * @used-by \Magento\Framework\App\FrontController::dispatch()
	 * @param IRequest|Http $req
	 * @return Forward|null
	 */
	function match(IRequest $req) {
		$r = null; /** @var Forward|null $r */
		$h = df_o(H::class); /** @var H $h */
		# 2021-06-26 E.g.: «blog/tag/space-force».
		$path = trim($req->getPathInfo(), '/'); /** @var string $path */
		$pathA  = explode('/', $path); /** @var string[] $pathA */
		# 2021-06-26 E.g.: «blog».
		$prefix = $h->getBlogConfig('general/url_prefix') ?: H::DEFAULT_URL_PREFIX; /** @var string $prefix */
		$routeSize  = sizeof($pathA);
		if ($h->isEnabled() && df_between($routeSize, 1, 3) && $prefix === array_shift($pathA)) {
			$req->setModuleName('mpblog')->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $path);
			$params = [];
			$controller = array_shift($pathA);
			if (!$controller) {
				$r = $this->forward($req, 'post', 'index');
			}
			else {
				/**
				 * 2021-06-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				 * "`Mageplaza_Blog` does not properly handle `/blog/tag/<tag>/` requests for an existing `<tag>`
				 * if it contains a space (e.g.: `/blog/tag/satellite%20tv/`)":
				 * https://github.com/canadasatellite-ca/site/issues/189
				 * @return string
				 */
				$pathF = function() use(&$pathA) {return urldecode(array_shift($pathA));};
				switch ($controller) {
					case 'post':
						$action = $pathF() ?: 'index';
						if (!in_array($action, ['index', 'rss'])) {
							$post = $h->getPostByUrl($action);
							$action = 'view';
							$params = ['id' => $post->getId()];
						}
						break;
					case 'category':
						$action = $pathF() ?: 'index';
						if (!in_array($action, ['index', 'rss'])) {
							$category = $h->getCategoryByParam('url_key', $action);
							$action = 'view';
							$params = ['id' => $category->getId()];
						}
						break;
					case 'tag':
						$tag = $h->getTagByParam('url_key', $pathF());
						$action = 'view';
						$params = ['id' => $tag->getId()];
						break;
					case 'topic':
						$topic = $h->getTopicByParam('url_key', $pathF());
						$action = 'view';
						$params = ['id' => $topic->getId()];
						break;
					case 'sitemap':
						$action = 'index';
						break;
					case 'author':
					case 'month':
						$author  = $h->getAuthorByParam('url_key', $pathF());
						$action = 'view';
						$params = ['id' => $author->getId()];
						break;
					default:
						$post = $h->getPostByUrl($controller);
						$controller = 'post';
						$action = 'view';
						$params = ['id' => $post->getId()];
				}
				$r = $this->forward($req, $controller, $action, $params);
			}
		}
		return $r;
	}

	/**
	 * @used-by match()
	 * @param IRequest|Http $r
	 * @param string $c
	 * @param string $a
	 * @param array(string => mixed) $p [optional]
	 * @return Forward
	 */
	private function forward(IRequest $r, $c, $a, $p = []) {
		$r->setActionName($a);
		$r->setControllerName($c);
		$r->setParams($p);
		$r->setPathInfo("/mpblog/$c/$a");
		return df_action_c_forward();
	}
}