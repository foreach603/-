<?php
namespace app\api\model;

use think\Model;

class ProductImage extends Model
{
    protected $hidden = ['id', 'img_id', 'order', 'product_id'];
    public function img()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}
