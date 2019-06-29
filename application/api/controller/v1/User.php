<?php
namespace app\api\controller\v1;

use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\lib\exception\UserException;
use app\api\validate\UserMessageNew;
use app\lib\exception\SuccessMessage;

class User
{

    protected $uid;
    protected $user;

    function __construct()
    {
        $this->uid = TokenService::getCurrentUid();
        $this->user = UserModel::get($this->uid);
    }
    /**
     * [index description]
     * @Author   zzBlazers
     * @DateTime 2019-05-24T11:08:47+0800
     * @return  json 用户资料信息展示
     */
    public function index()
    {
        if (!$this->user) {
            throw new UserException();
        }

        $userIndex = $this->user->index;
        unset($userIndex['id']);
        return $userIndex;
    }

    public function edit()
    {
        //添加用户信息
        $validate = new UserMessageNew();
        $validate->goCheck();
        $dataArray = $validate->getDataByRule(input('post.'));
        $dataArray = input('post.');
  
        $userIndex = $this->user->index;
        if (!$userIndex) {
            $this->user->index()->save($dataArray);
        } else {
            $this->user->index->save($dataArray);
        }
        return  '201';
    }
}
