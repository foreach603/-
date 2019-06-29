<?php

return [

  'name'=>'www.php.cn',

];
use think\Config;

dump(config('myconf'));
dump(config('myconf.name'));