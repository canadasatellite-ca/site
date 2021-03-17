<?php	
	/**		
		* Copyright © 2015 Magento. All rights reserved.		
		* See COPYING.txt for license details.		
	*/	
	
	// @codingStandardsIgnoreFile	
	
	namespace MageSuper\AdvancedSubcategoryList\Block;	
	
	/**		
		* Catalog Products List widget block		
		* Class ProductsList		
		* @SuppressWarnings(PHPMD.CouplingBetweenObjects)		
	*/	
	class Catlist extends \Magento\Catalog\Block\Category\View	
	{		
		
		//protected $_storeManager;		
		protected $categoryHelper;		
		public function __construct(		
        \Magento\Framework\View\Element\Template\Context $context,		
        \Magento\Catalog\Model\Layer\Resolver $layerResolver, 		
        \Magento\Framework\Registry $registry, 		
        \Magento\Catalog\Helper\Category $categoryHelper, 		
        \Magento\Catalog\Model\CategoryFactory  $categoryFactory,		
		
        array $data = array()) 		
		{			
			parent::__construct($context, $layerResolver, $registry, $categoryHelper,$data);			
			$this->_categoryFactory = $categoryFactory;			
			$this->categoryHelper = $categoryHelper;			
			//$this->_storeManager = $storeManager;			
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
		public function getCurrentCategoryChildernColumnCount()		
		{			
			return $this->getCurrentCategory()->getNoofsubcategories();			
		}				
		public function getCurrentCategoryMobileChildernColumnCount()	
		{	
			return $this->getCurrentCategory()->getNoofsubcategoriesmobile();	
		}
        public function getCurrentCategoryTabletChildernColumnCount()
        {
            return $this->getCurrentCategory()->getNoofsubcategoriestablet();
        }
		public function getPlaceholderImage()		
		{			
			return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/category/subcategory.jpg';			
		}		
		public function getCategoryList()		
		{			
			$_category  = $this->getCurrentCategory();			
			$collection = $this->_categoryFactory->create()->getCollection()->addAttributeToSelect('*')			
			->addAttributeToFilter('is_active', 1)			
			->setOrder('position', 'ASC')			
			->addIdFilter($_category->getChildren());			
			return $collection;			
			
		}	
		public function getSpecificCategoryUrl($category)
		{
			
			return 	$this->categoryHelper->getCategoryUrl($category);
		}
	}	
