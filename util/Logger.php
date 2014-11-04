<?php

namespace framework\util;
/**
 * Simple logging
 */
final class Logger {

   public static function log($message) {
      if(is_array($message)) {
         $msg = "Keys: " . implode(', ', array_keys($message));
         $msg .= " Values: " . implode(', ', array_values($message));
         $message = $msg;
      }

      file_put_contents("php://stderr", $message."\n");
   }

   public static function logRequest() {
      $req = $_SERVER['REQUEST_METHOD']."\r\n".$_SERVER['REQUEST_URI']."\r\n";

      self::log($req);
   }
}
