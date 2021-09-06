<?php

namespace CanadaSatellite\DynamicsIntegration\Rest;

use CanadaSatellite\DynamicsIntegration\Exception\DynamicsException;
use CanadaSatellite\DynamicsIntegration\Exception\MultipleObjectsReturnedException;
use CanadaSatellite\DynamicsIntegration\Enums\Sims\SortingDirection;
use CanadaSatellite\DynamicsIntegration\Enums\Sims\SimTableField;
use CanadaSatellite\DynamicsIntegration\Enums\Sims\FilterNetworkStatus;

class RestApi {
    private $credentialsProvider;
    private $logger;
    private $httpClient;

    private $accessToken;
    private $expiresAt;

    const LOGIN_URL = 'https://login.microsoftonline.com/CanSat.onmicrosoft.com/oauth2/token';

    function __construct(
        \CanadaSatellite\DynamicsIntegration\Rest\DynamicsCredentialsProvider $credentialsProvider,
        \CanadaSatellite\DynamicsIntegration\Logger\Logger                    $logger
    ) {
        $this->credentialsProvider = $credentialsProvider;
        $this->httpClient = $this->createHttpClient();
        $this->logger = $logger;
    }

    /**
     * @return string Product id
     */
    function createProduct($crmProduct) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPostRequestJson($this->getCrmUrl() . '/api/data/v8.1/products', $headers, json_encode($crmProduct));
        $json = $this->getResponseJsonIfSuccess($response);

        $this->logger->info('createProduct: ' . print_r($json, true));

