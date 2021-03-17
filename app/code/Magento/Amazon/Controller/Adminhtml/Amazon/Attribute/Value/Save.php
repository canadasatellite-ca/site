<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Attribute\Value;

use Magento\Amazon\Api\AttributeRepositoryInterface;
use Magento\Amazon\Api\Data\AttributeInterface;
use Magento\Amazon\Model\Indexer\AttributeProcessor;
use Magento\Amazon\Model\ResourceModel\Amazon\Attribute\Value as ValueResourceModel;
use Magento\Amazon\Ui\AdminStorePageUrl;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Model\Config as AttributeConfig;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Filter\FilterManager;

/**
 * Class Save
 */
class Save extends Action
{
    /** @var string */
    const BACKEND_TYPE = 'varchar';
    /** @var int */
    const SEARCH_WEIGHT = 99;

    /** @var AttributeRepositoryInterface $attributeRepository */
    protected $attributeRepository;
    /** @var ValueResourceModel $valueResourceModel */
    protected $valueResourceModel;
    /** @var FilterManager $filter */
    protected $filter;
    /** @var ProductAttributeRepositoryInterface $productAttributeRepository */
    protected $productAttributeRepository;
    /** @var ProductAttributeInterface $productAttribute */
    protected $productAttribute;
    /** @var AttributeProcessor $attributeProcessor */
    protected $attributeProcessor;
    /** @var AttributeConfig $attributeConfig */
    protected $attributeConfig;
    /**
     * @var AdminStorePageUrl
     */
    private $adminStorePageUrl;

    /**
     * @param Action\Context $context
     * @param AttributeRepositoryInterface $attributeRepository
     * @param ValueResourceModel $valueResourceModel
     * @param FilterManager $filter
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param ProductAttributeInterface $productAttribute
     * @param AttributeProcessor $attributeProcessor
     * @param AttributeConfig $attributeConfig
     * @param AdminStorePageUrl $adminStorePageUrl
     */
    public function __construct(
        Action\Context $context,
        AttributeRepositoryInterface $attributeRepository,
        ValueResourceModel $valueResourceModel,
        FilterManager $filter,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        ProductAttributeInterface $productAttribute,
        AttributeProcessor $attributeProcessor,
        AttributeConfig $attributeConfig,
        AdminStorePageUrl $adminStorePageUrl
    ) {
        parent::__construct($context);
        $this->attributeRepository = $attributeRepository;
        $this->valueResourceModel = $valueResourceModel;
        $this->filter = $filter;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productAttribute = $productAttribute;
        $this->attributeProcessor = $attributeProcessor;
        $this->attributeConfig = $attributeConfig;
        $this->adminStorePageUrl = $adminStorePageUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $request = $this->getRequest();
        $attributeId = $request->getParam('id');
        $url = $this->adminStorePageUrl->attributePage((string)$attributeId);

        try {
            /** @var AttributeInterface */
            $attribute = $this->attributeRepository->getById($attributeId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Unable to load the selected attribute. Please try again.'));
            return $resultRedirect->setPath($url);
        }

        /** @var string */
        $attributeCode = $request->getParam('catalog_attribute');
        /** @var string */
        $attributeSetIds = $request->getParam('attribute_set_ids');
        $attributeSetIds = ($attributeSetIds) ? implode(',', $attributeSetIds) : '';
        /** @var string */
        $storeIds = $request->getParam('store_ids');
        $storeIds = ($storeIds) ? implode(',', $storeIds) : '';
        /** @var string */
        $type = $request->getParam('type');
        /** @var bool */
        $inSearch = $request->getParam('is_searchable');
        /** @var bool */
        $comparable = $request->getParam('is_comparable');
        /** @var bool */
        $inNavigation = $request->getParam('is_filterable');
        /** @var bool */
        $inSearchNavigation = $request->getParam('is_filterable_in_search');
        /** @var int */
        $position = $request->getParam('position');
        /** @var bool */
        $inPromo = $request->getParam('is_used_for_promo_rules');
        /** @var bool */
        $isActive = $request->getParam('is_active');
        /** @var bool */
        $overwrite = $request->getParam('overwrite');
        /** @var bool */
        if ($request->getParam('is_global')) {
            $storeIds = '';
        }

        // create new Magento attribute (if applicable)
        if (!$attributeCode) {
            try {
                /** @var string */
                $attributeCode = $this->createAttribute();
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath($url);
            }
        }

        // checks for setting changes to the attribute mapping
        $this->resetAttributeValues($attribute, $attributeCode, $attributeSetIds, $storeIds, $isActive, $overwrite);

        // set attribute values
        $attribute->setCatalogAttribute($attributeCode);
        $attribute->setType($type);
        $attribute->setAttributeSetIds($attributeSetIds);
        $attribute->setStoreIds($storeIds);
        $attribute->setInSearch($inSearch);
        $attribute->setComparable($comparable);
        $attribute->setInNavigation($inNavigation);
        $attribute->setInSearchNavigation($inSearchNavigation);
        $attribute->setPosition($position);
        $attribute->setInPromo($inPromo);
        $attribute->setIsActive($isActive);
        $attribute->setOverwrite($overwrite);

        // save Amazon attribute
        try {
            $attribute = $this->attributeRepository->save($attribute);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Failed to save the changes. Please try again'));
            return $resultRedirect->setPath($url);
        }

        /** @var string */
        $message = 'Successfully updated the attribute values.  ';

        // if active
        if (!$attribute->getIsActive()) {
            $message .= 'The attribute is inactive and will not import values until activated.';
        }

        $this->messageManager->addSuccessMessage(__($message));
        return $resultRedirect->setPath($url);
    }

    /**
     * Checks for changes to one of four attribute settings:
     * magento attribute code, attribute set ids, is active,
     * or overwrite flag
     *
     * If a change is detected, it invalidates the attribute
     * value mappings to force an import of these values
     *
     * @param AttributeInterface $attribute
     * @param string $attributeCode
     * @param string $attributeSetIds
     * @param string $storeIds
     * @param bool $isActive
     * @param bool $overwrite
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function resetAttributeValues(
        AttributeInterface $attribute,
        $attributeCode,
        $attributeSetIds,
        $storeIds,
        $isActive,
        $overwrite
    ) {
        /** @var bool */
        $flag = false;

        // check for changes to attribute settings
        if ($attributeCode != $attribute->getCatalogAttribute()) {
            $flag = true;
        } elseif ($attributeSetIds != $attribute->getAttributeSetIds()) {
            $flag = true;
        } elseif ($storeIds) {
            if ($storeIds != $attribute->getStoreIds()) {
                $flag = true;
            }
        } elseif ($overwrite != $attribute->getOverwrite()) {
            $flag = true;
        } elseif ($isActive) {
            if ($isActive != $attribute->getIsActive()) {
                $flag = true;
            }
        }

        // if change to attribute mapping - reset values and invalidate indexer
        if ($flag) {
            $this->valueResourceModel->clearAttributeValuesByAttributeIds([$attribute->getId()]);
        }
    }

