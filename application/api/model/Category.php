<?php

namespace app\api\model;

use think\Model;

class Category extends BaseModel
{
    /*
        by:ys
     */
    protected $hidden = ['topic_img_id', 'id', 'description','delete_time','update_time'];
    public function products()
    {
        return $this->hasMany('Product', 'category_id', 'id');
    }

    public function img()
    {
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }
    /*
        by:ys
     */
        
    public static function getCategory()
    {
        $category = self::with(['img'])->all();
        $indexGoodList = [];
        foreach($category as $key => $value)
        {
            $indexGood = [];
            $indexGood['hid'] = $value['id'];
            $indexGood['name'] = $value['name'];
            $indexGood['image'] = IMGFROM.$value['img']['url'];
            $indexGoodList[] = $indexGood;
        }
        return $indexGoodList;
    }
}
