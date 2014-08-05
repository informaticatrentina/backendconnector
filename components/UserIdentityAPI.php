<?php

/**
 * UserIdentityAPI
 * 
 * UserIdentityAPI class is called for create, update, search userIdentityManager class. 
 * Copyright (c) 2014 <ahref Foundation -- All rights reserved.
 * Author: Pradeep Kumar <pradeep@incaendo.com>
 * This file is part of <Backendconnector>.
 * This file can not be copied and/or distributed without the express permission of
 *  <ahref Foundation.
 */
         
class UserIdentityAPI {

  private $baseUrl;
  private $response;
  private $url;
  
  function __construct() {
    $this->baseUrl = IDENTITY_MANAGER_API_URL;
  }
    
  /**
   * getUserDetail
   * 
   * This function is used for curl request on server using Get method
   * @param (array) $params
   * @param (string) $function
   * @return (array) $userDetail
   */
  function getUserDetail($function, $params = array()) {
    $userDetail = array();
    try {
      $userParam = array();
      if (array_key_exists('email', $params) && !empty($params['email'])) {
        $userParam['email'] = $params['email'];
      }
      if (array_key_exists('password', $params) && !empty($params['password'])) {
        $userParam['password'] = $params['password'];
      }
      if (array_key_exists('id', $params) && !empty($params['id'])) {
        $userParam['_id'] = $params['id'];
      }
      if (!empty($userParam)) {
        $userParam = 'where=' . json_encode($userParam);
        $this->url = $this->baseUrl . $function .'/?'. $userParam;
      } 
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->url);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
            "Authorization: Basic " . base64_encode(IDM_API_KEY . ':')
      ));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);
      
      $this->response = curl_exec($ch);
      $headers = curl_getinfo($ch);
      curl_close($ch);

      //Manage uncorrect response 
      if ($headers['http_code'] != 200) {
        throw new Exception('Identitity Manager API returning httpcode: ' . $headers['http_code']);
      } elseif (!$this->response) {
        throw new Exception('Identitity Manager API is not responding or Curl failed');
      } elseif (strlen($this->response) == 0) {
        throw new Exception('Zero length response not permitted');
      }
      $userDetail = json_decode(strstr($this->response, "{"), true);
    } catch (Exception $e) {
      Yii::log('', 'error', 'Error in curlGet :' . $e->getMessage());
      $userDetail['success'] = false;
      $userDetail['msg'] = $e->getMessage();
      $userDetail['data'] = '';
    }
    return $userDetail;
  }
  
  /**
   * createUser
   * 
   * @param (array) $params
   * @param (string) $function
   * @return (array) $return
   */
  function createUser($function, $params = array()) {
    $return = array();
    try {
      if (!empty($params)) {
        $data = http_build_query($params);
        $this->url = $this->baseUrl . $function .'/';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
              "Authorization: Basic " . base64_encode(IDM_API_KEY . ':')
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);
        
        $this->response = curl_exec($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        //Manage uncorrect response 
        if ($headers['http_code'] != 200) {
          throw new Exception('Identitity Manager returning httpcode: ' . $headers['http_code']);
        } elseif (!$this->response) {
          throw new Exception('Identitity Manager  is not responding or Curl failed');
        } elseif (strlen($this->response) == 0) {
          throw new Exception('Zero length response not permitted');
        }
        $return = json_decode(strstr($this->response, "{"), true);
      }
    } catch (Exception $e) {
      Yii::log('', 'error', 'Error in createUser :' . $e->getMessage());
      $return['success'] = false;
      $return['msg'] = $e->getMessage();
      $return['data'] = '';
    }
    return $return;
  }

  /**
   * getUserInfo
   * 
   * This function is used for curl request on server using Get method
   * @param (string) $userId
   * @param (string) $function
   * @return (array) $userDetail
   */
  function getUserInfo($function, $userId) {
    $userDetail = array();
    try {
      if (empty($userId)) {
        return $userDetail;  
      }
      $this->url = $this->baseUrl . $function .'/'. $userId;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->url);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
            "Authorization: Basic " . base64_encode(IDM_API_KEY . ':')
      ));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);
      
      $this->response = curl_exec($ch);
      $headers = curl_getinfo($ch);
      curl_close($ch);

      //Manage uncorrect response 
      if ($headers['http_code'] != 200) {
        throw new Exception('Identitity Manager API returning httpcode: ' . $headers['http_code']);
      } elseif (!$this->response) {
        throw new Exception('Identitity Manager API is not responding or Curl failed');
      } elseif (strlen($this->response) == 0) {
        throw new Exception('Zero length response not permitted');
      }
      $userDetail = json_decode(strstr($this->response, "{"), true);
    } catch (Exception $e) {
      Yii::log($e->getMessage(), 'error', 'Error in curlGet :');
      $userDetail['success'] = false;
      $userDetail['msg'] = $e->getMessage();
      $userDetail['data'] = '';
    }
    return $userDetail;
  }
}