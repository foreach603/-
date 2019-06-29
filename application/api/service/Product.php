<?php
/* by ys */
namespace app\api\service;

use app\api\model\Product as ProductModel;
use think\Exception;

class Product
{


    public static function getBicycle()
    {

        $allInfo = ProductModel::field('id,sales_num, name, hot, price, category_id')->where('category_id', 4)
            ->with(['imgByProductId'])->select();

        $allInfoArray = [
            '36V' => [],
            '48V' => [],
            '60V' => [],
            '72V' => [],
        ];
        foreach ($allInfo as $key => $value) {
            $valueNeed = [];
            $name = substr($value['name'],0,3);
            if(count($value['img_by_product_id'])){
                $valueNeed['image'] = IMGFROM . $value['img_by_product_id'][0]['url'];
            }else{
                $valueNeed['image'] = '';
            }
            $valueNeed['name'] = $value['name'];
            $valueNeed['price'] = $value['price'];
            $valueNeed['hid'] = $value['id'];

            if ($name == '36V') {
                array_push($allInfoArray['36V'], $valueNeed);
            } else if ($name == '48V') {
                array_push($allInfoArray['48V'], $valueNeed);
            } else if ($name == '60V') {
                array_push($allInfoArray['60V'], $valueNeed);
            }
            if ($name == '72V') {
                array_push($allInfoArray['72V'], $valueNeed);
            }
            
        }
        return $allInfoArray;
    }
}