        return $json->productid;
    }

    function deleteProductDynamicProperty($propertyId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json'
        );

        $response = $this->sendDeleteRequest($this->getCrmUrl() . "/api/data/v8.1/dynamicproperties($propertyId)", $headers);
        return $this->getResponseJsonIfSuccess($response);
    }

    /**
     * @param string $propertyId
     * @param array $crmDynamicProperty
     * @return array
     */
    function updateProductDynamicProperty($propertyId, $crmDynamicProperty) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/dynamicproperties($propertyId)", $headers, json_encode($crmDynamicProperty));
        return $this->getResponseJsonIfSuccess($response);
    }

    /**
     * @param array $crmDynamicProperty
     * @return string
     */
    function createProductDynamicProperty($crmDynamicProperty) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPostRequestJson($this->getCrmUrl() . '/api/data/v8.1/dynamicproperties', $headers, json_encode($crmDynamicProperty));
        $json = $this->getResponseJsonIfSuccess($response);

        return $json->dynamicpropertyid;
    }

    function getProductDynamicProperties($productGuid) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $query = array(
            '$filter' => "_regardingobjectid_value eq $productGuid"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/dynamicproperties', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        return $json->value;
    }

    function updateDynamicPropertiesInOrder($orderGuid, $orderItemOptions) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $query = ['$filter' => "_salesorderid_value eq $orderGuid"];
        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/salesorderdetails', $headers, $query);
        $detailsData = $this->getResponseJsonIfSuccess($response)->value;

        $index = 0;
        foreach ($detailsData as $orderItem) {
            if ($index >= count($orderItemOptions)) {
                break;
            }
            if (is_null($orderItemOptions[$index])) {
                continue;
            }

            $query = ['$filter' => "_regardingobjectid_value eq {$orderItem->salesorderdetailid}"];
            $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/dynamicpropertyinstances', $headers, $query);
            $propInstances = $this->getResponseJsonIfSuccess($response)->value;

            foreach ($propInstances as $propInst) {
				if (!array_key_exists($propInst->_dynamicpropertyid_value, $orderItemOptions[$index])) {
					continue;
				}
                $value = $orderItemOptions[$index][$propInst->_dynamicpropertyid_value];
                if ($propInst->valuestring === $value) {
                    continue;
                }

                $crmJson = ['valuestring' => $value];
                $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/dynamicpropertyinstances({$propInst->dynamicpropertyinstanceid})", $headers, json_encode($crmJson));
            }

            $index++;
        }
    }

    /**
     * @param string $productId
     * @param (string => string)[] $crmProduct
     * @return void
     */
    function updateProduct($productId, $crmProduct) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/products($productId)", $headers, json_encode($crmProduct));
        $json = $this->getResponseJsonIfSuccess($response);
    }

    /**
     * @param string $productId
     * @return void
     */
    function deleteProduct($productId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $response = $this->sendDeleteRequest($this->getCrmUrl() . "/api/data/v8.1/products($productId)", $headers);
        if ($response->getStatusCode() == 404)
            return;

        $this->checkResponseIsSuccess($response);
    }

    /**
     * @param string Product SKU
     * @return string|false Product id
     */
    function findProductIdBySku($sku) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );
        $sanitizedSku = addslashes($sku);
        $query = array(
            '$filter' => "productnumber eq '$sanitizedSku'"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/products', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0]->productid;
    }

    function getProductById($productId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . "/api/data/v8.1/products($productId)", $headers, array());
        $json = $this->getResponseJsonIfSuccess($response);

        return $json;
    }


    /**
     * @param array $crmProductPriceLevel
     * @param string $priceListId
     */
    function createProductPriceLevel($productId, $crmProductPriceLevel) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPostRequestJson($this->getCrmUrl() . '/api/data/v8.1/productpricelevels', $headers, json_encode($crmProductPriceLevel));
        $json = $this->getResponseJsonIfSuccess($response);

        return $json->productpricelevelid;
    }

    function updateProductPriceLevel($productPriceLevelId, $crmProductPriceLevel) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/productpricelevels($productPriceLevelId)", $headers, json_encode($crmProductPriceLevel));
        $json = $this->getResponseJsonIfSuccess($response);
    }

    function findProductPriceLevelIdByProductId($productId, $priceListId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );
        $query = array(
            '$filter' => "(productid/productid eq $productId) and (pricelevelid/pricelevelid eq $priceListId)"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/productpricelevels', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0]->productpricelevelid;
    }


    /**
     * @param string $name
     * @param int $accountNumber
     * @return string Account id
     */
    function createAccount($crmAccount) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8; IEEE754Compatible=true',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPostRequestJson($this->getCrmUrl() . '/api/data/v8.1/accounts', $headers, json_encode($crmAccount));
        $json = $this->getResponseJsonIfSuccess($response);

        return $json->accountid;
    }

    /**
     * @param string $accountId
     * @param array $crmAccount
     */
    function updateAccount($accountId, $crmAccount) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8; IEEE754Compatible=true',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/accounts($accountId)", $headers, json_encode($crmAccount));
        $json = $this->getResponseJsonIfSuccess($response);
    }

    /**
     * @param string $accountId
     * @return void
     */
    function deleteAccount($accountId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $response = $this->sendDeleteRequest($this->getCrmUrl() . "/api/data/v8.1/accounts($accountId)", $headers);
        if ($response->getStatusCode() == 404 || $response->getStatusCode() == 405)
            return;

        $this->checkResponseIsSuccess($response);
    }

    /**
     * @param string $accountId
     * @param string $contactId
     * @return void
     */
    function setAccountPrimaryContact($accountId, $contactId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );
        $data = array(
            'primarycontactid@odata.bind' => "/contacts($contactId)"
        );

        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/accounts($accountId)", $headers, json_encode($data));
        $json = $this->getResponseJsonIfSuccess($response);
    }

    /**
     * @param string $accountId
     * @param string $currencyId
     * @return void
     */
    function setAccountCurrency($accountId, $currencyId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );
        $data = array(
            'transactioncurrencyid@odata.bind' => "/transactioncurrencies($currencyId)"
        );

        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/accounts($accountId)", $headers, json_encode($data));
        $json = $this->getResponseJsonIfSuccess($response);
    }

    /**
     * @param string $company
     * @return string|false Account id
     */
    function findAccountIdByCompany($company) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $sanitizedCompany = addslashes($company);
        $query = array(
            '$filter' => "name eq '$sanitizedCompany'"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/accounts', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0]->accountid;
    }

    function findVendorAccountIdByName($vendor) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $sanitizedVendor = addslashes($vendor);
        $query = array(
            '$filter' => "new_accounttype eq 100000005 and startswith(name, '$sanitizedVendor')"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/accounts', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0]->accountid;
    }


    /**
     * @param string $customerId
     * @param string $email
     * @return object|false Account
     */
    function findAccountByCustomerIdAndEmail($customerId, $email) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $sanitizedCustomerId = addslashes($customerId);
        $sanitizedEmail = addslashes($email);
        $query = array(
            '$filter' => "accountnumber eq '$sanitizedCustomerId' and emailaddress1 eq '$sanitizedEmail'"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/accounts', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0];
    }


    /**
     * @param string $customerId
     * @return object|false Account
     */
    function findAccountByCustomerId($customerId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $sanitizedCustomerId = addslashes($customerId);
        $query = array(
            '$filter' => "accountnumber eq '$sanitizedCustomerId'"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/accounts', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0];
    }

    /**
     * @param string $email
     * @return object|false Account
     */
    function findAccountByEmail($email) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $sanitizedEmail = addslashes($email);
        $query = array(
            '$filter' => "emailaddress1 eq '$sanitizedEmail'"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/accounts', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0];
    }

    /**
     * @param string $accountId
     * @return object[] Contacts
     */
    function findAccountContacts($accountId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $sanitizedAccountId = addslashes($accountId);
        $query = array(
            '$filter' => "_parentcustomerid_value eq $sanitizedAccountId"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/contacts', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        return $json->value;
    }

    /**
     * @param array $crmContact
     * @return string Contact id
     */
    function createContact($crmContact) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8; IEEE754Compatible=true',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPostRequestJson($this->getCrmUrl() . '/api/data/v8.1/contacts', $headers, json_encode($crmContact));
        $json = $this->getResponseJsonIfSuccess($response);

        return $json->contactid;
    }

    /**
     * @param string $contactId
     * @param array $crmContact
     */
    function updateContact($contactId, $crmContact) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8; IEEE754Compatible=true',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/contacts($contactId)", $headers, json_encode($crmContact));
        $json = $this->getResponseJsonIfSuccess($response);
    }

    /**
     * @param string $contactId
     */
    function deleteContact($contactId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $response = $this->sendDeleteRequest($this->getCrmUrl() . "/api/data/v8.1/contacts($contactId)", $headers);
        if ($response->getStatusCode() == 404 || $response->getStatusCode() == 405)
            return;

        $this->checkResponseIsSuccess($response);
    }

    /**
     * @param string $accountId
     * @param string $contactId
     * @return void
     */
    function bindContactToAccount($contactId, $accountId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );
        $data = array(
            'parentcustomerid_account@odata.bind' => "/accounts($accountId)"
        );

        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/contacts($contactId)", $headers, json_encode($data));
        $json = $this->getResponseJsonIfSuccess($response);
    }

    /**
     * @param string $customerId
     * @param string $email
     * @return object|false Contact with parent account
     */
    function findContactByCustomerIdAndEmail($customerId, $email) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $sanitizedNumber = addslashes($customerId);
        $sanitizedEmail = addslashes($email);
        $query = array(
            '$expand' => 'parentcustomerid_account',
            '$filter' => "new_accountnumber eq '$sanitizedNumber' and emailaddress1 eq '$sanitizedEmail'"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/contacts', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0];
    }

    /**
     * @param string $customerId
     * @return object|false Contact with parent account
     */
    function findContactByCustomerId($customerId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $sanitizedNumber = addslashes($customerId);
        $query = array(
            '$expand' => 'parentcustomerid_account',
            '$filter' => "new_accountnumber eq '$sanitizedNumber'"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/contacts', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0];
    }

    /**
     * @param string $email
     * @return object|false Contact with parent account
     */
    function findContactByEmail($email) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $sanitizedEmail = addslashes($email);
        $query = array(
            '$expand' => 'parentcustomerid_account',
            '$filter' => "emailaddress1 eq '$sanitizedEmail'"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/contacts', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0];
    }


    /**
     * @return string|false Currency id
     */
    function findCurrencyIdByIsoCode($isoCode) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );
        $sanitizedIsoCode = addslashes($isoCode);
        $query = array(
            '$filter' => "isocurrencycode  eq '$sanitizedIsoCode'"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/transactioncurrencies', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0]->transactioncurrencyid;
    }

    /**
     * @return string|false Default price list id.
     */
    function getDefaultPriceListId() {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );
        $query = array(
            '$filter' => "cs_isdefault eq true"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/pricelevels', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0]->pricelevelid;
    }


    function createOrder($crmOrder) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPostRequestJson($this->getCrmUrl() . '/api/data/v8.1/salesorders', $headers, json_encode($crmOrder));
        $json = $this->getResponseJsonIfSuccess($response);

        $orderId = $json->salesorderid;

        // Unset customerid_account@odata.bind to set other fields via update.
        unset($crmOrder['customerid_account@odata.bind']);
        // Unset order_details to avoid 'Deep update of navigation properties is not allowed'.
        unset($crmOrder['order_details']);
        $this->updateOrder($orderId, $crmOrder);

        return $orderId;
    }

    function getOrderById($orderId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json; charset=utf-8',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );
        $query = array();
        $response = $this->sendGetRequest($this->getCrmUrl() . "/api/data/v8.1/salesorders($orderId)", $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        return $json;
    }

    function updateOrder($orderId, $crmOrder) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        // Unset customerid_contact@odata.bind to set other fields via update.
        unset($crmOrder['customerid_contact@odata.bind']);

        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/salesorders($orderId)", $headers, json_encode($crmOrder));
        $json = $this->getResponseJsonIfSuccess($response);
    }

    function findOrderByNumber($orderId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );
        $sanitizedNumber = addslashes($orderId);
        $query = array(
            '$filter' => "name eq '$sanitizedNumber'"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/salesorders', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0]->salesorderid;
    }

    function createOrderNote($orderId, $note) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $note["objectid_salesorder@odata.bind"] = "/salesorders($orderId)";

        $response = $this->sendPostRequestJson($this->getCrmUrl() . "/api/data/v8.1/annotations", $headers, json_encode($note));
        $json = $this->getResponseJsonIfSuccess($response);
        return $json->annotationid;
    }

    function findCountryByName($country) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0'
        );

        $sanitizedCountry = addslashes($country);
        $query = array(
            '$filter' => "startswith(new_name, '$sanitizedCountry')"
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/new_countries', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0]->new_countryid;
    }

    function getCardsByCustomerId($customerId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'odata.include-annotations="*"' // to get formatted values for option set (pick list) fields
        );

        $sanitizedCustomerId = addslashes($customerId);
        $query = array(
            '$filter' => "new_account/accountnumber eq '$sanitizedCustomerId'",
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/new_sundrieses', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);
		# 2021-06-01 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		# «count(): Parameter must be an array or an object that implements Countable
		# in app/code/CanadaSatellite/Theme/Model/ResourceModel/Card/Collection.php on line 77»:
		# https://github.com/canadasatellite-ca/site/issues/131
		return df_eta($json->value);
    }

    function getCard($cardId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'odata.include-annotations="*"' // to get formatted values for option set (pick list) fields
        );

        $sanitizedCardId = addslashes($cardId);
        $query = array(
            '$expand' => "new_account",
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . "/api/data/v8.1/new_sundrieses($sanitizedCardId)", $headers, $query);
        if ($response->getStatusCode() == 404) {
            return false;
        }

        $json = $this->getResponseJsonIfSuccess($response);

        return $json;
    }

    function createCard($crmCard) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8; IEEE754Compatible=true',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPostRequestJson($this->getCrmUrl() . '/api/data/v8.1/new_sundrieses', $headers, json_encode($crmCard));
        $json = $this->getResponseJsonIfSuccess($response);

        return $json->new_sundriesid;
    }

    function updateCard($cardId, $crmCard) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/new_sundrieses($cardId)", $headers, json_encode($crmCard));
        $json = $this->getResponseJsonIfSuccess($response);
    }

    function deleteCard($cardId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'odata.include-annotations="*"' // to get formatted values for option set (pick list) fields
        );

        $sanitizedCardId = addslashes($cardId);
        $response = $this->sendDeleteRequest($this->getCrmUrl() . "/api/data/v8.1/new_sundrieses($sanitizedCardId)", $headers);
        if ($response->getStatusCode() == 404 || $response->getStatusCode() == 405)
            return;

        $this->checkResponseIsSuccess($response);
    }

    /*
     * @param array $crmSim
     * @return string Sim identifier
     */
    function createSim($crmSim) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPostRequestJson($this->getCrmUrl() . '/api/data/v8.1/cs_sims', $headers, json_encode($crmSim));
        $json = $this->getResponseJsonIfSuccess($response);

        return $json->cs_simid;
    }

    function getSimByNumber($simNumber) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'odata.include-annotations="*"' // to get formatted values for option set (pick list) fields
        );

        $sanitizedSimNumber = addslashes($simNumber);
        $query = array(
            '$filter' => "cs_number eq '$sanitizedSimNumber'",
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/cs_sims', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0];
    }

    function getSimBySatelliteNumber($satelliteNumber) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'odata.include-annotations="*"' // to get formatted values for option set (pick list) fields
        );

        $sanitizedSimNumber = addslashes($satelliteNumber);
        $query = array(
            '$filter' => "cs_satellitenumber eq '$sanitizedSimNumber'",
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/cs_sims', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response);

        if (empty($json->value))
            return false;

        return $json->value[0];
    }

    /**
     * @param int $magentoCustomerId
     * @return array|false
     */
    function getSimsByCustomerId($magentoCustomerId, $simField, $simSorting, $simFilter) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'odata.include-annotations="*"' // to get formatted values for option set (pick list) fields
        );

        $sanitizedMagentoCustomerId = intval($magentoCustomerId);
        $queryXml = <<<QUERYXML
