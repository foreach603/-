<?php

namespace app\api\model;

use think\Model;

class Express extends BaseModel
{
   
    public static function getExpress($id)
    {
        $express = self::where('order_id','=',$id)->field('express_number,express_company')->find();
        return $express;
    }
}

