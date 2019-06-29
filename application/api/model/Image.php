<?php

namespace app\api\model;

use think\Model;

class Image extends BaseModel
{
    protected $hidden = ['delete_time', 'id', 'from'];

    /*
        by:朱政
     */
    public function getUrlAttr($value, $data)
    {
        $finalUrl = $value;
        if ($data['from'] == 1) {
            $finalUrl = config('setting.img_prefix') . $value;
        }
        return $finalUrl;
    }
    public static function getImgByProductId($id)
    {
        $img = self::where('product_id','=',$id)->fe->find();
        return $img;
    }
}

