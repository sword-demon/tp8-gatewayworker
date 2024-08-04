<?php
declare (strict_types=1);

namespace app\controller\api\v1;

use app\controller\api\Base;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Paginator;
use think\Request;
use think\response\Json;

class User extends Base
{
    /**
     * 暂时设置修改密码方法为无需验证器验证；测试完移除
     * @var array|string[]
     */
    protected array $excludeValidateCheck = ['changePassword', 'logout', 'sendCode2'];

    /**
     * 获取验证码
     * @return \think\response\Json
     */
    public function sendCode(): \think\response\Json
    {
        // 获取手机号码
        $phone = \request()->param('phone');
        // 调用发送短信接口
        $res = sendSms($phone);
        // 如果是调试模式，没有开启阿里云短信，则直接将验证码返回
        if (!config('api.aliSMS.isopen')) {
            return apiSuccess($res);
        }
        return apiSuccess('发送成功');
    }

    /**
     * 获取验证码(用来修改密码)
     * @return \think\response\Json
     */
    public function sendCode2(): \think\response\Json
    {
        // 获取当前用户的手机号码
        $phone = \request()->currentUser->getData('phone');
        if (!$phone) {
            ApiException('请先绑定手机号');
        }
        try {
            // 调用发送短信接口
            $res = sendSms($phone);
            // 如果是调试模式，没有开启阿里云短信，则直接将验证码返回
            if (!config('api.aliSMS.isopen')) {
                return apiSuccess($res);
            }
            return apiSuccess('发送成功');
        } catch (\Throwable $th) {
            ApiException($th->getMessage());
        }
    }

    /**
     * 手机号验证码登录
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function phoneLogin()
    {
        // 获取所有参数
        $phone = input('phone');
        $code = input('code');
        // 验证验证码
        $msg = checkSms($phone, $code);
        if ($msg !== true) {
            ApiException($msg);
        }

        // 验证用户是否存在
        $user = \app\model\User::isUserExist('phone', $phone);
        // 用户不存在，直接注册
        if (!$user) {
            // 直接创建用户
            $user = \app\model\User::create([
                'phone' => $phone
            ]);
            // 获取新用户信息
            $user = \app\model\User::query()->find($user->id);
        }
        // 登录成功
        return apiSuccess('登录成功', \app\model\User::loginHandle($user));
    }

    public function changePassword(): Json
    {
        // 获取所有参数
        $params = \request()->param();
        // 获取当前用户信息
        $user = \request()->currentUser;
        // 验证验证码
        // 通过 getData 获取器获取原始信息，否则是获取的修改器修改过后的内容
        $msg = checkSms($user->getData('phone'), $params['code']);
        if ($msg !== true) {
            ApiException($msg);
        }
        // 修改密码
        $user->password = createPassword($params['password']);
        $res = $user->save();
        if (!$res) {
            ApiException('修改密码失败');
        }
        return apiSuccess('修改密码');
    }

    // 用户密码登录
    public function login()
    {
        // 获取所有参数
        $params = \request()->param();
        // 验证用户是否存在
        $key = "";
        $value = $params['username'];
        if (isPhoneNumber($value)) {
            $key = 'phone';
        } elseif (isEmail($value)) {
            $key = 'email';
        } else {
            ApiException('账号错误');
        }
        // 验证手机号码是否规范
        $user = \app\model\User::isUserExist($key, $value);
        if (!$user) {
            ApiException('用户不存在');
        }
        // 验证密码是否正确
        if (!checkPassword($params['password'], $user->getData('password'))) {
            ApiException('密码错误');
        }
        // 登录成功
        return apiSuccess('登录成功', \app\model\User::loginHandle($user));
    }

    public function logout()
    {
        // 清除 token
        $header = \request()->header();
        if (array_key_exists('token', $header)) {
            $user = cache($header['token']);
            cache($header['token'], null);
            if ($user) {
                // 清除登录状态
                cache('login_' . $user['id'], null);
            }
        }

        return apiSuccess('退出成功');
    }
}
