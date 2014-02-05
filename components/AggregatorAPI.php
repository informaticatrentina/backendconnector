<?php

/**
 * AggregatorAPI
 * 
 * AggregatorAPI class is called for get, save, delete, update entries by AggregatorManager class. 
 * Copyright (c) 2014 <ahref Foundation -- All rights reserved.
 * Author: Pradeep Kumar <pradeep@incaendo.com>
 * This file is part of <Backendconnector>.
 * This file can not be copied and/or distributed without the express permission of
 *  <ahref Foundation.
 */
class AggregatorAPI {

  private $baseUrl;
  private $response;
  private $url;

  function __construct() {
    $this->baseUrl = AGGREGATOR_API_URL;
  }

  /**
   * curlGet
   * 
   * This function is used for curl request on server using Get method
   * @param (string) $params
   * @param (string) $function
   * @return (array) $out
   */
  function curlGet($function, $params = '') {
    $out = array();
    try {
      if (!empty($params)) {
        $this->url = $this->baseUrl . RESPONSE_FORMAT . '/' . $function . '?' . $params;
      }
      $defaultParams = array(
        CURLOPT_URL => $this->url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HEADER => 0, 
        CURLOPT_TIMEOUT => CURL_TIMEOUT
      );
      $curlHandle = curl_init();
      curl_setopt_array($curlHandle, $defaultParams);
      $this->response = curl_exec($curlHandle);
      $headers = curl_getinfo($curlHandle);
      curl_close($curlHandle);

      //Manage uncorrect response 
      if ($headers['http_code'] != 200) {
        throw new Exception('Aggregator returning httpcode: ' . $headers['http_code']);
      } elseif (!$this->response) {
        throw new Exception('Aggregator is not responding or Curl failed');
      } elseif (strlen($this->response) == 0) {
        throw new Exception('Zero length response not permitted');
      }

      $this->response = json_decode(str_replace("\n", '', $this->response), true);
      $out = $this->response;
    } catch (Exception $e) {
      Yii::log('', 'error', 'Error in curlGet :' . $e->getMessage());
      $out['success'] = false;
      $out['msg'] = $e->getMessage();
      $out['data'] = '';
    }
    return $out;
  }

  /**
   * curlPost
   * 
   * @param (array) $params
   * @param (string) $function
   * @return (array) $out
   */
  function curlPost($function, $params = array()) {
    $out = array();
    try {
      if (!empty($params)) {
        $data = array('entry' => json_encode($params));
        $this->url = $this->baseUrl . RESPONSE_FORMAT . '/' . $function;
        $defaultParams = array(
          CURLOPT_URL => $this->url,
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_HEADER => 0,
          CURLOPT_TIMEOUT => CURL_TIMEOUT,
          CURLOPT_POST => 1,
          CURLOPT_POSTFIELDS => $data
        );
        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, $defaultParams);
        $this->response = curl_exec($curlHandle);
        $headers = curl_getinfo($curlHandle);
        curl_close($curlHandle);

        //Manage uncorrect response 
        if ($headers['http_code'] != 200) {
          throw new Exception('Aggregator returning httpcode: ' . $headers['http_code']);
        } elseif (!$this->response) {
          throw new Exception('Aggregator is not responding or Curl failed');
        } elseif (strlen($this->response) == 0) {
          throw new Exception('Zero length response not permitted');
        }
        $this->response = json_decode(str_replace("\n", '', $this->response), true);
        $out['success'] = $this->response['status'];
        $id = array();
        $id = json_decode($this->response['data']);
        $out['id'] = $id->entryID;
      }
    } catch (Exception $e) {
      Yii::log('', 'error', 'Error in curlGet :' . $e->getMessage());
      $out['success'] = false;
      $out['msg'] = $e->getMessage();
      $out['data'] = '';
    }
    return $out;
  }

  /**
   * curlPut
   * 
   * @param (array) $params
   * @param (string) $function
   * @return (array) $out
   */
  function curlPut($function, $params = array()) {
    $out = array();
    try {
      if (!empty($params)) {
        $inputParam = array('entry' => json_encode($params));
        $data = http_build_query($inputParam);
        $this->url = $this->baseUrl . RESPONSE_FORMAT . '/' . $function;
        $defaultParams = array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_TIMEOUT => CURL_TIMEOUT,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        );
        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, $defaultParams);
        $this->response = curl_exec($curlHandle);
        $headers = curl_getinfo($curlHandle);
        curl_close($curlHandle);

        //Manage uncorrect response 
        if ($headers['http_code'] != 200) {
          throw new Exception('Aggregator returning httpcode: ' . $headers['http_code']);
        } elseif (!$this->response) {
          throw new Exception('Aggregator is not responding or Curl failed');
        } elseif (strlen($this->response) == 0) {
          throw new Exception('Zero length response not permitted');
        }

        $this->response = json_decode(str_replace("\n", '', $this->response), true);
        $out['success'] = $this->response['status'];
      }
    } catch (Exception $e) {
      Yii::log('', 'error', 'Error in curlPut :'. $e->getMessage());
      $out['success'] = false;
      $out['msg'] = $e->getMessage();
      $out['data'] = '';
    }
    return $out;
  }

}