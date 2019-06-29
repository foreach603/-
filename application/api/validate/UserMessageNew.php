<?php
namespace app\api\validate;

class UserMessageNew extends BaseValidate
{
    protected $rule = [
        'nickname' => 'require|isNotEmpty',
        'mobile' => 'require|isMobile',
        'birthday' => 'require|isNotEmpty',
        'province' => 'require|isNotEmpty',
        'country' => 'require|isNotEmpty',
        'city' => 'require|isNotEmpty'
    ];
}
