<?php

namespace framework;

interface ICrudHandler extends IRequestHandler {

   /**
    * Mapped to a http GET /{handler}. E.g: GET /home will be mapped to HomeHandler.php::index()
    * @return mixed
    */
   public function index();

   /**
    * Mapped to a http POST /{handler}. E.g: POST /home will be mapped to HomeHandler.php::create()
    * @return mixed If a string, a redirect will be automatically issued to /{string}. Use this to redirect after a post. Otherwise return null.
    */
   public function create();

   public function update($id);

   public function delete($id);
}
