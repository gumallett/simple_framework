<?php

namespace framework;

interface IRequestHandler {

   public function process();

   public function getRequest();

   public function getView();

   public function setView($view);
}
