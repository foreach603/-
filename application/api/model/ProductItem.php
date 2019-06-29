<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/20
 * Time: 1:34
 */

namespace app\api\model;


use think\Model;

class ProductItem extends BaseModel
{
    protected $hidden = ['img_id'];
    /*
        by:ys
    */

    public function imgUrl()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
        //$this与image模型关联，通过id来找image模型的img_id
    }
    /*
        by:ys
    */

    public function getProperty()
    {
        return $this->belongsTo('ProductProperty', 'id', 'id');
        //$this与product_property模型关联，通过id来找product_property模型的id
    }
    /*----------------------------------------------------------------------------------------------------------------*/
    /*
        by:朱政 
    */
    public function img()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
        //$this与image模型关联，通过id来找image模型的img_id
    }

    /*
        by:朱政
    */
    public function properties()
    {
        return $this->hasMany('ProductProperty', 'product_id', 'product_id');
        //$this与product_property模型关联，通过id来找product_id模型的product_id
    }

}