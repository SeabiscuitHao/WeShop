<?php
namespace app\api\validate;
use app\api\validate\BaseValidate;
class Count extends BaseValidate {
  protected $rule = [
    'count' => 'isPostiveInteger|between:1,15'
  ];
}