    /**
     * Create new Magento product attribute
     *
     * @return string
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function createAttribute()
    {
        /** @var ProductAttributeInterface */
        $productAttribute = $this->productAttribute;

        /** @var int */
        $entityTypeId = $this->attributeConfig->getEntityType(ProductAttributeInterface::ENTITY_TYPE_CODE)
            ->getEntityTypeId();
        /** @var string */
        $amazonAttribute = $this->getRequest()->getParam('amazon_attribute');
        /** @var string */
        $frontendLabel = ($newName = $this->getRequest()->getParam('new_name')) ? $newName : $amazonAttribute;
        /** @var string */
        $attributeCode =
            ($attributeCode = $this->getRequest()->getParam('new_code')) ? $attributeCode : $amazonAttribute;
        $attributeCode = $this->generateAttributeCode($attributeCode);
        /** @var string */
        $type = ($this->getRequest()->getParam('type') == 2) ? 'select' : 'text';
        /** @var int */
        $isSearchable = $this->getRequest()->getParam('in_search');
        /** @var int */
        $isComparable = $this->getRequest()->getParam('comparable');
        /** @var int */
        $isFilterable = $this->getRequest()->getParam('in_navigation');
        /** @var int */
        $isFilterableInSearch = $this->getRequest()->getParam('in_search_navigation');
        /** @var int */
        $isUsedForPromoRules = $this->getRequest()->getParam('in_promo');
        /** @var int */
        $position = $this->getRequest()->getParam('position');
        /** @var int */
        $isGlobal = $this->getRequest()->getParam('is_global');

        // set data
        $productAttribute->setFrontendLabel($frontendLabel);
        $productAttribute->setAttributeCode($attributeCode);
        $productAttribute->setFrontendInput($type);
        $productAttribute->setBackendType(self::BACKEND_TYPE);
        $productAttribute->setDefaultValuesYesno(false);
        $productAttribute->setIsUserDefined(true);
        $productAttribute->setIsSearchable($isSearchable);
        $productAttribute->setSearchWeight(self::SEARCH_WEIGHT);
        $productAttribute->setIsComparable($isComparable);
        $productAttribute->setIsFilterable($isFilterable);
        $productAttribute->setIsGlobal($isGlobal);
        $productAttribute->setIsFilterableInSearch($isFilterableInSearch);
        $productAttribute->setIsUsedForPromoRules($isUsedForPromoRules);
        $productAttribute->setPosition($position);
        $productAttribute->setEntityTypeId($entityTypeId);

        // save catalog product attribute
        try {
            $productAttribute = $this->productAttributeRepository->save($productAttribute);
        } catch (NoSuchEntityException $e) {
            $phrase = __('An error occured while trying to save the attribute. Please try again.');
            throw new NoSuchEntityException($phrase);
        } catch (AlreadyExistsException $e) {
            $phrase = __('An attribute with the same name already exists. Please try again.');
            throw new NoSuchEntityException($phrase);
        } catch (InputException $e) {
            $phrase = __('An error occured while trying to save the attribute. Please try again.');
            throw new NoSuchEntityException($phrase);
        } catch (StateException $e) {
            $phrase = __('An error occured while trying to save the attribute. Please try again.');
            throw new NoSuchEntityException($phrase);
        } catch (\Exception $e) {
            $phrase = __('An error occured while trying to save the attribute. Please try again.');
            throw new NoSuchEntityException($phrase);
        }

        return $productAttribute->getAttributeCode();
    }

    /**
     * Generate code from label
     *
     * @param string $label
     * @return string
     * @throws \Zend_Validate_Exception
     */
    private function generateAttributeCode($label)
    {
        // format code
        $code = substr(
            preg_replace(
                '/[^a-z_0-9]/',
                '_',
                $this->filter->translitUrl($label)
            ),
            0,
            30
        );

        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);

        // check for valid code
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(crypt('sha256', time()), 0, 8));
        }

        // check if attribute code already exists
        try {
            $this->productAttributeRepository->get($code);
            return false;
        } catch (NoSuchEntityException $e) {
            return $code;
        }
    }
}
