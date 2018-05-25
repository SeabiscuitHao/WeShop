<?php
namespace app\api\validate;
use app\api\validate\BaseValidate;
class IDMustBePostiveInt extends BaseValidate {
	protected $rule = [
		'id'	=> 'require|isPostiveInteger',
	];

	/**
	*判断value是否为正整数
	*@value 为传过来的id
	*/
	protected function isPostiveInteger($value,$rule = '',$data = '',$field = '') {
		if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
			return true;
		} else {
			return $field . '必须是正整数';
		}
	}
}