<?php
namespace app\api\service;
use think\Cache;
use think\Request;
use think\Exception;
use app\lib\exception\TokenException;
use app\lib\exception\ForbiddenException;
use app\lib\enum\ScopeEnum;
class Token {
  public static function generateToken() {
    //32个字符组成一组随机字符串
    $randChars = getRandChar(32);
    //用三组字符串，进行md5加密
    $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
    //salt 盐
    $salt = config('secure.token_salt');

    return md5($randChars.$timestamp.$salt);
  }

  //所有用户请求的令牌都要放在http请求的header里面，不能放在body里面
  public static function  getCurrentTokenVar($key) {
    //Request 静态类的全局方法
    $token = Request::instance()->header('token');

    $vars = Cache::get($token);
    if (!$vars) {
      throw new TokenException();
    } else {
      if (!is_array($vars)) {
        $vars = json_decode($vars,true);
      }
      if (array_key_exists($key,$vars)) {
        return $vars[$key];
      } else {
        throw new Exception('尝试获取的Token变量并不存在 ');
      }
    }
  }

  public static function getCurrentUid() {
    $uid = self::getCurrentTokenVar('uid');
    return $uid;
  }
  // 用户和CMS管理员都可以访问的权限
  public function needPrimaryScope() {
    $scope = self::getCurrentTokenVar('scope');
    if ($scope) {
      if ($scope >= ScopeEnum::User) {
        return true;
      } else {
        throw new ForbiddenException();
      }
    } else {
      throw new TokenException();
    }
  }

  // 只有用户才能访问的接口权限

  protected function needExclusiveScope() {
    $scope = self::getCurrentTokenVar('scope');
    if ($scope) {
      if ($scope >= ScopeEnum::User) {
        return true;
      } else {
        throw new ForbiddenException();
      }
    } else {
      throw new TokenException();
    }
  }

}
