<?php
namespace app\api\model;

class User extends BaseModel {
  //根据userid来查找address
  public function address() {
    return $this->hasOne('UserAddress','user_id','id');
  }

  public function getByOpenID($openid) {
    $user = self::where('openid','=',$openid)->find();
    return $user;
  }

}