<fetch mapping="logical">
   <entity name="cs_sim"> 
      <attribute name="cs_simstatus"/>
      <attribute name="cs_number"/> 
      <attribute name="new_nickname"/>
      <attribute name="cs_network"/> 
      <attribute name="cs_service"/>
      <attribute name="cs_plan"/>
      <attribute name="cs_currentminutes"/>
      <attribute name="cs_satellitenumber"/>
      <attribute name="cs_expirydate"/>
      <attribute name="new_substatus"/>
      <attribute name="new_order"/>
      <link-entity name="account" to="cs_accountid"> 
         <filter type="and"> 
            <condition attribute="accountnumber" operator="eq" value="$sanitizedMagentoCustomerId" /> 
          </filter> 
      </link-entity> 
QUERYXML;

        $dynamicsField = '';
        switch ($simField) {
            case SimTableField::NetworkStatus:
                $dynamicsField = 'cs_simstatus';
                break;
            case SimTableField::SimSharp:
                $dynamicsField = 'cs_number';
                break;
            case SimTableField::SatSharp:
                $dynamicsField = 'cs_satellitenumber';
                break;
            case SimTableField::Network:
                $dynamicsField = 'cs_network';
                break;
            case SimTableField::Plan:
                $dynamicsField = 'cs_plan';
                break;
            case SimTableField::CurrentMinutes:
                $dynamicsField = 'cs_currentminutes';
                break;
            case SimTableField::ExpiryDate:
                $dynamicsField = 'cs_expirydate';
                break;
            case SimTableField::Nickname:
                $dynamicsField = 'new_nickname';
                break;
            case SimTableField::Order:
                $dynamicsField = 'new_order';
                break;
        }
        if ($dynamicsField !== '') {
            if ($simSorting === SortingDirection::Descending)
                $queryXml .= "<order attribute='{$dynamicsField}' descending='true' />";
            else
                $queryXml .= "<order attribute='{$dynamicsField}' />";
        }

        $simstatusValue = '';
        switch ($simFilter) {
            case FilterNetworkStatus::Active:
                $simstatusValue = '100000001';
                break;
            case FilterNetworkStatus::Issued:
                $simstatusValue = '100000000';
                break;
            case FilterNetworkStatus::Expired:
                $simstatusValue = '100000002';
                break;
            case FilterNetworkStatus::Deactivated:
                $simstatusValue = '100000006';
                break;
        }
        if ($simstatusValue !== '') {
            $queryXml .=
                "<filter type='and'>
                    <condition attribute='cs_simstatus' operator='eq' value='{$simstatusValue}' />
                </filter>";
        }

        $queryXml .= '</entity></fetch>';

        $query = array(
            'fetchXml' => $queryXml
        );

        // to find out name for api url (cs_sims in this case), see EntitySetName for an entity in XrmToolBox
        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/cs_sims', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response, true);

        if (!isset($json['value']))
            return false;

        return $json['value'];
    }

    /**
     * @param int $magentoCustomerId
     * @return array|false
     */
    function getSimsActivationRequestsCountByCustomerId($magentoCustomerId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'odata.include-annotations="*"' // to get formatted values for option set (pick list) fields
        );

        $sanitizedMagentoCustomerId = intval($magentoCustomerId);
        $queryXml = <<<QUERYXML
