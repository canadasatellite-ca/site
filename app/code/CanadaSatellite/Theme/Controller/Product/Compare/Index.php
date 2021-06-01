<?php

namespace CanadaSatellite\Theme\Controller\Product\Compare;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Url\DecoderInterface;
use Magento\Framework\Registry;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Catalog\Controller\Product\Compare\Index
{

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var DecoderInterface
     */
    protected $urlDecoder;


    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Model\Product\Compare\ItemFactory $compareItemFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Visitor $customerVisitor
     * @param \Magento\Catalog\Model\Product\Compare\ListCompare $catalogProductCompareList
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param PageFactory $resultPageFactory
     * @param ProductRepositoryInterface $productRepository
     * @param DecoderInterface $urlDecoder
     * @param CheckoutSession $checkoutSession
     * @param Registry $registry
     */
    function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Product\Compare\ItemFactory $compareItemFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Visitor $customerVisitor,
        \Magento\Catalog\Model\Product\Compare\ListCompare $catalogProductCompareList,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        PageFactory $resultPageFactory,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Url\DecoderInterface $urlDecoder,
        CheckoutSession $checkoutSession,
        Registry $registry
)
    {
        parent::__construct(
            $context,
            $compareItemFactory,
            $itemCollectionFactory,
            $customerSession,
            $customerVisitor,
            $catalogProductCompareList,
            $catalogSession,
            $storeManager,
            $formKeyValidator,
            $resultPageFactory,
            $productRepository,
            $urlDecoder);
        $this->checkoutSession = $checkoutSession;
        $this->_registry = $registry;
    }

    function execute()
    {
        $items = $this->getRequest()->getParam('items');

        $beforeUrl = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED);
        if ($beforeUrl) {
            $this->_catalogSession->setBeforeCompareUrl(
                $this->urlDecoder->decode($beforeUrl)
            );
        }

        if ($items) {
            $items = explode(',', $items);

            $sessionIds = [];
            if (isset($_SESSION['catalog']['is_incognito']) && $_SESSION['catalog']['is_incognito'] == 1) {
                if (isset($_SESSION['catalog']['compare_ids'])) {
                    $sessionIds = $_SESSION['catalog']['compare_ids'];
                }
            }
            $items = array_unique(array_merge_recursive($sessionIds, $items));

            /** @var \Magento\Catalog\Model\Product\Compare\ListCompare $list */
            $list = $this->_catalogProductCompareList;
            $list->addProducts($items);
        }
        return $this->resultPageFactory->create();
    }
}
