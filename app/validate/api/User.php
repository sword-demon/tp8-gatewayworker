<?php
declare (strict_types=1);

namespace app\validate\api;

use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'phone' => ['require', 'mobile'],
        'code' => ['require', 'length:6'],
        'password' => ['require'],
        'username' => ['require']
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'phone.require' => '手机号码不能为空',
        'phone.mobile' => '手机号码无效',
        'code.require' => '验证码不能为空',
        'code.length' => '验证码长度不超过 6',
        'password.require' => '密码不能为空',
        'username.require' => '用户名不能为空',
    ];

    /**
     * 验证场景
     * 格式: '场景' => ['字段1']
     * @var array[]
     */
    protected $scene = [
        // 获取验证码的验证
        'sendCode' => ['phone'],
        // 手机号验证码登录
        'phoneLogin' => ['phone', 'code'],
        // 用户名密码登录
        'login' => ['username', 'password'],
    ];

    // 修改密码验证场景
    public function sceneChangePassword(): User
    {
        return $this->only(['password', 'code'])
            ->append('password', 'length:6,20');
    }
}