<fetch mapping="logical" aggregate="true" distinct="true">
   <entity name="cs_sim"> 
      <attribute name="cs_simid" alias="cs_simid" groupby="true"/> 
      
      <link-entity name="account" to="cs_accountid"> 
         <filter type="and"> 
            <condition attribute="accountnumber" operator="eq" value="$sanitizedMagentoCustomerId" /> 
          </filter> 
      </link-entity> 
      <link-entity name="cs_activationrequest" from="cs_sim" to="cs_simid" >
		 <attribute name="cs_emailaddress" alias="activationrequests_count" aggregate="count" />
		 <filter type="and" >
			<condition attribute="createdon" operator="last-x-hours" value="72" />
		 </filter>
	  </link-entity>
   </entity> 
</fetch>
QUERYXML;
        $query = array(
            'fetchXml' => $queryXml
        );

        // to find out name for api url (cs_sims in this case), see EntitySetName for an entity in XrmToolBox
        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/cs_sims', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response, true);

        if (!isset($json['value']))
            return false;

        return $json['value'];
    }

    /**
     * don't forget to validate accountnumber (to current customer id in Magento) if you get $simId from untrusted browser
     * @param string $simId
     * @return array|false
     */
    function getSim($simId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'odata.include-annotations="*"' // to get formatted values for option set (pick list) fields
        );

        $sanitizedSimId = htmlspecialchars($simId);
        $queryXml = <<<QUERYXML
