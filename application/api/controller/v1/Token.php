<?php
namespace app\api\cpntroller\v1;

class Token {
  public function getToken($code = '') {
    (new TokenGet())->goCheck();
    $ut = new UserToken($code);
    $token = $ut->get();
    //框架会自动将其转化为json
    return [
      'token' => $token,
    ];
  }
}
