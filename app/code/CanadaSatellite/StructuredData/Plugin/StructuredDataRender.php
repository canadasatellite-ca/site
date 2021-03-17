<?php

namespace CanadaSatellite\StructuredData\Plugin;

use Exception;
use Magento\Catalog\Helper\Data as CatalogData;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Manager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Config\Renderer;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Search\Helper\Data as SearchHelper;
use Magento\Store\Model\StoreManagerInterface;
use CanadaSatellite\StructuredData\Helper\Data as HelperData;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\CollectionFactory as ReviewCollection;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Model\Category;

/**
 * Class StructuredDataRender
 * @package CanadaSatellite\StructuredData\Plugin
 */
class StructuredDataRender
{
    const GOOLE_SITE_VERIFICATION = 'google-site-verification';
    const MSVALIDATE_01           = 'msvalidate.01';
    const P_DOMAIN_VERIFY         = 'p:domain_verify';
    const YANDEX_VERIFICATION     = 'yandex-verification';

    /**
     * @var PageConfig
     */
    protected $pageConfig;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var CatalogData
     */
    protected $catalogData;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var StockRegistryInterface
     */
    protected $stockState;

    /**
     * @var SearchHelper
     */
    protected $_searchHelper;

    /**
     * @var PriceHelper
     */
    protected $_priceHelper;

    /**
     * @var Manager
     */
    protected $_eventManager;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timeZoneInterface;

    /**
     * @var ReviewCollection
     */
    protected $_reviewCollection;

    /**
     * @var ModuleManager
     */
    protected $_moduleManager;

    /**
     * @var ImageBuilder
     */
    protected $_imageBuilder;

    /**
     * @var Category
     */
    protected $_categoryImageBuilder;

    /**
     * SeoRender constructor.
     *
     * @param PageConfig $pageConfig
     * @param Http $request
     * @param HelperData $helpData
     * @param StockItemRepository $stockItemRepository
     * @param Registry $registry
     * @param ReviewFactory $reviewFactory
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param ProductFactory $productFactory
     * @param ManagerInterface $messageManager
     * @param StockRegistryInterface $stockState
     * @param SearchHelper $searchHelper
     * @param PriceHelper $priceHelper
     * @param Manager $eventManager
     * @param DateTime $dateTime
     * @param TimezoneInterface $timeZoneInterface
     * @param ReviewCollection $reviewCollection
     * @param ModuleManager $moduleManager
     * @param ImageBuilder $_imageBuilder
     * @param Category $_categoryImageBuilder
     */
    function __construct(
        PageConfig $pageConfig,
        Http $request,
        CatalogData $catalogData,
        HelperData $helpData,
        StockItemRepository $stockItemRepository,
        Registry $registry,
        ReviewFactory $reviewFactory,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        ProductFactory $productFactory,
        ManagerInterface $messageManager,
        StockRegistryInterface $stockState,
        SearchHelper $searchHelper,
        PriceHelper $priceHelper,
        Manager $eventManager,
        DateTime $dateTime,
        TimezoneInterface $timeZoneInterface,
        ReviewCollection $reviewCollection,
        ModuleManager $moduleManager,
        ImageBuilder $_imageBuilder,
        Category $_categoryImageBuilder
    ) {
        $this->pageConfig = $pageConfig;
        $this->request = $request;
        $this->catalogData = $catalogData;
        $this->helperData = $helpData;
        $this->stockItemRepository = $stockItemRepository;
        $this->registry = $registry;
        $this->_storeManager = $storeManager;
        $this->reviewFactory = $reviewFactory;
        $this->_urlBuilder = $urlBuilder;
        $this->productFactory = $productFactory;
        $this->messageManager = $messageManager;
        $this->stockState = $stockState;
        $this->_searchHelper = $searchHelper;
        $this->_priceHelper = $priceHelper;
        $this->_eventManager = $eventManager;
        $this->_dateTime = $dateTime;
        $this->_timeZoneInterface = $timeZoneInterface;
        $this->_reviewCollection = $reviewCollection;
        $this->_moduleManager = $moduleManager;
        $this->_imageBuilder = $_imageBuilder;
        $this->_categoryImageBuilder = $_categoryImageBuilder;
    }

