<?php

namespace framework\util;

use Symfony\Component\Yaml\Parser;

class Util {

   public static function parseYAML($yaml) {
      $parser = new Parser();

      return $parser->parse($yaml);
   }
}
 