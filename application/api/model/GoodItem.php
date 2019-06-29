<?php

namespace app\api\model;

use think\Model;

class GoodItem extends BaseModel
{

    public function img()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}
