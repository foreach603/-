<?php
namespace app\api\model;
use app\api\model\Image;
use app\api\model\Product;

class Order extends BaseModel
{
    // public function productInfo()
    // {
    //     return $this->hasOne('Image', 'product_id', 'id');
    // }

    protected $autoWriteTimestamp = true;

    public function getSnapItemsAttr($value)
    {
        if (empty($value)) {
            return null;
        }
        return json_decode($value);
    }
    public function getSnapAddressAttr($value)
    {
        if (empty($value)) {
            return null;
        }
        return json_decode($value);
    }
    public static function getSummaryByUser($uid, $page = 1, $size = 15)
    {
        $paginate = self::where('user_id', $uid)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $paginate;
    }
    public static function getOder($where){

        $res = self::field('order_no,total_price,snap_items,snap_address,id,status')->where($where)->select();
        $snap_items = [];
        foreach($res as &$value){
            $tem = $value['snap_items'];
            foreach($tem as &$value2){
                // $value2->image =Image::getImgByProductId($value2->id);
                $value2->productInfo = Product::getInfoByProductId($value2->id);
                // $value2->productInfo->img = IMGFROM .$value2->productInfo->img['url'];
            }
            // $snap_items = json_endo$tem;
            $value['snap_items'] = json_encode($tem);
        }
       
        return $res;
    }
}