    /**
     * @param Renderer $subject
     */
    public function beforeRenderMetadata(Renderer $subject)
    {
        $pages = [
            'catalogsearch_result_index',
            'cms_noroute_index',
            'catalogsearch_advanced_result'
        ];
        if (in_array($this->getFullActionName(), $pages)) {
            $this->pageConfig->setMetadata('robots', 'NOINDEX,NOFOLLOW');
        }
    }

    /**
     * @param Renderer $subject
     * @param $result
     *
     * @return string
     */
    public function afterRenderHeadContent(Renderer $subject, $result)
    {
        $result .= $this->showBusinessStructuredData();
        $result .= $this->showSiteLinksStructuredData();
        $result .= $this->showBreadcrumbsStructuredData();
        $fullActionName = $this->getFullActionName();
        switch ($fullActionName) {
            case 'catalog_product_view':
                $productStructuredData = $this->showProductStructuredData();
                $result .= $productStructuredData;
                break;
        }

        return $result;
    }

    /**
     * Get full action name
     * @return string
     */
    public function getFullActionName()
    {
        return $this->request->getFullActionName();
    }

    /**
     * Get current product
     * @return mixed
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get Url
     *
     * @param string $route
     * @param array $params
     *
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * @param $productId
     *
     * @return StockItemInterface
     * @throws NoSuchEntityException
     */
    public function getProductStock($productId)
    {
        return $this->stockItemRepository->get($productId);
    }

