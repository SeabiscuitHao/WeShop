<?php
namespace app\api\model;
use think\Model;
use think\Db;
class Banner extends Model {

	public function items() {
		//关联模型BannerItem 使用hasMany方法一对多关系（关联模型的明星名称，外键，主键）
		return $this->hasMany('BannerItem','banner_id','id');
	}
	public static function getBannerById($id) {
		$banner = self::with(['items','items.img'])->find($id);
		// $result = Db::table('banner_item')->where('banner_id','=',$id)->select();
		return $banner;
	}
}
