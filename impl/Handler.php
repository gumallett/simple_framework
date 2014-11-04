<?php

namespace framework\impl;

use framework\util\Logger;
use framework\IRequestHandler;
use framework\util\RestRequest;
use framework\util\HTMLTemplate;
use framework\util\Util;
use framework\util\RouteMatcher;

abstract class Handler implements IRequestHandler {

   private $request;
   private $view;
   private $routeArgs;

   private static $routeMatcher = null;

   private function __construct(RestRequest $request, HTMLTemplate $view, $routeArgs = array()) {
      $this->request = $request;
      $this->view = $view;
      $this->routeArgs = $routeArgs;
   }

   /**
    * Static factory method for retrieving a new handler.
    * Gets a handler from the http request. E.g: /home will be processed by 'HomeHandler' if it exists. Otherwise, null
    * is returned.
    * @param RestRequest $request
    * @return null|IRequestHandler
    */
   public static function getHandler(RestRequest $request) {
      if(is_null(self::$routeMatcher)) {
         $routes = Util::parseYAML(file_get_contents(__DIR__.'/../../routes/routes.yaml'));
         self::$routeMatcher = new RouteMatcher($routes);
      }

      $route_info = self::$routeMatcher->match($request->getRequest());
      $route_args = null;
      $handlerName = null;
      $template = null;

      if(is_null($route_info)) {
         $part0 = $request->getPart(0);
         Logger::log('Request: '.$part0);

         $handlerName = self::resolveHandler($part0);
         $template = static::resolveView($request);
      }
      else {
         $handlerName = $route_info->getController();
         $template = self::getViewFile($route_info->getView());
         $route_args = $route_info->getArgs();
      }

      Logger::log('Handler: '.$handlerName);

      if(is_readable(__DIR__ . '/../../handlers/' . $handlerName . '.php')) {
         $handler = 'handlers\\' . $handlerName;
         static::loadHandler($handler);

         $view = new HTMLTemplate($handlerName, "template.php", array('content' => $template));
         $handler = new $handler($request, $view, $route_args);

         return $handler;
      }

      return null;
   }

   /**
    * @return RestRequest
    */
   public function getRequest() {
      return $this->request;
   }

   /**
    * Gets the view (HTMLTemplate) used by this handler. Defaults to views/{handler}.php where {handler} is the part
    * before Handler in the php class name. E.g: home.php for 'HomeHandler'.
    * @return HTMLTemplate
    */
   public function getView() {
      return $this->view;
   }

   /**
    * Sets the view (HTMLTemplate) used by this handler. Defaults to views/{handler}.php where {handler} is the part
    * before Handler in the php class name. E.g: home.php for 'HomeHandler'.
    */
   public function setView($view) {
      $this->view = $view;
   }

   /**
    * @return mixed
    */
   public function getRouteArgs() {
      return $this->routeArgs;
   }

   /**
    * Render the view.
    */
   public function process() {
      $this->getView()->render();
   }

   private static function resolveHandler($request) {
      $request = ucfirst(strtolower($request));
      $request = preg_replace("/s$/", '', $request);

      return $request . 'Handler';
   }

   private static function loadHandler($handlerName) {
      require_once $handlerName . '.php';
   }

   private static function getViewFile($fileName) {
      $result = __DIR__ . '/../../views/' . $fileName;
      Logger::log("View file: " . $result);

      if(!preg_match("/\.php$/", $result)) {
         $result .= '.php';
      }

      return $result;
   }

   /**
    * Attempt to automatically determine the view file to render based on the request. E.g. /home will be mapped to
    * /views/home.php. /users will be mapped to /views/users.php.
    * @param RestRequest $request
    * @return string
    */
   protected static function resolveView(RestRequest $request) {
      $request = $request->getPart(0);
      $request = strtolower($request);
      return self::getViewFile($request);
   }
}