<?php

namespace framework\util;

class RouteInfo {

   private $controller;
   private $view;
   private $method;
   private $args;

   public function __construct($controller, $view, $method, $args = array()) {
      $this->args = $args;
      $this->controller = $controller;
      $this->view = $view;
      $this->method = $method;
   }

   /**
    * @return array
    */
   public function getArgs() {
      return $this->args;
   }

   /**
    * @return mixed
    */
   public function getController() {
      return $this->controller;
   }

   /**
    * @return mixed
    */
   public function getView() {
      return $this->view;
   }

   /**
    * @return mixed
    */
   public function getMethod() {
      return $this->method;
   }

}
 