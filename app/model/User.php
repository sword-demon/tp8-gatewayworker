<?php
declare (strict_types=1);

namespace app\model;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * @mixin \think\Model
 */
class User extends Model
{
    public function getNameAttr($value, $data)
    {
        $name = '';
        if ($data['username']) {
            $name = $data['username'];
        } elseif ($data['phone']) {
            // 将手机中间 4 位数字换成 ****
            $name = maskPhoneNumber($data['phone']);
        } elseif ($data['email']) {
            $name = $data['email'];
        } else {
            $name = '未知';
        }

        return $name;
    }

    public static function loginHandle($user)
    {
        if (!is_array($user)) {
            $user = $user->toArray();
        }

        // 生成 token
        $user['token'] = createToken($user);
        $key = "login_" . $user['id'];
        // 用户 token 过期时间缓存
        cache($key, $user['token'], config('api.token_expire'));
        return $user;
    }

    /**
     * 判断用户是否存在
     * @param $key
     * @param $value
     * @return array|false|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function isUserExist($key, $value): mixed
    {
        $user = self::getUserInfo($key, $value);
        if ($user) {
            if ($user->getData('status') == 0) {
                ApiException('用户已被禁用');
            }
            return $user;
        }
        return false;
    }

    /**
     * 获取用户信息（包含虚拟字段 name）
     * @param $key
     * @param $value
     * @return array|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function getUserInfo($key, $value): mixed
    {
        $user = self::where($key, $value)->find();
        if ($user) {
            $user->append(['name']);
        }

        return $user;
    }

    /**
     * 隐藏真实密码
     * @param $value
     * @param $data
     * @return string
     */
    public function getPasswordAttr($value, $data): string
    {
        return $value ? '*******' : '';
    }
}
