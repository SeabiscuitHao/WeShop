<?php
namespace app\api\validate;
use app\api\validate\BaseValidate;
class OrderPlace extends BaseValidate {

  protected $rule = [
    'products'  => 'checkProducts',
  ];
  //products 中的参数：
  protected $singlerule = [
    'product_id'  => 'require|isPostiveInteger',
    'count'       => 'require|isPostiveInteger'
  ];

  protected function checkProducts($values) {
    if (empty($values)) {
      throw new ParameterException([
        'msg' => '商品列表不能为空'，
      ]);
    }

    if (!is_array($values)) {
      throw new ParameterException([
        'msg' => '商品参数不正确'，
      ]);
    }

    foreach ($values as $value) {
      $this->checkProducts($value);
    }
    return true;
  }

  protected function checkProducts($value) {
    $validate = new BaseValidate($this->$singlerule);
    $result = $validate->check($value);
    if (!$result) {
      throw new ParameterException([
        'msg' => '商品列表参数错误',
      ]);
    }
  }
}
