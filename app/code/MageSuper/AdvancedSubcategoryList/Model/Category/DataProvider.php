<?php
	/**
		* Copyright © 2016 Magento. All rights reserved.
		* See COPYING.txt for license details.
	*/
	namespace MageSuper\AdvancedSubcategoryList\Model\Category;
	
	use Magento\Catalog\Model\Category;
	use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
	use Magento\Eav\Api\Data\AttributeInterface;
	use Magento\Eav\Model\Config;
	use Magento\Eav\Model\Entity\Type;
	use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
	use Magento\Store\Model\Store;
	use Magento\Store\Model\StoreManagerInterface;
	use Magento\Ui\Component\Form\Field;
	use Magento\Ui\DataProvider\EavValidationRules;
	use Magento\Catalog\Model\CategoryFactory;
	use Magento\Framework\Exception\NoSuchEntityException;
	
	
	/**
		* Class DataProvider
		*
		* @SuppressWarnings(PHPMD.CouplingBetweenObjects)
	*/
	class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
	{
		
		/**
			* @var array
		*/
		protected $loadedData;
		
		/**
			* @var EavValidationRules
		*/
		protected $eavValidationRules;
		
		/**
			* @var \Magento\Framework\Registry
		*/
		protected $registry;
		
		/**
			* @var \Magento\Framework\App\RequestInterface
		*/
		protected $request;
		
		/**
			* @var Config
		*/
		private $eavConfig;
		
		/**
			* @var StoreManagerInterface
		*/
		private $storeManager;
		
		public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        EavValidationRules $eavValidationRules,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        Config $eavConfig,
        \Magento\Framework\App\RequestInterface $request,
        CategoryFactory $categoryFactory,
        array $meta = [],
        array $data = []
		) {
			$this->eavValidationRules = $eavValidationRules;
			$this->collection = $categoryCollectionFactory->create();
			$this->collection->addAttributeToSelect('*');
			$this->eavConfig = $eavConfig;
			$this->registry = $registry;
			$this->storeManager = $storeManager;
			$this->request = $request;
			$this->categoryFactory = $categoryFactory;
			parent::__construct($name, $primaryFieldName, $requestFieldName,$eavValidationRules,$categoryCollectionFactory,$storeManager,$registry,$eavConfig,$request,$categoryFactory ,$meta, $data);
			
		}
		
		/**
			* Get data
			*
			* @return array
		*/
		public function getData()
		{
			if (isset($this->loadedData)) {
				return $this->loadedData;
			}
			$category = $this->getCurrentCategory();
			if ($category) {
				$categoryData = $category->getData();
				$categoryData = $this->addUseDefaultSettings($category, $categoryData);
				$categoryData = $this->addUseConfigSettings($categoryData);
				$categoryData = $this->filterFields($categoryData);
				if (isset($categoryData['image'])) {
					unset($categoryData['image']);
					$categoryData['image'][0]['name'] = $category->getData('image');
					$categoryData['image'][0]['url'] = $category->getImageUrl();
				}
				if (isset($categoryData['thumbnail'])) {
					unset($categoryData['thumbnail']);
					$categoryData['thumbnail'][0]['name'] = $category->getData('thumbnail');
					$categoryData['thumbnail'][0]['url'] = $this->getThumbnailUrl($category);
				}
				$this->loadedData[$category->getId()] = $categoryData;
			}
			return $this->loadedData;
		}
		
		
		
		/**
			* Retrieve image URL
			*
			* @return string
		*/
		public function getThumbnailUrl($category)
		{
			$url = false;
			$image = $category->getThumbnail();
			if ($image) {
				if (is_string($image)) {
					$url = $this->storeManager->getStore()->getBaseUrl(
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
			* @return array
		*/
		protected function getFieldsMap()
		{
			return [
            'general' =>
			[
			'parent',
			'path',
			'is_active',
			'include_in_menu',
			'name',
			'thumbnail',
			],
            'content' =>
			[
			'image',
			
			'description',
			'landing_page',
			],
            'display_settings' =>
			[
			'display_mode',
			'is_anchor',
			'available_sort_by',
			'use_config.available_sort_by',
			'default_sort_by',
			'use_config.default_sort_by',
			'filter_price_range',
			'use_config.filter_price_range',
			],
            'search_engine_optimization' =>
			[
			'url_key',
			'url_key_create_redirect',
			'use_default.url_key',
			'url_key_group',
			'meta_title',
			'meta_keywords',
			'meta_description',
			],
            'assign_products' =>
			[
			],
            'design' =>
			[
			'custom_use_parent_settings',
			'custom_apply_to_products',
			'custom_design',
			'page_layout',
			'custom_layout_update',
			],
            'schedule_design_update' =>
			[
			'custom_design_from',
			'custom_design_to',
			],
            'category_view_optimization' =>
			[
			],
            'category_permissions' =>
			[
			],
			];
		}
	}
