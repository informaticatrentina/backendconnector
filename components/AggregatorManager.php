<?php

/**
 * AggregatorManager
 * 
 * AggregatorManager class is used for interacting with AggregatorAPI class.
 * AggregatorManager class is used for manipulate(get, save, delete, update) entries. 
 * Copyright (c) 2014 <ahref Foundation -- All rights reserved.
 * Author: Pradeep Kumar <pradeep@incaendo.com>
 * This file is part of <Backendconnector>.
 * This file can not be copied and/or distributed without the express permission of
 *   <ahref Foundation.
 */
class AggregatorManager {

  public $source = SOURCE;

  /**
   * getEntry$search
   * 
   * This function is used for get entries from aggregator
   * @param (int) $limit (default 1),
   * @param (int) $offset (default 1),
   * @param (string) $id (id of entries) 
   * @param (string) $status 
   * @param $guid
   * @param (string) $tags
   * @param (string) $tagName
   * @param (int) $count (1 for all entry with count and 2 for only count) 
   * @param (date) $dateFrom
   * @param (date) $dateTo
   * @param (int) $enclosures (default 1), 
   * @param (int) $range, 
   * @param $sort (sorting parameter like date_changed, date_published   default ascending order)
   * @param (array) $cordinate 
   * @param (string) $returnField (return these field as output)
   * @param (string) $returnContent 
   * @param (string) $returnTag
   * @return (array) $entry
   */
  public function getEntry($limit = 1, $offset = 0, $id, $status = 'active', $tags = '', $tagsName = '', $guid = '', $count = 1, $dateFrom = '', $dateTo = '', $enclosures = 1, $range = '', $cordinate = array(), $sort, $returnField, $returnContent, $returnTag, $related = '', $source = SOURCE, $author = '') {
    $data = array();
    $entry = array();
    $inputData = array();
    $inputParam = '';
    $aggregatorAPI = new AggregatorAPI;
    if (!empty($limit) && is_numeric($limit)) {
      $inputData['limit'] = $limit;
    } else {
      $inputData['limit'] = DEFAULT_LIMIT;
    }

    if (!empty($offset) && is_numeric($offset)) {
      $inputData['offset'] = $offset;
    } else {
      $inputData['offset'] = DEFAULT_OFFSET;
    }

    if (!empty($id)) {
      $inputData['id'] = $id;
    }

    if (!empty($status) && is_string($status)) {
      $inputData['status'] = $status;
    }

    if (!empty($guid) && filter_var($guid, FILTER_VALIDATE_URL)) {
      $inputData['guid'] = $guid;
    }

    if (!empty($tags)) {
      $inputData['tags'] = $tags;
    }

    if (!empty($tagsName)) {
      $inputData['tagsname'] = $tagsName;
    }

    if (!empty($count) && ($count == 1 || $count == 2)) {
      $inputData['count'] = $count;
    }

    if (!empty($dateFrom) && !empty($dateTo) && $dateFrom < $dateTo && $dateTo < time()) {
      $inputData['interval'] = $dateFrom . ',' . $dateTo;
    }

    if (isset($enclosures) && is_numeric($enclosures)) {
      $inputData['enclosures'] = $enclosures;
    }

    if (!empty($range) && is_numeric($range)) {
      $inputData['range'] = $range;
    }

    if (!empty($sort)) {
      $inputData['sort'] = $sort;
    }

    if (array_key_exists('NE', $cordinate) && !empty($cordinate['NE']) && (array_key_exists('SW', $cordinate) && !empty($cordinate['SW']))) {
      $inputData['NE'] = $cordinate['NE'];
      $inputData['SW'] = $cordinate['SW'];
    } else if (array_key_exists('radius', $cordinate) && !empty($cordinate['radius']) && (array_key_exists('center', $cordinate) && !empty($cordinate['center']))) {
      $inputData['radius'] = $cordinate['radius'];
      $inputData['center'] = $cordinate['center'];
    }

    if (!empty($returnField)) {
      $inputData['return_fields'] = $returnField;
    }

    if (!empty($returnContent)) {
      $inputData['returnContent'] = $returnContent;
    }

    if (!empty($returnTag)) {
      $inputData['returnTag'] = $returnTag;
    }

    if (!empty($related)) {
      $inputData['related'] = $related;
    }

    if (!empty($source)) {
      $inputData['source'] = $source;
    }

    if (!empty($author)) {
      $inputData['author'] = $author;
    }
    // encode array into a query string
    $inputParam = http_build_query($inputData);

    try {
      if (empty($returnField) && empty($returnContent) && empty($returnTag)) {
        throw new Exception('Return fields should not be empty');
      }
      Yii::log('', 'info', 'Input data in getEntry : ' . $inputParam);
      $data = $aggregatorAPI->curlGet(ENTRY, $inputParam);
    } catch (Exception $e) {
      Yii::log('', 'error',  'Error in getEntry method :' . $e->getMessage());
    }
    if (array_key_exists('status', $data) && $data['status'] == 'true') {
      $entry = $data['data'];
    }
    return $entry;
  }
}