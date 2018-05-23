<?php
namespace app\api\controller;

use think\Controller;

class BaseController extends Controller {
  protected function checkPrimaryScope() {
    TokenService::needPrimaryScope();
  }

  protected function checkExclusiveScope() {
    TokenService::needExclusiveScope(); 
  }
}
