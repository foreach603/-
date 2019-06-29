<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\ShoppingCart as ShoppingCartNew;
use app\api\validate\ProductId;
use app\api\validate\ChangeNum;
use think\facade\Request;
use app\api\service\Token as TokenService;
use app\api\service\ShoppingCart as ShoppingCartService;
use app\api\model\Collect as CollectModel;
use app\api\model\ShoppingCart as ShoppingCartModel;



class ShoppingCart extends BaseController{

    public function createOrUpdate( Request $request ){
        (new ShoppingCartNew())->gocheck();
        //根据Token来获取uid
        //根据uid来查找用户数据,判断用户是否存在,如果不存在抛出异常
        //获取用户从客户端提交来的地址信息

        $product_id = $request::param('product_id');
        $behavior = $request::param('behavior');
        $count = $request::param('count');
        $isMeal = $request::param('isMeal');
  
        $uid = TokenService::getCurrentUid();

        $good = ShoppingCartService::addGood($uid,$product_id,$isMeal,$count,$behavior);
        return $good;
    }
    
    public function getShoppingCartDate(){

        $uid = TokenService::getCurrentUid();
        //$product_id = $request::param('product_id');
        $goodList = ShoppingCartService::getAllInfo($uid);

        return $goodList;
    }
    /**
     *  购物车页面内 删除商品
     * @param product_id 
     */
    public function cutOrDelete(Request $request)
    {
        $validate = new ProductId();
        $validate->goCheck();
        $uid = TokenService::getCurrentUid();
        $pid = $request::param('product_id');
        $isMeal = $request::param('isMeal');
        $classify = $request::param('classify');
        $result = ShoppingCartService::softDeleteOne($uid,$pid,$isMeal,$classify);

        if($result){
            return 201;
        }
        
    }
    /*
        购物车页面，商品数量减少
     */
    public function numMinus(Request $request){
        $validate = new ProductId();
        $validate->goCheck();
        $uid = TokenService::getCurrentUid();
        $pid = $request::param('product_id');
        $isMeal = $request::param('isMeal');

        $result = ShoppingCartService::numMinus($uid,$pid,$isMeal);

        return $result;
    }
    public function changeNum(Request $request){
        $validate = new ChangeNum();
        $validate->goCheck();
        $uid = TokenService::getCurrentUid();
        $pid = $request::param('product_id');
        $count = $request::param('count');
        $isMeal = $request::param('isMeal');

        $result = ShoppingCartService::changeNum($uid,$pid,$count,$isMeal);

        return $result;
    }
    public function getCount(){

        $uid = TokenService::getCurrentUid();

        $result =  ShoppingCartService::getCollectShoppingCartCount($uid);

        return json($result);
    }
     /**
     *  购物车页面内 取消收藏按钮
     * @param product_id 
     */
    public function delCollect($product_id)
    {
        $uid = TokenService::getCurrentUid();
        $res = CollectModel::delWithPid($uid, $product_id);
        if (!$res) {
            throw new MissException([
                'msg' => '网络异常，请刷新后再操作',
                'errorCode' => 40000
            ]);
        }
        return "201";
    }
    public function submitShoppingCart(){

        $uid = TokenService::getCurrentUid();
        
        return $uid;
    }
}