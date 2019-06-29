<?php
namespace app\api\controller\v1;

use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;

use app\lib\exception\UserException;
use app\api\validate\AddressNew;
use app\api\model\UserAddress;
use app\lib\exception\SuccessMessage;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\MissException;
use app\api\validate\AddressEdit;
use think\facade\Request;
use think\Exception;

class Address
{

    protected $uid;
    protected $user;
    protected $validate;

    function  __construct()
    {
        // 根据token 获取前台用户的uid
        $this->uid = TokenService::getCurrentUid();
        // 根据uid查询用户信息 
        $this->user = UserModel::get($this->uid);
        // 验证传递id值
        $this->validate = new IDMustBePositiveInt();
    }

    /**
     * 获取当前用户所有的地址信息
     * @return json 
     * @author zhuZheng
     */
    public function index()
    {
        if (!$this->user) {
            throw new UserException();
        }
        // 用户存在 根据user表联动address 查询用户所有的地址信息
        $address = $this->user->address->order('update_time, desc');
        //
        return $address;
    }
    /**
     * 为当前用户添加收货地址
     * @return success
     * @author zhuZheng
     */
    public function add()
    {
        $validate = new AddressNew();
        $validate->goCheck();
        $dataArray = $validate->getDataByRule(input('post.'));
        $this->user->address()->save($dataArray);

        return json(new SuccessMessage(), 201);
    }
    /**
     * 为当前用户显示修改收货地址
     * @return json
     * @author zhuZheng
     */
    public function edit($id)
    {
        $this->validate->goCheck();
        $res = UserAddress::showById($id);
        if (!$res) {
            throw new MissException([
                'msg' => '网络异常，请刷新后再操作',
                'errorCode' => 40000
            ]);
        }
        return json($res);
    }

    /**
     * 为当前用户执行修改收货地址
     * @return success
     * @author zhuZheng
     */
    public function doEdit()
    {
        $validate = new AddressEdit();
        $validate->goCheck();
        $dataArray = $validate->editDataByRule(input('post.'));
        $id = $dataArray['id'];
        $res = UserAddress::doEditById($id, $dataArray);
        if (!$res) {
            throw new MissException([
                'msg' => '网络异常，请刷新后再操作',
                'errorCode' => 40000
            ]);
        }
        return '201';
    }
    /**
     * 为当前用户删除收货地址
     * @return success
     * @author zhuZheng
     */
    public function del($id)
    {
        $this->validate->goCheck();
        $res = UserAddress::deleteById($id);
        if (!$res) {
            throw new MissException([
                'msg' => '网络异常，请刷新后再操作',
                'errorCode' => 40000
            ]);
        }
        return 201;
    }
    /**
     * @param int 按当前操作的地址id值
     * @return success 
     **/
    public function setDefault($id)
    {
        // 先根据用户id去找寻是否有默认地址存在
        $uid = $this->user->id;
        $res = UserAddress::findDefaultById($uid);

        if ($res) { // 如果有默认地址  将其取消默认进行下一步
            $id = $res['id'];
            $arr['default'] = 0;
            UserAddress::editDefaultById($id, $arr);
        }
        $newId = input('get.id');
        if ($res['id'] != $newId) { // 当之前默认地址 和当前修改的地址不相同时 将二者默认值调换
            $id = input('get.id');
            $arr['default'] = 1;
            $res = UserAddress::editDefaultById($id, $arr);
        }
        //如果 两次点击都是同一个地址 则第二次点击时间为取消默认 直接在第一步就取消了 逻辑结束
        if (!$res) {
            throw new MissException([
                'msg' => '网络异常，请刷新后再操作',
                'errorCode' => 40000
            ]);
        }
        return  '201';
    }
     /**
      * 获取一条地址
     * @param int 获取地址的id,不写就去找默认地址,默认地址也不存在就返回第一条
     * @return 201
     **/
    public function getOne(Request $request){
        // 先根据用户id去找寻是否有默认地址存在
        $uid = $this->user->id;
        $id = $request::param('id');
        if($id != null){
            $good = UserAddress::showById($id);
            
        }else{
            $good = UserAddress::findDefaultById($uid);
        }
        if($good == null){
            $good = UserAddress::getOne($uid);
        }
        return $good;
    }
}
