<?php
namespace app\api\controller\v1;
use think\Controller;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDCollection;
use app\api\validate\IDMustBePostiveInt;
use app\api\lib\exception\ThemeException;
class Theme extends Controller {
  public function getSimpleList($ids = '') {
    /**
    *@url /theme?ids=id1,id2,id3...
    *@return 一组theme模型
    */
    (new IDCollection())->goCheck();
    $ids = explode(',',$ids);
    $result = ThemeModel::with(['topicImg','headImg'])->select($ids);
    if (!$result) {
      throw new ThemeException();
    }

    return $result;
  }

  public function getComplexOne($id) {
    (new IDMustBePostiveInt())->goCheck();
    $theme = ThemeModel::getThemeWithProduct($id);
    if (!$theme) {
      throw new ThemeException();
    }
    return $theme;
  }
}
