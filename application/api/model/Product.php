<?php
namespace app\api\model;
use app\api\model\BaseModel;
class Product extends BaseModel {

  protected $hidden = ['delete_time','main_img_id','pivot','from','category_id','create_time','update_time'];

  public function getMainImgUrlAttr($value,$data) {
    return $this->prefixImgUrl($value,$data);
  }
  //商品图片
  public function imgs() {
    return $this->hasMany('ProductImage','product_id','id');
  }
  //商品属性
  public function properties() {
    return $this->hasMany('ProductProperty','product_id','id');
  }

  public static function getMostRecent($count) {
    $products = self::limit($count)->order('create_time desc')->select();
    return $products;
  }
  public function getProductsByCategoryID($categoryID) {
    $products = self::where('category_id','=',$categoryID)->select();
    return $products;
  }

  public function getProductDetail($id) {
    // $product = self::with('imgs.imgUrl,properties')->find($id);

    //闭包方式进行查询构造器
    $product = self::with([
      'imgs'  => function($query) {
        $query->with(['imgUrl'])->order('order','asc');
      }
    ])
    ->with(['properties'])
    ->find($id);
    return $product;
  }
}
