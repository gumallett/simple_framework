<?php

namespace framework\util;

class RestRequest {

   private $request;
   private $method;
   private $requestParts;
   private $parameters;
   private $queryString;
   private $referrer;
   private $contextRoot;

   private static $INSTANCE;

   private function __construct($uri = null, $method = null) {
      if($uri == null) {
         $uri = $_SERVER['REQUEST_URI'];
      }

      if($method == null) {
         $method = $_SERVER['REQUEST_METHOD'];
      }

      Logger::log('Request URI: '.$uri);

      if(preg_match('/^\/~malletg/', $uri)) {
         $this->request = trim(str_replace('/~malletg', '', $uri));
         $this->contextRoot = '/~malletg/';
      }
      else {
         $this->request = trim($uri);
         $this->contextRoot = '/';
      }

      $this->method = $method;
      $this->referrer = array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : null;

      $this->parseRequest();
      $this->parseQueryString();
      $this->parseParameters();

      Logger::log('Request Parts:');
      Logger::log($this->requestParts);
   }

   public static function get($uri = null, $method = null) {
      if(self::$INSTANCE == null) {
         self::$INSTANCE = new RestRequest($uri, $method);
      }

      return self::$INSTANCE;
   }

   public function getRequest() {
      return $this->request;
   }

   public function getMethod() {
      return $this->method;
   }

   public function getParts() {
      return $this->requestParts;
   }

   public function getPart($part) {
      return $this->requestParts[$part];
   }

   public function getParameters() {
      return $this->parameters;
   }

   public function getQueryString() {
      return $this->queryString;
   }

   public function setReferrer($referrer) {
      $this->referrer = $referrer;
   }

   public function getReferrer() {
      return $this->referrer;
   }

   public function setContextRoot($contextRoot) {
      $this->contextRoot = $contextRoot;
   }

   public function getContextRoot() {
      return $this->contextRoot;
   }

   private function parseRequest() {
      $this->requestParts = preg_split("/\//", $this->request);
      array_shift($this->requestParts);
   }

   private function parseQueryString() {
      $lastIndex = count($this->requestParts) - 1;
      $part = $this->getPart($lastIndex);
      $queryStart = stripos($part, '?');

      if($queryStart > 0) {
         $this->queryString = substr($part, $queryStart + 1);
         $this->requestParts[$lastIndex] = substr($part, 0, $queryStart);
      }
   }

   private function parseParameters() {
      if($this->method == 'POST') {
         $this->parameters = $_POST;
      }
      else if($this->method == 'GET') {
         $this->parameters = $_GET;
      }
   }
}
