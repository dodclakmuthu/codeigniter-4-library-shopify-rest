<?php

namespace App\Libraries;

class Shopify {

  private $config;
  
  /*
  =========================================================
  ===following fields should contain in the config file====
  =========================================================
  $admin_secrect_api_key  = 'admin_secrect_api_key';
  $api_key                = 'api_key';
  $secret_key             = 'secret_key';
  $version                = '2022-04';
  $domain                 = 'https://domain.myshopify.com'
  ========================================================
  */

  public function __construct() {
    $this->config = config('Shopify');
  }

  public function getProductsCount() {
    $url = $this->getBaseUrl() . 'products/count.json';
    return $this->doGet($url);
  }

  public function createProduct($data) {
    $url = $this->getBaseUrl() . 'products.json';
    return $this->doPost($url, $data);
  }

  public function updateVarientPrice($varientId, $price)
  {
    $data = [
      "variant" => [
        'id' => $varientId,
        'price' => $price,
        'inventory_policy' => 'continue',
        'inventory_management' => 'shopify'
      ]
    ];
    $url = $this->getBaseUrl() . "variants/{$varientId}.json";
    return $this->doPut($url, json_encode($data));
  }

  private function getBaseUrl() {
    //shopify store url
    // ex : https://northeastsportsandcollectables.myshopify.com
    return "{$this->config->store_url}/admin/api/{$this->config->version}/";
  }

  private function getHeaders() {
    $headers = [
      "X-Shopify-Access-Token: " . $this->config->admin_secrect_api_key,
      "Content-Type: application/json",
    ];
    return $headers;
  }

  private function doGet($url) {
    $headers = $this->getHeaders();
    $connection = curl_init();
    curl_setopt($connection, CURLOPT_URL, $url);
    curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($connection, CURLOPT_HEADER, 0);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($connection);
    $httpCode = curl_getinfo($connection, CURLINFO_HTTP_CODE);
    $header_size = curl_getinfo($connection, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    curl_close($connection);
    $resp = ['httpCode' => $httpCode, 'response' => $response];
    return $resp;
  }

  private function doPost($url, $data) {
    $connection = curl_init();
    $headers = $this->getHeaders();
    curl_setopt($connection, CURLOPT_URL, $url);
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
    if (!empty($headers))
      curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($connection, CURLOPT_POST, 1);
    curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
    curl_setopt($connection, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($connection);
    $httpCode = curl_getinfo($connection, CURLINFO_HTTP_CODE);
    curl_close($connection);
    $resp = ['httpCode' => $httpCode, 'response' => $response];
    return $resp;
  }

  private function doPut($url, $data) {
    $connection = curl_init();
    $headers = $this->getHeaders();
    curl_setopt($connection, CURLOPT_URL, $url);
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
    if (!empty($headers))
      curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($connection, CURLOPT_POST, 1);
    curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($connection);
    $httpCode = curl_getinfo($connection, CURLINFO_HTTP_CODE);
    curl_close($connection);
    $resp = ['httpCode' => $httpCode, 'response' => $response];
    return $resp;
  }
}
