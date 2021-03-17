<?php

namespace Magedelight\Faqs\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class Questiontab extends AbstractModifier
{

    const DATA_SCOPE = '';
    
    const DATA_SCOPE_QUESTIONS = 'questions';
    
    const GROUP_QUESTIONS = 'questions';
    /**
     * @var string
     */
    private static $previousGroup = 'search-engine-optimization';
    
    private static $sortOrder = 90;

    public $locator;

    public $websiteRepository;

    public $groupRepository;

    public $storeRepository;

    public $websitesOptionsList;

    public $storeManager;
    /**
     * @var string
     */
    public $scopeName;
    /**
     * @var string
     */
    public $scopePrefix;
    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    public $websitesList;
    
    public $productQuestionCollection;

    private $dataScopeName;
    
    public $status;
    
    public $questiontype;
    
    public $create;

    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        WebsiteRepositoryInterface $websiteRepository,
        GroupRepositoryInterface $groupRepository,
        StoreRepositoryInterface $storeRepository,
        \Magedelight\Faqs\Model\ResourceModel\Product\CollectionFactory $productQuestionCollection,
        \Magedelight\Faqs\Model\ResourceModel\Faq\CollectionFactory $faqQuestionCollection,
        \Magedelight\Faqs\Model\Source\Faq\Status $status,
        \Magedelight\Faqs\Model\Source\Faq\Questiontype $questiontype,
        \Magedelight\Faqs\Model\Source\Faq\Created $create,
        $dataScopeName,
        $scopeName = '',
        $scopePrefix = ''
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->websiteRepository = $websiteRepository;
        $this->groupRepository = $groupRepository;
        $this->storeRepository = $storeRepository;
        $this->dataScopeName = $dataScopeName;
        $this->scopeName = $scopeName;
        $this->productQuestionCollection = $productQuestionCollection;
        $this->_faqQuestionCollection = $faqQuestionCollection;
        $this->status = $status;
        $this->questiontype = $questiontype;
        $this->create = $create;
        $this->scopePrefix = $scopePrefix;
    }

    public function modifyData(array $data)
    {
      /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();

        if (!$productId) {
            return $data;
        }

        foreach ($this->getDataScopes() as $dataScope) {
            $data[$productId]['links'][$dataScope] = [];
            $productQuestionsCollection = $this->productQuestionCollection->create()
                                            ->addFieldToFilter('product_id', ['eq' => $productId]);
            foreach ($productQuestionsCollection as $prodColl) {
                if ($this->fillData($prodColl['question_id'])!= '') {
                    $data[$productId]['links'][$dataScope][] = $this->fillData($prodColl['question_id']);
                }
            }
        }
        return $data;
    }
    
    public function fillData($faqids)
    {
        $faqQuestionCollection = $this->_faqQuestionCollection->create()
                    ->addFieldToFilter('question_id', ['eq' => $faqids])
                    ->addFieldToFilter('question_type', [
                        'in' => [\Magedelight\Faqs\Model\Faq::PRODUCT_FAQ ,\Magedelight\Faqs\Model\Faq::BOTH_FAQ]
                        ])
                // @codingStandardsIgnoreStart
                    ->getFirstItem();
                // @codingStandardsIgnoreEnd
        if (isset($faqQuestionCollection) && !empty($faqQuestionCollection->getData())) {
            return [
            'id' => $faqQuestionCollection->getId(),
            'question' => $faqQuestionCollection->getQuestion(),
            'status' => $this->status->getOptionText($faqQuestionCollection->getStatus()),
            'created_by' => $faqQuestionCollection->getCreatedBy(),
            'question_type' => $this->questiontype->getOptionText($faqQuestionCollection->getQuestionType()),
            ];
        } else {
            return "";
        }
    }
    /**
     * Retrieve all data scopes
     *
     * @return array
     */
    // @codingStandardsIgnoreStart
    protected function getDataScopes()
    {
        return [
            static::DATA_SCOPE_QUESTIONS
        ];
    }
    // @codingStandardsIgnoreEnd
   /**
    * {@inheritdoc}
    */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_QUESTIONS => [
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_QUESTIONS => $this->getQuestionFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Related Question'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' =>
                                    $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    ),
                            ],
                        ],

                    ],
                ],
            ]
        );

        return $meta;
    }
    
     /**
      * Prepares config for the Related Question fieldset
      *
      * @return array
      */
    public function getQuestionFieldset()
    {
        $content = __(
            'Related Question are shown to customers in addition to the item the customer is looking at.'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Related Question'),
                    $this->scopePrefix . static::DATA_SCOPE_QUESTIONS
                ),
                'modal' => $this->getGenericModal(
                    __('Add Related Question'),
                    $this->scopePrefix . static::DATA_SCOPE_QUESTIONS
                ),
                static::DATA_SCOPE_QUESTIONS => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_QUESTIONS),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Related Questions'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 10,
                    ],
                ],
            ]
        ];
    }
     /**
      * Retrieve grid
      *
      * @param string $scope
      * @return array
      * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
      */
    public function getGrid($scope)
    {
        $dataProvider = $scope . '_faq_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'question_id',
                            'question' => 'question',
                            'status' => 'status_text',
                            'created_by' => 'created_by',
                            'question_type'=> 'question_type_text'
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }'
                        ],
                        'sortOrder' => 2,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->fillMeta(),
                ],
            ],
        ];
    }
    
    /**
     * Retrieve button set
     *
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     * @return array
     */
    public function getButtonSet(Phrase $content, Phrase $buttonTitle, $scope)
    {
        $modalTarget = 'product_form.product_form.' . static::GROUP_QUESTIONS . '.' . $scope . '.modal';
        
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.' . $scope . '_faq_listing',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],

                ],
            ],
        ];
    }
    
    public function getPanelChildren()
    {
        return [
            'tabname_products_button_set' => $this->getButtonSet()

        ];
    }

    /**
     * Prepares config for modal slide-out panel
     *
     * @param Phrase $title
     * @param string $scope
     * @return array
     */
    public function getGenericModal(Phrase $title, $scope)
    {
        $listingTarget = $scope . '_faq_listing';

        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Add Selected Questions'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $listingTarget,
                                'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => 'questions_faq_listing.'
                                . 'questions_faq_listing.questions_faq_columns.ids',
                                'ns' => $listingTarget,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $modal;
    }
    
    /**
     * Retrieve meta column
     *
     * @return array
     */
    public function fillMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            'question' => $this->getTextColumn('question', false, __('Question'), 10),
            'created_by' => $this->getTextColumn('created_by', false, __('Created By'), 20),
            'status' => $this->getTextColumn('status', true, __('Status'), 30),
            'question_type' =>$this->getTextColumn('question_type', true, __('Question Type'), 40),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
        ];
    }
    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    public function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }
}
