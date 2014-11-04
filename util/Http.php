<?php

namespace framework\util;

class Http {

   public static function send404() {
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
      echo '<h1>404 Not Found</h1>';
   }

   public static function send403() {
      header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', true, 403);
      echo '<h1>403 Forbidden</h1>';
   }

   public static function sendRedirect($page) {
      if(preg_match('/^http\:\/\//', $page)) {
         header("Location: $page");
      }
      else {
         $context = RestRequest::get();
         $url = 'http://' . $_SERVER['SERVER_NAME'];

         if($_SERVER['SERVER_PORT']) {
            $url .= ':'.$_SERVER['SERVER_PORT'];
         }

         $url .= $context->getContextRoot() . $page;
         header("Location: $url");
      }
   }
}
