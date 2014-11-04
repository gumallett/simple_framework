<?php

namespace framework\impl;

use framework\ICrudHandler;
use framework\util\Logger;
use framework\util\RestRequest;
use framework\util\Http;

abstract class AbstractCrudHandler extends Handler implements ICrudHandler {

   public function process() {
      $request = $this->getRequest();

      switch(self::getMethod($request)) {
         case 'GET':
            Logger::log($this->getRouteArgs());
            if(count($this->getRouteArgs()) > 0) {
               call_user_func_array(array($this, 'index'), $this->getRouteArgs());
               break;
            }
            else if(count($request->getParts()) == 2) {
               $idx = $request->getPart(1);
               if(strlen(trim($idx)) > 0) {
                  $this->index($idx);
                  break;
               }
            }

            $this->index();
            break;

         case 'POST':

            $result = $this->create();

            if($result) {
               $this->onSuccess($result);
               return;
            }
            else {
               $this->onError();
            }

            break;

         case 'DELETE':

            if(count($request->getParts()) == 2) {
               $idx = $request->getPart(1);
               $result = $this->delete($idx);
            }
            else {
               $result = $this->delete(null);
            }
            Http::sendRedirect($result);

            break;

         case 'PUT':

            if(count($request->getParts()) == 2) {
               $idx = $request->getPart(1);
               $result = $this->update($idx);

            }
            else {
               Http::send404();
               return;
            }

            break;
      }

      parent::process();
   }

   protected function onSuccess($success) {
      Http::sendRedirect($success);
   }

   protected function onError($error = null) {
      //Http::send404();
   }

   protected static function resolveView(RestRequest $request) {
      $view = parent::resolveView($request);
      $part0 = $request->getPart(0);

      if(count($request->getParts()) == 2 && strlen(trim($request->getPart(1))) > 0) {
         $method = self::getMethod($request);

         if($method == 'GET' || $method == 'PUT') {
            $name = preg_replace('/s$/', '', $part0);
            $view = preg_replace("/$part0/", $name, $view);
         }
      }

      return $view;
   }

   private static function getMethod(RestRequest $request) {
      $method = $request->getMethod();
      $params = $request->getParameters();

      if(array_key_exists('op', $params)) {
         $op = strtolower($params['op']);

         if($op == 'delete') {
            $method = 'DELETE';
         }
         else if($op == 'put') {
            $method = 'PUT';
         }
      }

      return $method;
   }
}