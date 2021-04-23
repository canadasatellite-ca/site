<?php


namespace MageSuper\Casat\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Url;
use Mirasvit\SearchLanding\Api\Data\PageInterface;
use Mirasvit\SearchLanding\Api\Repository\PageRepositoryInterface;
use Magento\Search\Model\QueryFactory;

class Router implements RouterInterface
{
    /**
     * @var PageRepositoryInterface
     */
    protected $categoryCollectionFactory;
    protected $responseFactory;
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseFactory $responseFactory
    )
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->actionFactory = $actionFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritdoc}
     */
    function match(RequestInterface $request)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $identifier = trim($request->getPathInfo(), '/');

        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToFilter('is_active', array('eq' => 0))
            ->addAttributeToFilter('alternate_url', array('notnull' => true))
            ->addAttributeToSelect('alternate_url')
            ->addAttributeToSelect('volusion_url')
            ->addAttributeToSelect('url_key');

        if ($collection->count()) {
            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($collection as $category) {
                if ($category->getData('volusion_url') == $identifier || 'catalog/category/view/id/'.$category->getEntityId()== $identifier) {
                    $alternate_url = $category->getData('alternate_url');
                    $this->responseFactory->create()->setRedirect($alternate_url)->sendResponse();
                    die();
                }
            }
        }

        return false;
    }
}
