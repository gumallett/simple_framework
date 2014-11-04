<?php

namespace framework\util;

class RouteMatcher {

   private $regex = '[a-zA-Z0-9]+';
   private $route_infos = array();

   public function __construct($routes) {
      foreach($routes as $route_name => $route) {
         if(isset($route['path'])) {
            $info = $this->buildRouteInfo($route);
            Logger::log($info['regex']);
            Logger::log($info['controller']);
            Logger::log($info['view']);

            $this->route_infos[] = $info;
         }
         else {
            Logger::log("path not set for route: " . $route_name);
         }
      }
   }

   /**
    * @param $requestUri
    * @return RouteInfo|null
    */
   public function match($requestUri) {
      Logger::log("In RouteMatcher, uri: " . $requestUri);
      foreach($this->route_infos as $route_info) {
         if(preg_match($route_info['regex'], $requestUri, $matches)) {
            $args = array();
            $controller = $route_info['controller'];
            $view = $route_info['view'];
            $method = null;

            if(isset($route_info['method'])) {
               $method = $route_info['method'];
            }

            foreach($route_info['part_names'] as $part_name) {
               $part_value = $matches[$part_name];
               if(is_array($part_value)) {
                  $part_value = $part_value[0];
               }

               $args[$part_name] = $part_value;
               Logger::log("$part_name: " . $part_value);
            }

            return new RouteInfo($controller, $view, $method, $args);
         }
      }

      return null;
   }

   private function buildRouteInfo($route) {
      $path = $route['path'];
      $arr = preg_split("/\//", $path);
      array_shift($arr);
      $route_regex = '/^';
      $part_names = array();

      foreach($arr as $part) {
         //Logger::log($part);
         //Logger::log($route_regex);
         if(preg_match('/^(\{[a-zA-Z]+\})/', $part, $matches)) {
            $part_name = ltrim($matches[1], '{');
            $part_name = rtrim($part_name, '}');
            $part_names[] = $part_name;
            $route_regex .= '\/' . "(?P<$part_name>" . $this->regex . ')';
         }
         else {
            $route_regex .= '\/' . $part;
         }
      }

      $route_regex .= '\/?/';
      return array(
         'regex' => $route_regex,
         'part_names' => $part_names,
         'controller' => $route['controller'],
         'view' => $route['view']
      );
   }
}
 