<fetch mapping="logical">
   <entity name="cs_sim"> 
      <attribute name="cs_simstatus"/>
      <attribute name="cs_number"/> 
      <attribute name="new_nickname"/>
      <attribute name="cs_network"/> 
      <attribute name="cs_type"/>
      <attribute name="cs_service"/>
      <attribute name="cs_plan"/>
      <attribute name="cs_currentminutes"/>
      <attribute name="cs_satellitenumber"/>
      <attribute name="cs_data"/>
      <attribute name="cs_activationdate"/>
      <attribute name="cs_expirydate"/>
      <attribute name="cs_imei"/>
      <attribute name="new_substatus"/>
      <attribute name="new_quicknote"/>
      <attribute name="new_order"/>
      
      <link-entity name="account" to="cs_accountid"> 
          <attribute name="accountnumber" alias="magento_customer_id"/>
      </link-entity> 
      <filter type="and"> 
        <condition attribute="cs_simid" operator="eq" value="$sanitizedSimId" /> 
      </filter> 
   </entity> 
</fetch>
QUERYXML;
        $query = array(
            'fetchXml' => $queryXml
        );

        // to find out name for api url (cs_sims in this case), see EntitySetName for an entity in XrmToolBox
        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/cs_sims', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response, true);

        if (empty($json['value']))
            return false;

        return $json['value'][0];
    }

    /**
     * don't forget to validate accountnumber (to current customer id in Magento) if you get $simId from untrusted browser
     * @param string $simId
     * @return array|false
     */
    function getSimActivationRequestsCount($simId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'odata.include-annotations="*"' // to get formatted values for option set (pick list) fields
        );

        $sanitizedSimId = htmlspecialchars($simId);
        $queryXml = <<<QUERYXML
