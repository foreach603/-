<?php 
//模型之间的关系

/*$result = Db::query('select * from banner_item where banner_id=?',[$id]);
return $result;

$result = Db:table('banner_item')->where('banner_id','=',$id)->select();
//where('字段名'，'表达式'，'查询条件')*/

hasMany('BannerItem','banner_id','id');
/*
1.关联模型
 */


$banner = BannerModel::with('items')->find($id);
// 一对多  hasMany
// 一对一  belongsTo
