<?php
namespace MageSuper\Casat\Plugin\Store\Switcher;

use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Framework\UrlInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ActionInterface;

class SetRedirectUrl
{
    /** @var UrlHelper */
    private $urlHelper;

    /** @var UrlInterface */
    private $urlBuilder;

    /** @var UrlFinderInterface */
    private $urlFinder;

    /** @var RequestInterface */
    private $request;

    /**
     * @param UrlHelper $urlHelper
     * @param UrlInterface $urlBuilder
     * @param UrlFinderInterface $urlFinder
     * @param RequestInterface $request
     */
    function __construct(
        UrlHelper $urlHelper,
        UrlInterface $urlBuilder,
        UrlFinderInterface $urlFinder,
        RequestInterface $request
    ) {
        $this->urlHelper = $urlHelper;
        $this->urlBuilder = $urlBuilder;
        $this->urlFinder = $urlFinder;
        $this->request = $request;
    }

    /**
     * Set redirect url for store view based on request path info
     *
     * @param \Magento\Store\Block\Switcher $switcher
     * @param \Magento\Store\Model\Store $store
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    function beforeGetTargetStorePostData(
        \Magento\Store\Block\Switcher $switcher,
        \Magento\Store\Model\Store $store,
        $data = []
    ) {
        $urlRewrite = $this->urlFinder->findOneByData([
            UrlRewrite::TARGET_PATH => $this->trimSlashInPath($this->request->getPathInfo()),
            UrlRewrite::STORE_ID => $store->getId(),
        ]);
        if ($urlRewrite) {
            $path = $this->urlBuilder->getDirectUrl($urlRewrite->getRequestPath());
            $data[ActionInterface::PARAM_NAME_URL_ENCODED] = $this->urlHelper->getEncodedUrl(
                $this->trimSlashInPath($path)
            );
        }
        return [$store, $data];
    }

    /**
     * @param string $path
     * @return string
     */
    private function trimSlashInPath($path)
    {
        return trim($path, '/');
    }
}
