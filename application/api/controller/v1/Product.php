<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;

/**
 *
 */
class Product extends BaseController
{
    /**
     * 根据id 获取对应商品的详情
     * 展示在详情页
     */
    public function getOne($id)
    {
        $validate = new IDMustBePositiveInt();
        $validate->goCheck();
        $one = ProductModel::getDetailById($id);

        if (!$one) {
            throw new ProductException();
        }
        return $one;
    }
}
