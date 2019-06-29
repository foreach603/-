<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Category as CategoryModel;
use app\api\model\IndexGood as IndexGoodModel;
//use app\lib\exception\MissException;

class IndexImg extends BaseController {
    
    public function getIndexImg(){
        
        $indexClassify = CategoryModel::getCategory();
        $indexImg = IndexGoodModel::getIndexGood();
        $indexInfo = [
            'indexClassify' => $indexClassify,
            'indexImg'      => $indexImg
        ];
        return json($indexInfo);

    }
}