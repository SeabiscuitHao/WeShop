<?php
namespace app\api\controller\v1;
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;
use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\BannerMissException;
class Banner extends Controller {
	public function getBanner($id) {

		//拦截器 执行对id为正整数的验证
		(new IDMustBePostiveInt()) ->batch()->goCheck();
		// $banner = BannerModel::with(['items','items.img'])->find($id);
		$banner = BannerModel::getBannerById($id);
		if (!$banner) {
			throw new BannerMissException();
		}
		// return json($banner);
		config('settimg.img_prefix');
		return $banner;

	}
}
