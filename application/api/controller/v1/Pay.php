<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;
use app\api\service\WxNotify;

use think\facade\Request;

class Pay extends BaseController
{
    public function getPreOrder($id = '')
    {
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

    public function receiveNotify()
    {   
        $request = Request::instance()->post();

        UserOld::test($request);
        //   特点:  post  xml格式 链接不会携带参数
        //  1. 检测库存量,超卖
        //  2. 真实的更新这个订单状态  order->status字段
        //  3.  减少库存
        //  4.  如果成功处理 返回微信成功处理消息  否则返回未成功处理
        $notify  = new WxNotify();
        $notify->handle();
    }
}
