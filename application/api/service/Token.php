<?php 
namespace app\api\service;
use think\facade\Request;
use think\facade\Cache;
use app\lib\exception\TokenException;

class Token{
    public static function generateToken(){
        //32个字符串组成一组随机字符串
        $randChars = getRandChar(32);
        //时间戳
        $timesamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //salt 盐
        $salt = config('secure.token_salt');
        $info = $randChars.$timesamp.$salt;
        return md5($info);
    }
    
    public static function getCurrentTokenVar($key){
        // $request = Request::instance()->post();
        // $token = $request['token'];
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if(!$vars){
            throw new TokenException();
        }else{
            if(!is_array($vars)){
                $vars = json_decode($vars,true);
            }
            if(array_key_exists($key,$vars)){
                return $vars[$key];
            }else{
                throw new Exception('尝试获取的Token变量并不存在');
            }
            
        }
    }

    public static function getCurrentUid(){
        //token
        $uid = self::getCurrentTokenVar('uid');
        return $uid;

    }
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope == ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    public static function isValidOperate($checkUID)
    {
        if (!$checkUID) {
            throw new Exception('被检测的UID不能为空 !');
        }
        $currentOpreateUID = self::getCurrentUid();
        if ($currentOpreateUID == $checkUID) {
            return true;
        }
        return false;
    }
}