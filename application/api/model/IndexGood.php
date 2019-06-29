<?php

namespace app\api\model;

use think\Model;

class IndexGood extends BaseModel
{
    public function items()
    {   
        /*一对多查询函数 */
        /* 使用结构hasMany('关联模型','外键','主键');*/
        /*关联模型（必须）：模型名或者模型类名
        外键：关联模型外键，默认的外键名规则是当前模型名+_id
        主键：当前模型主键，一般会自动获取也可以指定传入*/
        return $this->hasMany('GoodItem', 'good_id', 'id');
    }
    public function img()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }

    /**
     * @param $id int banner所在位置
     * @return Banner
     */
    public static function getIndexGood()
    {
        
        $indexImg = Product::where('index','=','1' )->with(['imgID','imgID.imgUrl'])->select();
        $indexGoodList = [];
        foreach($indexImg as $key => $value){
            $indexGood = [];
            $indexGood['hid'] = $value['id'];
            $indexGood['name'] = $value['name'];
            $indexGood['image'] = IMGFROM.$value['img_i_d']['img_url']['url'];
            $indexGoodList[] = $indexGood;
        }
        return $indexGoodList;
    }
}
