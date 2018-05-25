<?php
namespace app\api\controller\v2;
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;
use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\BannerMissException;
class Banner extends Controller {
	public function getBanner($id) {
		return 'This is v2 version';
	}
}
