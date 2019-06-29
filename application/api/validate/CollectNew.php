<?php
namespace app\api\validate;

class CollectNew extends BaseValidate
{
    protected $rule = [
        'product_id' => 'require|number'
    ];
}
