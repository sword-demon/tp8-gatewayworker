<?php
declare (strict_types=1);

namespace app\validate\api;

use think\Validate;

class Topic extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'category_id|分类 id' => 'require|integer',
        'page|分页' => 'require|integer'
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
        'index' => ['category_id', 'page']
    ];
}
