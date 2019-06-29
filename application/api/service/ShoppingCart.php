<?php
/* by ys */
namespace app\api\service;

use app\api\model\ShoppingCart as ShoppingCartModel;
use app\api\model\Collect as CollectModel;
use think\Exception;

class ShoppingCart
{

    public static function addGood($uid, $hid, $isMeal, $count, $behavior)
    {
        $where = [
            ['user_id', '=', $uid],
            ['product_id', '=', $hid],
            ['is_delete', '=', '1']
        ];

        $good = ShoppingCartModel::checkOneGood($where);

        if (count($good) == 0) {

            $create_time = time();
            if ($isMeal == "1") {
                $charger_count = $count;
            } else {
                $charger_count = 0;
            }
            $data = [
                'user_id' => $uid, 'product_id' => $hid, 'count' => 1, 'create_time' => $create_time,
                'count' => $count, 'charger_count' => $charger_count
            ];

            $insertData = ShoppingCartModel::addOneGood($data);

            if (!$insertData) {
                throw new Exception('添加数据失败');
            }
        } else {
            if ($behavior == 'add') {

                if ($isMeal == "1") {
                    $data = [
                        'count' => (int)$good['count'] + 1,
                        'update_time' => time(),
                        'charger_count' => (int)$good['charger_count'] + 1
                    ];
                } else {
                    $data = [
                        'count' => (int)$good['count'] + 1,
                        'update_time' => time(),
                    ];
                }

                $id = $good['id'];
                $insertData = ShoppingCartModel::updateGood($id, $data);

                if (!$insertData) {
                    throw new Exception('更新数据失败');
                }
            } else {

                if ($isMeal == "1") {
                    $data = [
                        'count' => (int)$good['count'] + (int)$count,
                        'update_time' => time(),
                        'charger_count' => (int)$good['charger_count'] + (int)$count
                    ];
                } else {
                    $data = [
                        'count' => (int)$good['count'] + (int)$count,
                        'update_time' => time(),
                    ];
                }

                $id = $good['id'];
                $insertData = ShoppingCartModel::updateGood($id, $data);

                if (!$insertData) {
                    throw new Exception('更新数据失败');
                }
            }
        };
        return 201;
    }
    public static function getAllInfo($uid)
    {

        $goodList = ShoppingCartModel::checkAllGoodInfo($uid);
        $collectList = CollectModel::getAllProduct($uid);
        $goodDateList = [];
        foreach ($goodList as $key => $value) {
            $goodDate = [];

            $goodDate['hid'] = $value['product_id'];
            $goodDate['collect'] = 0;
            foreach ($collectList as $collect) {
                if ($goodDate['hid'] == $collect['product_id']) {
                    $goodDate['collect'] = 1;
                }
            }
            $goodDate['charger_count'] = $value['charger_count'];
            $goodDate['count'] = $value['count'];
            $goodDate['name'] = $value['productinfo']['name'];
            $goodDate['classify'] = $value['productinfo']['classify'];
            $goodDate['freight'] = $value['productinfo']['freight'];
            $goodDate['sale'] = $value['productinfo']['sale'];
            $goodDate['price'] = $value['productinfo']['price'];
            if (count($value['product_img'])) {
                $goodDate['img'] = IMGFROM . $value['product_img']['url'];
            } else {
                $goodDate['img'] = '';
            }

            array_push($goodDateList, $goodDate);
        }
        $goodDateList = json($goodDateList);
        return $goodDateList;
    }
    public static function softDeleteOne($uid, $pid, $isMeal, $classify)
    {
        $where = [
            ['user_id', '=', $uid],
            ['product_id', '=', $pid],
            ['is_delete', '=', '1']
        ];
        $good = ShoppingCartModel::checkOneGood($where);

        $id = $good['id'];
        if ($classify != 4) {

            $data = [
                'is_delete' => 0,
                'delete_time' => time()
            ];
        } else {
            if ($isMeal == 1) {
                
                if ($good['count'] == $good['charger_count']) {
                    $data = [
                        'is_delete' => 0,
                        'delete_time' => time()
                    ];
                }else{
                   
                    $data = [
                        'count' => $good['count'] - $good['charger_count'],
                        'charger_count' => 0,
                        'update_time' => time()
                    ];
                }
            }else{
                if($good['charger_count'] == 0){
                    $data = [
                        'is_delete' => 0,
                        'delete_time' => time()
                    ];
                }else{
                    $data = [
                        'count' =>  $good['charger_count'],
                        'update_time' => time()
                    ];
                }
            }
        }
        $insertData = ShoppingCartModel::updateGood($id, $data);
        if (!$insertData) {
            throw new Exception('添加数据失败');
        }

        return 201;
    }
    public static function numMinus($uid, $pid, $isMeal)
    {
        $where = [
            ['user_id', '=', $uid],
            ['product_id', '=', $pid],
            ['is_delete', '=', '1']
        ];
        $good = ShoppingCartModel::checkOneGood($where);
        $count = $good['count'];
        if ((int)$count <= 1) {
            return 202;
        } else {
            $id = $good['id'];

            if ($isMeal == "1") {
                if ($good['charger_count'] <= 1) {
                    return 202;
                } else {
                    $data = [
                        'count' => (int)$good['count'] - 1,
                        'update_time' => time(),
                        'charger_count' => (int)$good['charger_count'] - 1
                    ];
                }
            } else {
                $data = [
                    'count' => (int)$good['count'] - 1,
                    'update_time' => time()
                ];
            }

            $insertData = ShoppingCartModel::updateGood($id, $data);
            if (!$insertData) {
                throw new Exception('添加数据失败');
            }
            return 201;
        }
    }
    public static function changeNum($uid, $pid, $count, $isMeal)
    {
        $where = [
            ['user_id', '=', $uid],
            ['product_id', '=', $pid],
            ['is_delete', '=', '1']
        ];
        $good = ShoppingCartModel::checkOneGood($where);
        if ((int)$count <= 1) {
            return 202;
        } else {
            $id = $good['id'];
            if ($isMeal == '1') {
                if ($good['charger_count'] <= 1) {
                    return 202;
                }
                $data = [
                    'count' => (int)$count,
                    'update_time' => time(),
                    'charger_count' => (int)$count
                ];
            } else {
                $data = [
                    'count' => (int)$count,
                    'update_time' => time()
                ];
            }

            $insertData = ShoppingCartModel::updateGood($id, $data);
            if (!$insertData) {
                throw new Exception('添加数据失败');
            }
            return 201;
        }
    }
    public static function getCollectShoppingCartCount($uid)
    {

        $where = [
            ['user_id', '=', $uid],
            ['is_delete', '=', '1']
        ];
        $shoppingCartCount = ShoppingCartModel::getCount($where);
        $collectCount = CollectModel::getCount($uid);

        $countList = [
            'shoppingCartCount' => $shoppingCartCount,
            'collectCount'      => $collectCount
        ];
        return $countList;
    }
}
