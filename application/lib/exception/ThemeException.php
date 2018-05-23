<?php
namespace app\lib\exception;
use app\lib\exception\BaseException;
class ThemeException extends BaseException {
  	public $code = 404;
  	public $msg  = '参数错误';
  	public $errorCode = 30000;
}
