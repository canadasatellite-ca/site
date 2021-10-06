<?php

namespace CanadaSatellite\AstIntegration\LogicProcessors;

class EmailSender {
    const SENDER_NAME = 'CANADA SATELLITE';
    const SENDER_EMAIL = 'sales@canadasatellite.ca';

    protected $objectManager;
    protected $inlineTranslation;
    protected $dataObject;
    protected $escaper;
    protected $scopeConfig;
    protected $transportBuilder;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface          $objectManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Escaper                         $escaper,
        \Magento\Framework\DataObject                      $DataObject,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder  $transportBuilder
    ) {
        $this->objectManager = $objectManager;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->dataObject = $DataObject;
        $this->escaper = $escaper;
        $this->inlineTranslation = $inlineTranslation;
    }

    public function sendTopUpEmail($sim, \Magento\Sales\Model\Order $order, string $message, string $to) {
        $templateId = 'ast_top_up_report';

        $templateVars = [
            'sim_number' => gettype($sim) === 'object' ? $sim->cs_number : $sim,
            'order_id' => $order->getIncrementId(),
            'message' => $message
        ];

        $from = ['email' => EmailSender::SENDER_EMAIL, 'name' => EmailSender::SENDER_NAME];
        $this->inlineTranslation->suspend();

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
        ];
        $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($from)
            ->addTo($to)
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}
