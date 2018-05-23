<?php
namespace app\api\controller\v1;

use app\api\model\Product as ProductModel;

use app\api\validate\Count;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ProductException;
class Product{
  public function getRecent($count = 15) {
    (new Count())->goCheck();
    $products = ProductModel::getMostRecent($count);
    if (!$products) {
      throw new ProductException();
    }

    $products = $products->hidden(['summary']);
    return $products;
  }


  public function getAllInCategory($id) {
    (new IDMustBePostiveInt())->goCheck();
    $pro = new ProductModel();
    $products = $pro->getProductsByCategoryID($id);
    if (!$products) {
      throw new ProductException();
    }
    $products = $products->hidden(['summary']);
    return $products;
  }

  public function getOne($id) {
    (new IDMustBePostiveInt())->goCheck();
    $pr = new ProductModel();
    $product = $pr->getProductDetail($id);
    if (!$product) {
      throw new ProductException();
    }
    return $product;
  }
}