<fetch mapping="logical" aggregate="true">
   <entity name="cs_sim"> 
      <attribute name="cs_simid" alias="cs_simid" groupby="true"/>
      
      <filter type="and"> 
        <condition attribute="cs_simid" operator="eq" value="$sanitizedSimId" /> 
      </filter> 

      <link-entity name="cs_activationrequest" from="cs_sim" to="cs_simid" >
      <attribute name="cs_emailaddress" alias="activationrequests_count" aggregate="count" />
      	<filter type="and" >
        	<condition attribute="createdon" operator="last-x-hours" value="72" />
      	</filter>
      </link-entity>
   </entity> 
</fetch>
QUERYXML;
        $query = array(
            'fetchXml' => $queryXml
        );

        // to find out name for api url (cs_sims in this case), see EntitySetName for an entity in XrmToolBox
        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/cs_sims', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response, true);

        if (empty($json['value']))
            return 0;

        return $json['value'][0]['activationrequests_count'];
    }

    function updateSim($simId, $crmSim) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/cs_sims($simId)", $headers, json_encode($crmSim));
        $json = $this->getResponseJsonIfSuccess($response);
    }

    /**
     * @param int $magentoCustomerId
     * @return array|false
     */
    function getDevicesByCustomerId($magentoCustomerId) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'odata.include-annotations="*"' // to get formatted values for option set (pick list) fields
        );

        $sanitizedMagentoCustomerId = intval($magentoCustomerId);
        $queryXml = <<<QUERYXML
