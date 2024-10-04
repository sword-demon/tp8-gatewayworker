<?php
declare (strict_types=1);

namespace app\validate\api;

use think\Validate;

class ArticleReadLog extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'page|页码' => 'require|integer|>=:1'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];

    protected $scene = [
        'index' => ['page']
    ];
}
