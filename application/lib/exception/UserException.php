<?php
namespace app\lib\exception;
use app\lib\exception\BaseException;
class UserException extends BaseException {
  public $code = 404;
  public $msg = '用户不存在';
  public $errorCode = 600000;

}
