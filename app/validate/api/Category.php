<?php
declare (strict_types=1);

namespace app\validate\api;

use think\Validate;

class Category extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        // 取值范围是 2 个字符串内容 多个以逗号隔开不能有空格
        'type|分类类型' => 'require|in:article,topic'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];

    // 验证场景
    protected $scene = [
        'index' => ['type']
    ];
}
