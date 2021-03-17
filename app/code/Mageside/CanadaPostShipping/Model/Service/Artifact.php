<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Service;

/**
 * Class Artifact
 * @package Mageside\CanadaPostShipping\Model\Service
 * @documentation https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/shippingmanifest/soap/shipmentartifact.jsf
 */
class Artifact extends \Mageside\CanadaPostShipping\Model\Service\AbstractService
{
    /**
     * @param $artifacts
     * @return string
     */
    public function getArtifacts($artifacts)
    {
        $artifactData = [];
        foreach ($artifacts as $artifact) {
            if ($artifactContent = $this->getArtifactData(
                $artifact['artifact_id'],
                !empty($artifact['page_index']) ? $artifact['page_index'] : 0)
            ) {
                $artifactData[] = $artifactContent;
            }
        }

        return $this->combineLabelsPdf($artifactData);
    }

    /**
     * Get Print Shipping Labels
     *
     * @param $artifactId
     * @param $pageIndex
     * @return string
     */
    protected function getArtifactData($artifactId, $pageIndex = 0)
    {
        // Execute Request
        $client = $this->createSoapClient('artifact');
        $result = $client->__soapCall('GetArtifact', [
            'get-artifact-request' => [
                'mailed-by'     => $this->_carrierHelper->getMailedBy(),
                'artifact-id'   => $artifactId,
                'page-index'    => $pageIndex
            ]
        ], null, null);

        // Parse Response
        if (isset($result->{'artifact-data'})) {
            if ($result->{'artifact-data'}->{'mime-type'} == 'application/pdf') {
                return base64_decode($result->{'artifact-data'}->{'image'});
            }
        }

        return '';
    }

    /**
     * Combine array of labels as instance PDF
     *
     * @param array $artifactContent
     * @return string
     */
    protected function combineLabelsPdf(array $artifactContent)
    {
        $outputPdf = new \Zend_Pdf();
        foreach ($artifactContent as $content) {
            if (stripos($content, '%PDF-') !== false) {
                $pdfLabel = \Zend_Pdf::parse($content);
                foreach ($pdfLabel->pages as $page) {
                    $outputPdf->pages[] = clone $page;
                }
            }
        }

        return $outputPdf->render();
    }
}
