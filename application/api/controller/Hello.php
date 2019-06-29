<?php
//命名空间
namespace app\api\controller;

//引入api
use think\Response;
use app\api\validate\IDMustBePositiveInt;

class Hello
{
    public function say($id)
    {
    	$validate = new IDMustBePositiveInt;

        echo $id;
    }
}