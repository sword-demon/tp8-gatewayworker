<?php
declare (strict_types=1);

namespace app\validate\api;

use think\Validate;

class Image extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'image|图片' => 'image|fileSize:10240'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];

    /**
     * 配置场景
     * @var array[]
     */
    protected $scene = [
        // 上传图片
        'upload' => ['image']
    ];
}
