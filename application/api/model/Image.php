<?php
namespace app\api\model;
use app\api\model\BaseModel;
class Image extends BaseModel {
  public function getUrlAttr($value,$data) {
    return $this->prefixImgUrl($value,$data);
  }
}
