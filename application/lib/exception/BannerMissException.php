<?php
namespace app\lib\exception;
use app\lib\exception\BaseException;
class BannerMissException extends BaseException {
	public $code = 404;
	public $msg = '数据不存在';
	public $errorCode = 40000;
}