    /**
     * Show product structured data
     * @return string
     *
     * Learn more: https://developers.google.com/structured-data/rich-snippets/products#single_product_page
     */
    public function showProductStructuredData()
    {
        if ($currentProduct = $this->getProduct()) {
            try {
                $productId = $currentProduct->getId() ?: $this->request->getParam('id');

                $product = $this->productFactory->create()->load($productId);

                $url = $this->getUrl() . $product->getUrlKey() . '.htm';

                if ($product->isAvailable()
                    && ($product->getData('quantity_and_stock_status')['is_in_stock'])
                    && ($product->getFrontendStock() == 1)
                    && ($product->getQuoteHidePrice() == 0)
                ){
                    $availability = 'InStock';
                } else {
                    $availability = 'OutOfStock';
                }
                $stockItem = $this->stockState->getStockItem(
                    $product->getId(),
                    $product->getStore()->getWebsiteId()
                );
                $upcValue = $product->getResource()
                    ->getAttribute('upc')
                    ->getFrontend()->getValue($product);

                $description = trim(strip_tags($currentProduct->getDescription()));
                $description = substr($description, 0, 250);
                if (strlen($description) == 250) {
                    $dotPosition = strripos ($description, '.');
                    $description = substr($description, 0, $dotPosition+1);
                }

                $images = [];
                if ($imagesArray = $currentProduct->getMediaGallery()['images']) {
                    foreach ($imagesArray as $image) {
                        $images[] = $this->getUrl('pub/media/catalog') . 'product' . $image['file'];
                    }
                } else {
                    $images = $this->getUrl('pub/media/catalog') . 'product' . $currentProduct->getImage();
                }

                $productStructuredData = [
                    '@context'    => 'http://schema.org/',
                    '@type'       => 'Product',
                    'name'        => $currentProduct->getName(),
                    'description' => $description,
                    'sku'         => $currentProduct->getSku(),
                    'url'         => $url,
                    'image'       => $images,
                    'mpn'         => $currentProduct->getSku(),
                    'offers'      => [
                        '@type'         => 'Offer',
                        'priceCurrency' => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                        'price'         => $currentProduct->getPriceInfo()->getPrice('final_price')->getValue(),
                        'itemOffered'   => $stockItem->getQty(),
                        'availability'  => 'http://schema.org/' . $availability,
                        'url'           => $url
                    ]
                ];
                if ($upcValue) {
                    $productStructuredData['gtin'] = $upcValue;
                }
                $productStructuredData = $this->addProductStructuredDataByType(
                    $currentProduct->getTypeId(),
                    $currentProduct,
                    $productStructuredData
                );

                $priceValidUntil = $currentProduct->getSpecialToDate();

                if (!empty($priceValidUntil)) {
                    $productStructuredData['offers']['priceValidUntil'] = $priceValidUntil;
                } else {
                    $time = $this->_dateTime->gmtTimestamp() + 2592000;
                    $productStructuredData['offers']['priceValidUntil'] = date('Y-m-d', $time);
                }

                $brandValue = $product->getResource()
                    ->getAttribute('brand')
                    ->getFrontend()->getValue($product);

                $productStructuredData['brand']['@type'] = 'Thing';
                $productStructuredData['brand']['name'] = $brandValue ?: 'Brand';

                /**
                 * @var $collection \Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection
                 */
                $collection = $this->_reviewCollection->create();
                $collection
                    ->addFieldToFilter('product_id', $product->getId())
                    ->addFieldToFilter('status', Review::STATUS_APPROVED)
                    ->load();

                $ratingValue = 0;
                $ratingCount = 0;
                if ($collection->getItems()) {
                    foreach ($collection->getData() as $review) {
                        $productStructuredData['review'][] = [
                            '@type' => 'Review',
                            'reviewRating' => [
                                '@type' => 'Rating',
                                'ratingValue' => round($review['rating'] / 20, 2),
                                'bestRating' => '5',
                                'worstRating' => '0'
                            ],
                            'name' => $review['summary'],
                            'author' => [
                                '@type' => 'Person',
                                'name' => $review['nickname']
                            ],
                            //'reviewBody'  => $review['content']
                        ];
                        $ratingCount++;
                        $ratingValue += $review['rating'];
                    }

                    $ratingValue = round($ratingValue / $ratingCount / 20, 2);

                    if ($ratingCount > 0) {
                        $productStructuredData['aggregateRating']['@type'] = 'AggregateRating';
                        $productStructuredData['aggregateRating']['bestRating'] = 5;
                        $productStructuredData['aggregateRating']['worstRating'] = 0;
                        $productStructuredData['aggregateRating']['ratingValue'] = $ratingValue;
                        $productStructuredData['aggregateRating']['reviewCount'] = $ratingCount;
                    }
                }

                $objectStructuredData = new DataObject(['casatdata' => $productStructuredData]);
                $this->_eventManager->dispatch(
                    'casat_seo_product_structured_data',
                    ['structured_data' => $objectStructuredData]
                );
                $productStructuredData = $objectStructuredData->getCasatdata();

                return $this->helperData->createStructuredData(
                    $productStructuredData,
                    'product-json-ld',
                    '<!-- Product Structured Data -->'
                );
            } catch (Exception $e) {
                $this->messageManager->addError(__('Can not add structured data'));
            }
        }
    }

    /**
     * get Business Structured Data
     *
     * @return string
     */
    public function showBusinessStructuredData()
    {
        if (stripos($this->getUrl(), 'https://') === 0) {
            $siteAddress = mb_substr(str_replace('https://www.', '', $this->getUrl()), 0, -1);
        } else {
            $siteAddress = 'canadasatellite.ca';
        }
        $email = 'sales@' . $siteAddress;

        $lastDot = strrpos($siteAddress, '.');

        $siteName = ucfirst(substr($siteAddress, 0, $lastDot));

        $businessStructuredData = [
            '@context'     => 'http://schema.org/',
            '@type'        => 'Organization',
            'name'         => $siteName,
            'description'  => 'As the industry leader in mobile satellite communications, 
                               Canada Satellite offers global connectivity solutions to individuals, 
                               businesses and government agencies around the world.',
            'image'        => 'https://www.canadasatellite.ca/media/wysiwyg/home/Phone.jpg',
            'logo'         => $this->helperData->getLogo(),
            'url'          => $this->getUrl(),
            'email'        => $email
        ];

        $businessStructuredData['contactPoint'][] = [
            '@type'         => 'ContactPoint',
            'telephone'     => '1 (403) 918-6300',
            'areaServed'    => 'CA',
            'contactType'   => 'Customer service'
        ];

        $businessStructuredData['address'][] = [
            '@type'           => 'PostalAddress',
            'addressLocality' => 'Calgary',
            'addressRegion'   => 'Alberta',
            'postalCode'      => 'T2E 6R7',
            'streetAddress'   => '2121 39 Ave NE, BAY H',
            'addressCountry'  => [
                '@type'       => 'Country',
                'name'        => 'Canada'
            ]
        ];

        return $this->helperData->createStructuredData(
            $businessStructuredData,
            null,
            '<!-- Business Structured Data -->'
        );
    }

