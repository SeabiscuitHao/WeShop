<?php
namespace app\api\model;

class ProductImage extends BaseModel {
  protected $hidden = ['img_id','img_url.id','delete_time','product_id','img_url.update_time','img_url.from'];

  public function imgUrl() {
    return $this->belongsTo('Image','img_id','id');
  }
}
