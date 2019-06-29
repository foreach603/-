<?php
namespace app\api\controller\v1;

use app\api\validate\CollectNew;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\api\service\Collect as CollectService;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;
use app\api\validate\IDMustBePositiveInt;

class Collect
{
    protected $uid;
    protected $user;
    function __construct()
    {
        // 根据token 获取前台用户的uid
        $this->uid = TokenService::getCurrentUid();
        // 根据uid查询用户信息 
        $this->user = UserModel::get($this->uid);
    }
    /**
     * 商品页面点击添加进收藏夹
     * @param array 获取前端传递post数组
     * @return success
     */
    public function createCollect()
    {
        $validate = new CollectNew();
        $validate->goCheck();
        $data = input('post.');

        $array = $validate->getDataByRule($data);
        $array['user_id'] = $this->uid;

        $result = CollectService::saveOne($array);
        if($result == 0){
            //如果该用户对应的收藏型号不存在,就保存然后返回201
            $result = $this->user->collect()->save($array);
            return '201';
        }else if($result == 1){
            return '200';
        }
        return $result;
    }

    /**
     * 收藏夹页面展示所有收藏商品
     * @param uid  根据user_id查询所有商品
     * @return json 杰森格式返回前台
     */
    public function index()
    {
        if (!$this->user) {
            throw new UserException();
        }
        $uid = TokenService::getCurrentUid();
        $collects = CollectService::getAll($uid);
        $collects = json($collects);
        return $collects;
    }

    public function del($id)
    {
        $validate = new IDMustBePositiveInt();
        $validate->goCheck();
        
        $res = CollectModel::delById($id);
        if (!$res) {
            throw new MissException([
                'msg' => '网络异常，请刷新后再操作',
                'errorCode' => 40000
            ]);
        }
        return json(new SuccessMessage(), 201);
    }
}
