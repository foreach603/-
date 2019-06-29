<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Product as ProductModel;
use app\api\service\Product as ProductService;


class GetGoodInfo extends BaseController {
    
    public function getAllInfo(){
        
      
        $allProduct = ProductModel::getAllGood();
        return json($allProduct);
    }
    public function getBicycle(){

        $allProduct = ProductService::getBicycle();
        return json($allProduct);
    }
}