    /**
     * get Sitelinks Searchbox Structured Data
     *
     * @return string
     */
    public function showSiteLinksStructuredData()
    {
        $name = '';
        $alternateName = '';
        $url = $this->_urlBuilder->getBaseUrl();

        switch ($url) {
            case 'https://www.canadasatellite.ca/':
                $name = 'CanadaSatellite.ca';
                $alternateName = ['CanadaSatellite', 'Canada Satellite', 'Canada Satellite Ca', 'CanadaSatellite Ca'];
                break;
            case 'https://www.africasatellite.com/':
                $name = 'AfricaSatellite.ca';
                $alternateName = ['AfricaSatellite', 'Africa Satellite', 'Africa Satellite Com', 'AfricaSatellite Com'];
                break;
            case 'https://www.australiasatellite.co/':
                $name = 'Australiasatellite.co';
                $alternateName = ['AustraliaSatellite', 'Australia Satellite', 'Australia Satellite Co', 'AustraliaSatellite Co'];
                break;
            case 'https://www.asiasatellite.co/':
                $name = 'AsiaSatellite.co';
                $alternateName = ['AsiaSatellite', 'Asia Satellite', 'Asia Satellite Co', 'AsiaSatellite Co'];
                break;
            case 'https://www.europasatellite.com/':
                $name = 'EuropaSatellite.ca';
                $alternateName = ['EuropaSatellite', 'Europa Satellite', 'Europa Satellite Ca', 'Europasatellite Ca'];
                break;
            case 'https://www.calgarysatellite.ca/':
                $name = 'CalgarySatellite.ca';
                $alternateName = ['CalgarySatellite', 'Calgary Satellite', 'Calgary Satellite Ca', 'CalgarySatellite Ca'];
                break;
            case 'https://www.americansatellite.us/':
                $name = 'AmericanSatellite.us';
                $alternateName = ['AmericanSatellite', 'American Satellite', 'American Satellite Us', 'AmericanSatellite Us'];
                break;
            case 'https://www.universalrv.ca/':
                $name = 'UniversalRv.ca';
                $alternateName = ['UniversalRv', 'Universal Rv', 'Universal Rv Ca', 'UniversalRv Ca'];
                break;
            case 'https://www.universalmaritime.ca/':
                $name = 'UniversalMaritime.ca';
                $alternateName = ['UniversalMaritime', 'Universal Maritime', 'Universal Maritime Ca', 'UniversalMaritime Ca'];
                break;
            case 'https://www.satelliterentals.ca/':
                $name = 'SatelliteRentals.ca';
                $alternateName = ['SatelliteRentals', 'Satellite Rentals', 'Satellite Rentals Ca', 'SatelliteRentals Ca'];
                break;
            case 'https://www.oilsat.ca/':
                $name = 'OilSat.ca';
                $alternateName = ['OilSat', 'Oil Sat', 'Oil Sat Ca', 'OilSat Ca'];
                break;
            case 'https://staging.canadasatellite.ca/':
                $name = 'CanadaSatellite.ca';
                $alternateName = ['CanadaSatellite', 'Canada Satellite'];
                break;
            default:
                $name = 'CanadaSatellite.ca';
                $alternateName = ['CanadaSatellite', 'Canada Satellite', 'Canada Satellite Ca', 'CanadaSatellite Ca'];
                break;
        }

        $siteLinksStructureData = [
            '@context'        => 'http://schema.org',
            '@type'           => 'WebSite',
            'name'            => $name,
            'alternateName'   => $alternateName,
            'url'             => $this->_urlBuilder->getBaseUrl(),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => $this->_searchHelper->getResultUrl() . '?q={searchbox_target}',
                'query-input' => 'required name=searchbox_target'
            ]
        ];