<fetch mapping="logical">
   <entity name="new_device"> 
      <link-entity name="salesorder" to="new_order" link-type="outer">
          <attribute name="name" alias="ordernumber"/>
          <order attribute="ordernumber" descending="true"/>       
      </link-entity> 
      <attribute name="new_saledate"/>
      <attribute name="new_name"/>
      <link-entity name="product" to="new_product" link-type="outer">
          <attribute name="name" alias="productname"/>
      </link-entity>
      <!-- <attribute name="new_serialnumber"/> -->
      <link-entity name="account" to="new_soldto"> 
         <filter type="and"> 
            <condition attribute="accountnumber" operator="eq" value="$sanitizedMagentoCustomerId" /> 
          </filter> 
      </link-entity> 
      <order attribute="new_name"/>       
   </entity> 
</fetch>
QUERYXML;
        $query = array(
            'fetchXml' => $queryXml
        );

        $response = $this->sendGetRequest($this->getCrmUrl() . '/api/data/v8.1/new_devices', $headers, $query);
        $json = $this->getResponseJsonIfSuccess($response, true);

        if (!isset($json['value']))
            return false;

        return $json['value'];
    }

    function createOrUpdateActivationRequest($crmActivationRequest) {
        $this->login();

        $headers = array(
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'OData-Max-Version' => '4.0',
            'OData-Version' => '4.0',
            'Prefer' => 'return=representation'
        );

        $sanitizedRequestNumber = addslashes($crmActivationRequest['cs_requestnumber']);
        $response = $this->sendPatchRequestJson($this->getCrmUrl() . "/api/data/v8.1/cs_activationrequests(cs_requestnumber='$sanitizedRequestNumber')", $headers, json_encode($crmActivationRequest));
        $json = $this->getResponseJsonIfSuccess($response);

        return $json->cs_activationrequestid;
    }

    private function login() {
        if (isset($this->accessToken) && !$this->isTokenExpired()) {
            return;
        }

        $this->logger->info('Obtaining access token...');

        $now = microtime(true);

        $credentials = $this->credentialsProvider->getCredentials();

        $headers = array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        );
        $params = array(
            'grant_type' => 'client_credentials',
            'client_id' => $credentials->getClientId(),
            'client_secret' => $credentials->getClientSecret(),
            'resource' => $this->getCrmUrl(),
        );

        $response = $this->sendPostRequest(self::LOGIN_URL, $headers, $params);
        $json = $this->getResponseJsonIfSuccess($response);

        $this->accessToken = $json->access_token;
        $this->expiresAt = $json->expires_in + $now;
    }

    private function getCrmUrl() {
        return $this->credentialsProvider->getCredentials()->getResource();
    }

    private function isTokenExpired() {
        if (!isset($this->expiresAt)) {
            return false;
        }

        $now = microtime(true);
        return $this->expiresAt < $now;
    }

    private function createHttpClient() {
        $client = new \Zend\Http\Client();
        $options = array(
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
            'maxredirects' => 0,
            'timeout' => 30
        );
        $client->setOptions($options);
        return $client;
    }

    private function sendGetRequest($uri, $headers, $query) {
        $request = new \Zend\Http\Request();
        $request->setMethod(\Zend\Http\Request::METHOD_GET);
        $request->setUri($uri);

        $request->getHeaders()->addHeaders($headers);
        $request->getQuery()->fromArray($query);

        return $this->sendRequest($request);
    }

    private function sendPostRequest($uri, $headers, $params) {
        $request = new \Zend\Http\Request();
        $request->setMethod(\Zend\Http\Request::METHOD_POST);
        $request->setUri($uri);

        $request->getHeaders()->addHeaders($headers);
        $request->getPost()->fromArray($params);

        return $this->sendRequest($request);
    }

    private function sendPostRequestJson($uri, $headers, $json) {
        $request = new \Zend\Http\Request();
        $request->setMethod(\Zend\Http\Request::METHOD_POST);
        $request->setUri($uri);

        $request->getHeaders()->addHeaders($headers);
        $request->setContent($json);

        return $this->sendRequest($request);
    }

    private function sendPatchRequestJson($uri, $headers, $json) {
        $request = new \Zend\Http\Request();
        $request->setMethod(\Zend\Http\Request::METHOD_PATCH);
        $request->setUri($uri);

        $request->getHeaders()->addHeaders($headers);
        $request->setContent($json);

        return $this->sendRequest($request);
    }

    private function sendDeleteRequest($uri, $headers) {
        $request = new \Zend\Http\Request();
        $request->setMethod(\Zend\Http\Request::METHOD_DELETE);
        $request->setUri($uri);

        $request->getHeaders()->addHeaders($headers);

        return $this->sendRequest($request);
    }

    /**
     * @param \Zend\Http\Request $request
     * @return \Zend\Http\Response
     * @throws DynamicsException
     */
    private function sendRequest($request) {
        try {
            $queryLog = json_encode($request->getQuery()->toArray());
            $this->logger->info("Request | {$request->getMethod()} {$request->getUri()}\nQuery: $queryLog\nBody: {$request->getContent()}\n");

            return $this->httpClient->send($request);
        } catch (\Zend\Http\Client\Exception\RuntimeException $e) {
            $this->logger->error("Failed Zend request: " . $e->getMessage());
            throw new DynamicsException('Request to Dynamics API failed');
        } catch (\Zend\Http\Exception\RuntimeException $e) {
            $this->logger->error("Failed Zend request: " . $e->getMessage());
            throw new DynamicsException('Request to Dynamics API failed');
        }
    }

    private function getResponseJsonIfSuccess($response, $asArray = false) {
        $this->checkResponseIsSuccess($response);

        $body = $response->getBody();
        return json_decode($body, $asArray);
    }

    /**
     * @param \Zend\Http\Response $response
     * @throws DynamicsException
     */
    private function checkResponseIsSuccess($response) {
        $this->logger->info("Response | Code: {$response->getStatusCode()}\nBody: {$response->getBody()}\n");

        if (!$response->isSuccess())
            throw new DynamicsException('Dynamics API returned failed response');
    }
}