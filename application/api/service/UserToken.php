<?php
/* by ys*/
namespace app\api\service;
use app\lib\exception\WeChatException;
use app\api\model\User as UserModel;
use app\lib\exception\TokenException;
use think\facade\Cache;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;    

    function __construct($code)
    {   
        $this->code = $code;
        $this->wxAppID =  config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'),
        $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    public function get(){
       
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result,true);
 
        if(empty($wxResult)){
            throw new Exception('获取session_key及openID时异常');
        }else{
            $loginFail = array_key_exists('errcode', $wxResult);
            if($loginFail){
                $this->processLoginError($wxResult);
            }else{
                return $this->grantToken($wxResult);
            }
        }
    }
    private function grantToken($wxResult){
        //拿到openid\
        //数据库里看一下，这个openID是不是已经存在
        //如果参照 则不处理,如果不存在,那么新增一条user记录
        //生成令牌,准备缓存数据，写入缓存
        //把令牌返回到客户端去
        //用户使用的令牌：key
        //value: wxResult,uid,scope(权限)
        $openid = $wxResult['openid'];
        $user = UserModel::getByOpenID($openid);

        if($user){
            $uid = $user->id;
        }else{
            $uid = $this->newUser($openid);
        }
        $cachedValue = $this->prepareCachedValue($wxResult,$uid);
        $token = $this->saveToCache($cachedValue);
        return $token;                                                                                                                                   
    }
    //存入缓存
    private function saveToCache($cacheValue){
        $key = self::generateToken();
        $value = json_encode($cacheValue);
        $expire_in = config('setting.token_expire_in');
        //存入服务器缓存
        $request = Cache::set($key, $value, $expire_in);
        if(!$request){
            throw new TokenException([
                'msg'         => '服务器缓存异常',
                'errorCode'   =>  10005
            ]);
        }
        
        return $key;
    }

    // 如果是新的用户
    private function newUser($openid){
        $create_time = time();
        $user = UserModel::create([
            'openid'     =>  $openid,
            'create_time' =>  $create_time,
        ]);
        return $user->id;
    }

    //准备发送个小程序的数据
    private function prepareCachedValue($wxResult, $uid){
        $cachedValue = $wxResult;

        $cachedValue['uid'] = $uid;
        $cachedValue['scope'] = 16;

        return $cachedValue;
    }
    //异常的时候
    private function processLoginError($wxResult){

        throw new WeChatException(
            [
                'msg'        => $wxResult['errmsg'],
                'errorCode'  => $wxResult['errcode']
            ]
            );
    }
}