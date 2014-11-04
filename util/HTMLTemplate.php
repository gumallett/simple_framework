<?php

namespace framework\util;

class HTMLTemplate {

    private $file;
    private $title;
    private $css;

    private $data;

    public function __construct($title, $file, $data = array()) {
        $this->file = $file;
        $this->title = $title;
        $this->data = $data;
        $this->css = "style.css";
        $this->contextRoot = RestRequest::get()->getContextRoot();
    }

    public function render() {
        include $this->file;
    }

    public function __get($key) {
       if(array_key_exists($key, $this->data)) {
         return $this->data[$key];
       }

       return null;
    }

   public function __set($key, $value) {
      $this->data[$key] = $value;
   }

   public function setTitle($title) {
      $this->title = $title;
   }

   public function getTitle() {
      return $this->title;
   }

   public function setCss($css) {
      $this->css = $css;
   }

   public function getCss() {
      return $this->css;
   }
}