        return $this->helperData->createStructuredData(
            $siteLinksStructureData,
            'website-json-ld',
            '<!-- Sitelinks Searchbox Structured Data -->'
        );
    }

    /**
     * get Breadcrumbs Structured Data
     *
     * @return string
     */
    public function showBreadcrumbsStructuredData()
    {
        $breadcrumbsStructureData = [
            '@context'        => 'http://schema.org',
            '@type'           => 'BreadcrumbList'
        ];

        $breadcrumbsCollection = $this->catalogData->getBreadcrumbPath();
        $breadcrumbsStructureData['itemListElement'][] = [
            '@type'  => 'ListItem',
            'position' => 0,
            'name' => 'Home',
            'image' => $this->helperData->getLogo(),
            'item' => [
                '@type'  => 'Thing',
                '@id' => $this->_storeManager->getStore()->getBaseUrl()
            ]
        ];
        $i = 1;
        foreach ($breadcrumbsCollection as $key => $breadcrumb) {
            if (isset($breadcrumb['link'])) {
                $link = $breadcrumb['link'];
                if ($link == ''){
                    $link = $this->_storeManager->getStore()->getBaseUrl() . substr($this->request->getOriginalPathInfo(),1);
                }
                $currentCategory = $this->_categoryImageBuilder->load(substr($key, 8));
                $image = $this->getCategoryImage($currentCategory, 'image');
            } else {
                $link = $this->_storeManager->getStore()->getBaseUrl() . $this->getProduct()->getUrlKey() . '.htm';
                $image = $this->getUrl('pub/media/catalog') . 'product' . $this->getProduct()->getImage();
            }
            $breadcrumbsStructureData['itemListElement'][] = [
                '@type'  => 'ListItem',
                'position' => $i,
                'name' => $breadcrumb['label'],
                'image' => $image,
                'item' => [
                    '@type'  => 'Thing',
                    '@id' => $link,
                ]
            ];
            $i++;
        }

        return $this->helperData->createStructuredData(
            $breadcrumbsStructureData,
            null,
            '<!-- Breadcrumbs Structured Data -->'
        );
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */

    public function getProductImage($product, $imageId, $attributes = [])
    {
        return $this->_imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * Retrieve category image
     *
     * @param \Magento\Catalog\Model\Category $product
     * @param string $imageId
     * @param array $attributes
     * @return @string
     */

    public function getCategoryImage($category, $attributeCode = 'image')
    {
        $url = false;
        $image = $category->getData($attributeCode);
        if ($image) {
            if (is_string($image)) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ) . 'catalog/category/' . $image;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    /**
     * add Grouped Product Structured Data
     *
     * @param $currentProduct
     * @param $productStructuredData
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getGroupedProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type'] = 'AggregateOffer';
        $childrenPrice = [];
        $offerData = [];
        $typeInstance = $currentProduct->getTypeInstance();
        $childProductCollection = $typeInstance->getAssociatedProducts($currentProduct);
        foreach ($childProductCollection as $child) {
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                        . 'catalog/product' . $child->getImage();

            $offerData[] = [
                '@type' => 'Offer',
                'name'  => $child->getName(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false),
                'sku'   => $child->getSku(),
                'image' => $imageUrl
            ];
            $childrenPrice[] = $this->_priceHelper->currency($child->getPrice(), false);
        }

        $productStructuredData['offers']['highPrice'] = array_sum($childrenPrice);
        $productStructuredData['offers']['lowPrice'] = min($childrenPrice);
        unset($productStructuredData['offers']['price']);

        if (!empty($offerData)) {
            $productStructuredData['offers']['offers'] = $offerData;
        }

        return $productStructuredData;
    }

    /**
     * add Downloadable Product Structured Data
     *
     * @param $currentProduct
     * @param $productStructuredData
     *
     * @return mixed
     */
    public function getDownloadableProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type'] = 'AggregateOffer';

        $typeInstance = $currentProduct->getTypeInstance();
        $childProductCollection = $typeInstance->getLinks($currentProduct);
        $childrenPrice = [];
        foreach ($childProductCollection as $child) {
            $offerData[] = [
                '@type' => 'Offer',
                'name'  => $child->getTitle(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false)
            ];
            $childrenPrice[] = $this->_priceHelper->currency($child->getPrice(), false);
        }
        $productStructuredData['offers']['highPrice'] = array_sum($childrenPrice);
        $productStructuredData['offers']['lowPrice'] = min($childrenPrice);

        if (!empty($offerData)) {
            $productStructuredData['offers']['offers'] = $offerData;
        }

        return $productStructuredData;
    }

    /**
     * add Configurable Product Structured Data
     *
     * @param $currentProduct
     * @param $productStructuredData
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getConfigurableProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type'] = 'AggregateOffer';
        $productStructuredData['offers']['highPrice'] = $currentProduct->getPriceInfo()->getPrice('regular_price')->getMaxRegularAmount()->getValue();
        $productStructuredData['offers']['lowPrice'] = $currentProduct->getPriceInfo()->getPrice('regular_price')->getMinRegularAmount()->getValue();
        $offerData = [];
        $typeInstance = $currentProduct->getTypeInstance();
        $childProductCollection = $typeInstance->getUsedProductCollection($currentProduct)->addAttributeToSelect('*');
        foreach ($childProductCollection as $child) {
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                        . 'catalog/product' . $child->getImage();

            $offerData[] = [
                '@type' => 'Offer',
                'name'  => $child->getName(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false),
                'sku'   => $child->getSku(),
                'image' => $imageUrl
            ];
        }
        if (!empty($offerData)) {
            $productStructuredData['offers']['offers'] = $offerData;
        }

        return $productStructuredData;
    }

    /**
     * add Bundle Product Structured Data
     *
     * @param $currentProduct
     * @param $productStructuredData
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getBundleProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type'] = 'AggregateOffer';
        $productStructuredData['offers']['highPrice'] = $currentProduct->getPriceInfo()->getPrice('regular_price')->getMaximalPrice()->getValue();
        $productStructuredData['offers']['lowPrice'] = $currentProduct->getPriceInfo()->getPrice('regular_price')->getMinimalPrice()->getValue();
        unset($productStructuredData['offers']['price']);
        $offerData = [];
        $typeInstance = $currentProduct->getTypeInstance();
        $childProductCollection = $typeInstance->getSelectionsCollection(
            $typeInstance->getOptionsIds($currentProduct),
            $currentProduct
        );
        $offerCount = 0;
        foreach ($childProductCollection as $child) {
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                        . 'catalog/product' . $child->getImage();

            $offerData[] = [
                '@type' => 'Offer',
                'name'  => $child->getName(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false),
                'sku'   => $child->getSku(),
                'image' => $imageUrl
            ];

            $offerCount ++;
        }
        if (!empty($offerData)) {
            $productStructuredData['offers']['offers'] = $offerData;
            $productStructuredData['offers']['offerCount'] = $offerCount;
        }

        return $productStructuredData;
    }

    /**
     * @param $productType
     * @param $currentProduct
     * @param $productStructuredData
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function addProductStructuredDataByType($productType, $currentProduct, $productStructuredData)
    {
        switch ($productType) {
            case 'grouped':
                $productStructuredData = $this->getGroupedProductStructuredData(
                    $currentProduct,
                    $productStructuredData
                );
                break;
            case 'bundle':
                $productStructuredData = $this->getBundleProductStructuredData($currentProduct, $productStructuredData);
                break;
            case 'downloadable':
                $productStructuredData = $this->getDownloadableProductStructuredData(
                    $currentProduct,
                    $productStructuredData
                );
                break;
            case 'configurable':
                $productStructuredData = $this->getConfigurableProductStructuredData(
                    $currentProduct,
                    $productStructuredData
                );
                break;
        }

        return $productStructuredData;
    }
}
