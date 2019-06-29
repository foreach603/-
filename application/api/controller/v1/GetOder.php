<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\service\Token as TokenService;
use app\api\model\Order as OrderModel;
use think\facade\Request;

class GetOder extends BaseController {
    
    public function getNoPay( Request $request ){
        $uid = TokenService::getCurrentUid();
        $type = $request::param('type');

        if($type != null && $type != 4){
             $where['status'] = $type;
        }
        $where['is_delete'] = 1; 
        if($type == 4 ){
            $where['is_delete'] = 0; 
        }
        $where['user_id'] = $uid; 
        $res = OrderModel::getOder($where);
        return $res;
    }
}