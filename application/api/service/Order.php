<?php
namespace app\api\service;
use app\lib\exception\OrderException;
use app\api\model\OrderProduct;
class Order {
  // 订单的商品列表，也就是客户端传递过来的products参数
  protected $oProducts;

  // 真实的商品信息（包括库存量）
  protected $products;

  protected $uid;

  public function place($uid,$oProducts) {
    // $oProducts 和 product 做对比
    // products 是从数据库中查询出来的
    $this->oProducts = $oProducts;
    $this->product = $this->getoProductByOrder($oProducts);
    $this->uid = $uid;
    $status = $this->getOrderStatus();
    if (!$status['pass']) {
      $status['order_id'] = -1;
      return $status;
    }
    // 开始创建订单
    $orderSnap = $this->snapOrder($status);
  }

  // 生成订单
  private function createOrder($snap) {
    try {
      $orderNo = $this->makeOrderNo();
      $order = new \app\api\model\Order();
      $order->user_id      = $this->uid;
      $order->order_no     = $orderNo;
      $order->total_price  = $snap['orderPrice'];
      $order->total_count  = $snap['totalCount'];
      $order->snap_img     = $snap['snapImg'];
      $order->snap_name    = $snap['snapImg'];
      $order->snap_address = $snap['snapAddress'];
      $order->snap_item    = json_encode($snap['pStatus']);

      $order->save();

      $orderID = $order->id;
      $create_time = $order->create_time;
      foreach ($this->oProducts as &$p) {
        $p['order_id'] = $orderID;
      }
      $orderProtect = new OrderProduct();
      $orderProtect->saveAll($this->oProduct);

      return [
        'order_no'    => $orderNo,
        'order_id'    => $orderID,
        'create_time' => $create_time
      ];
    } catch(Exception $ex) {
      throw $ex;
    }
  }
  // 生成随机订单号
  public static function makeOrderNo() {
    $yCode = array('A','B','C','D','E','F','G','H','I','J');
    $orderSn =
      $yCode[intval($date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date('d') . substn(microtime(), 2, 5) . sprintf('%02d',rand(0, 99));
      return $orderSn;
  }


  // 生成订单快照
  private function snapOrder($status) {
    // 订单快照信息
    $snap = [
      'orderPrice'  => 0,
      'totalCount'  => 0,
      'pStatus'     => [],
      'snapAddress' => null,
      'snapName'    =>'',
      'snapImg'     => ''
    ];

    $snap['orderPrice']  = $status['orderPrice'];
    $snap['totleCount']  = $status['totalCount'];
    $snap['pStatus']     = $status['pStatus'];
    $snap['snapAddress'] = json_encode($this->getUserAddress());
    $snap['snapName']    = $this->products[0]['name'];
    $snap['snapImg']     = $this->products[0]['main_img_url'];
    if (count($this->products) > 1) {
      $snap['snapName'] .= '等';
  }
}

  private function getUserAddress() {
    $userAddress = UserAddress::where('user_id','=',$this->uid)->find();
    if (!$userAddress) {
      throw new UserException([
        'msg'       => '用户收货地址不存在，下单失败',
        'errorCode' => 60001,
      ]);
    }
    return $userAddress->toArray();
  }
  private function getOrderStatus() {
    // pStatusArray 保存的是订单中所有商品的详细信息
    // orderPrice 订单中所有商品价格的总和
    $status = [
      'pass'  => true,
      'orderPrice'  => 0,
      'totalCount'  => 0,
      'pStatusArray'  => []
    ];
    // 每一个oProdycts都是一个product_id和count两个字段的数组，存订单中选择商品的id号和数量
    foreach ($this->oProducts as $oProduct) {
      $pStatus = $this->getProductStatus(
        $oProduct['product_id'],$oProduct['count'],$this->products
      );
      if (!$pStatus['haveStock']) {
        $status['pass'] = false;
      }
      $status['orderPrice'] += $pStatus['totlePrice'];
      $status['totalCount'] += $pStatus['count'];
      array_push($status['pStatusArray'],$pStatus);
    }
    return $status;
  }

  private function getProductStatus($oPID,$oCount,$products) {
    $pStatus = [
      'id'  => null,
      'haveStock' => false,
      'count' => 0,
      'name'  => '',
      //totalPrice 订单中某一类的商品总价格
      'totalPrice'  => 0
    ];
    $pIndex = -1;
    // for循环中，i从开始，所以定义$pIndex = -1
    for ($i=0; $i < count($products); $i++) {
      if ($oPID == $products[$i]['id']) {
        $pIndex = $i;
      }
    }
    if ($pIndex == -1) {
      // 客户端传递的product_id可能是根本不存在的
      throw new OrderException([
        'msg' => 'id为'.$oPID.'商品不存在，创建订单失败'
      ]);
    } else {
      $product = $products[$pIndex];
      $pStatus['id'] = $product['id'];
      $pStatus['count'] = $oCount;
      $pStatus['name'] = $oCount['name'];
      // 商品数量乘以商品价格
      $pStatus['totalPrice'] = $product['price'] * $oCount;
      if ($product['stock'] - $oCount >= 0) {
        $pStatus['haveStock'] = true;
      } else {
        $pStatus['haveStock'] = false;
      }
    }
    return $pStatus;
  }

  // 根据订单信息查找真实的商品信息
  private function getProductsByOrder() {
    // 所有订单的商品id号
    $oPIDs = [];
    foreach ($oProducts as $item) {
      array_push($oPIDs,$item['product_id']);
    }
    // 根据商品id来查询相关的商品信息
    $products = Product::all($oPIDs)->visible(['id','price','stock','name','main_img_url']);
    return $products;
  }

}
