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
  function getUserDetail($function, $params = array(), $email = false, $id = false, $nickname = false) {
    $userDetail = array();
    try {
      $projection = '';
      if ($id) {
        $projection = '&projection={"_id":1}';
      } else if ($email) {
        $projection = '&projection={"email":1}';
      } else if ($nickname) {
        $projection = '&projection={"nickname":1}';
      }
      $userParam = array();
      if (array_key_exists('email', $params) && !empty($params['email'])) {
        if (is_array($params['email'])) {
          $userDetail = array('_items' => array());
          $emailIds = array_chunk($params['email'], 20);
          foreach ($emailIds as $emailId) {
            $userParam['$or'] = array();
            foreach ($emailId as $userId) {
              $userParam['$or'][] = array('email' => $userId);
            }
            $user = $this->get($function, $userParam, $projection);
            if (array_key_exists('_items', $user) && !empty($user['_items'])) {
              $userDetail['_items'] = array_merge($userDetail['_items'], $user['_items']);
            }
          }
          goto LAST;
        } else {
          $userParam['email'] = $params['email'];
        }
      }
      if (array_key_exists('password', $params) && !empty($params['password'])) {
        $userParam['password'] = $params['password'];
      }
      if (array_key_exists('id', $params) && !empty($params['id'])) {
        if (is_array($params['id'])) {
          $userDetail = array('_items' => array());
          $userIds = array_chunk($params['id'], 20);
          foreach ($userIds as $ids) {
            $userParam['$or'] = array();
            foreach ($ids as $userId) {
              $userParam['$or'][] = array('_id' => $userId);
            }
            $user = $this->get($function, $userParam, $projection);
            if (array_key_exists('_items', $user) && !empty($user['_items'])) {
              $userDetail['_items'] = array_merge($userDetail['_items'], $user['_items']);
            }
          }
          goto LAST;
        } else {
          $userParam['_id'] = $params['id'];
        }
      }
      if (array_key_exists('nickname', $params) && !empty($params['nickname'])) {
        if (is_array($params['nickname'])) {
          $userDetail = array('_items' => array());
          $userNicknames = array_chunk($params['nickname'], 20);
          foreach ($userNicknames as $nicknames) {
            $userParam['$or'] = array();
            foreach ($nicknames as $userNickname) {
              $userParam['$or'][] = array('nickname' => $userNickname);
            }
            $user = $this->get($function, $userParam, $projection);
            if (array_key_exists('_items', $user) && !empty($user['_items'])) {
              $userDetail['_items'] = array_merge($userDetail['_items'], $user['_items']);
            }
          }
          goto LAST;
        } else {
          $userParam['nickname'] = $params['nickname'];
        }
      }
      $userDetail = $this->get($function, $userParam, $projection);
    } catch (Exception $e) {
      Yii::log('', 'error', 'Error in getUserDetail :' . $e->getMessage());
      $userDetail['success'] = false;
      $userDetail['msg'] = $e->getMessage();
      $userDetail['data'] = '';
    }
    LAST:
    return $userDetail;
  }
  
  /**
   * get
   * function is used for send curl get request for getting user data
   * @param string $function - collection (entry)
   * @param array $userParam - parameter for getting user detail
   * @param string $projection - id or email (that you want to get)
   * @return array $userDetail
   */
  public function get($function, $userParam, $projection = '') {
    try {
      if (!empty($userParam)) {
        $userParam = 'where=' . json_encode($userParam) . $projection;
        $this->url = $this->baseUrl . $function . '/?' . $userParam;
      }
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->url);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
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
      Yii::log('get function', 'error', $e->getMessage());
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
       if ($headers['http_code'] != 200 && $headers['http_code'] != 201 && $headers['http_code'] != 400) {
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

  public function curlPut ($function, $params = array()) {
    $out = array();
    try {
      if (!empty($params)) {
        $this->url = $this->baseUrl . $function .'/' . $params['id'] . '/' ;
        unset( $params['id'] );
        $data = json_encode($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
          "Authorization: Basic " . base64_encode(IDM_API_KEY . ':'),
          'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);

        $this->response = curl_exec($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);
        if ($headers['http_code'] != 200) {
          throw new Exception('Identitity Manager returning httpcode: ' . $headers['http_code']);
        } elseif (!$this->response) {
          throw new Exception('Identitity Manager  is not responding or Curl failed');
        } elseif (strlen($this->response) == 0) {
          throw new Exception('Zero length response not permitted');
        }
        $out = json_decode(strstr($this->response, "{"), true);
      }
    } catch (Exception $e) {
      Yii::log('', ERROR, Yii::t('contest','Error in curlPut :') . $e->getMessage());
      $out['success'] = false;
      $out['msg'] = $e->getMessage();
      $out['data'] = '';
    }
    return $out;
  }
}