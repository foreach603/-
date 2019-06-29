<?php
namespace app\api\model;
use think\Model;
use think\db\Where;

class User extends BaseModel

{
    protected $hidden = ['delete_time', 'create_time', 'update_time', 'openid'];
    protected $autoWriteTimestamp = true;
    //by ys
    public static function getByOpenID($openid){
        $user = self::where( 'openid','=',$openid )->find();
        
        return $user;
    }
    //by 朱政
    public function address()
    {
        return $this->hasMany('UserAddress', 'user_id', 'id');
    }
    //by 朱政
    public function collect()
    {
        return $this->hasMany('Collect', 'user_id', 'id');
    }
    //by 朱政
    public function shopCart()
    {
        return $this->hasMany('ShoppingCart', 'user_id', 'id');
    }

    //by 朱政
    public function index()
    {
        return $this->hasOne('User', 'id', 'id');
    }
}