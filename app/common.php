<?php
// 应用公共文件


// api 成功返回
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

function apiSuccess($msg = 'ok', $data = [])
{
    $res = [
        'code' => 1,
        'msg' => $msg,
        'data' => $data
    ];

    return json($res);
}

// api 失败返回
function apiFail($msg = '请求失败', $statusCode = 400)
{
    $res = [
        'code' => 0,
        'msg' => $msg,
        'data' => null
    ];

    return json($res, $statusCode);
}

// api 异常返回
function ApiException($msg = '异常', $statusCode = 403)
{
    abort($statusCode, $msg);
}

function sendSms(string $phone)
{
    $config = config('api.aliSMS');

    // 判断是否已经发送过
    if (cache($phone)) ApiException("你操作的太快了！");
    // 生产 6 位验证码
    $code = random_int(100000, 999999);
    // 判断是否开启验证码功能
    if (!$config['isopen']) {
        // 将手机号作为 key。将验证码作为 value，存储在缓存中
        cache($phone, $code, $config['expire']);
        return '验证码: ' . $code . ', (演示阶段)';
    }

    // 发生验证码
    AlibabaCloud::accessKeyClient($config['accessKeyId'], $config['accessSecret'])
        ->regionId($config['regionId'])
        ->asGlobalClient();

    try {
        $option = [
            'query' => [
                'RegionId' => $config['regionId'],
                'PhoneNumbers' => $phone,
                'SignName' => $config['SignName'],
                'TemplateCode' => $config['TemplateCode'],
                'TemplateParam' => '{"code": "' . $code . '"}',
            ]
        ];
        $result = AlibabaCloud::rpcRequest()
            ->product($config['product'])
            ->version($config['version'])
            ->action('SendSms')
            ->method('GET')
            ->options($option)
            ->request();
        $res = $result->toArray();
        // 发送成功，写入缓存
        if ($res['Code'] == 'OK') {
            return cache($phone, $code, $config['expire']);
        }

        // 无效号码
        if ($res['Code'] == 'isv.MOBILE_NUMBER_ILLEGAL') {
            ApiException('无效号码');
        }

        // 触发日期限制
        if ($res['Code'] == 'isv.DAY_LIMIT_CONTROL') {
            ApiException('今日你已发送超过限制，改日再来');
        }

        // 发送失败
        ApiException('发送失败');
    } catch (ClientException $e) {
        ApiException($e->getErrorMessage());
    } catch (ServerException $e) {
        ApiException($e->getErrorMessage());
    }
}

/**
 * 验证手机短信
 * @param $phone
 * @param $code
 * @return mixed
 */
function checkSms($phone, $code)
{
    // 获取缓存中的验证码，并且删除缓存，只能验证一次
    $beforeCode = cache($phone);
    // 删除缓存中的验证码
    cache($phone, NULL);
    if (!$beforeCode) return '请重新获取验证码';
    // 验证验证码
    if ($code != $beforeCode) return '验证码错误';
    return true;
}

function createToken($data = [], $prefix = '')
{
    // 生成唯一 token
    $token = sha1(md5(uniqid(md5(microtime(true)), true)));
    $data['token'] = $token;
    // 登录过期时间
    $expire = config('api.token_expire');
    // 保存到缓存中
    $key = $token;
    if ($prefix != '') {
        $key = $prefix . $token;
    }
    // token 缓存
    cache($key, $data, $expire);
    return $token;
}

/**
 * 隐藏手机号码中间 4 位
 * @param string $phone
 * @return string
 */
function maskPhoneNumber(string $phone): string
{
    return substr($phone, 0, 3) . '****' . substr($phone, 7);
}

/**
 * 创建密码
 * @param $password
 * @return string
 */
function createPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * 验证密码
 * @param $password
 * @param $hash
 * @return bool
 */
function checkPassword($password, $hash)
{
    return password_verify($password, $hash);
}

/**
 * 是否是手机号格式
 * @param $phone
 * @return false|int
 */
function isPhoneNumber($phone): bool|int
{
    return preg_match('/^1[34578]\d{9}$/', $phone);
}

/**
 * 是否是邮箱格式
 * @param $email
 * @return bool|int
 */
function isEmail($email): bool|int
{
    return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email);
}

function getUploadPath($path = '')
{
    if (!$path) {
        return $path;
    }

    $path = str_replace("\\", "/", $path);
    // 将本地测试环境的地址替换成线上地址 自动将本地的替换成线上的
    if (strpos($path, "http") !== false) {
        return str_replace("http://127.0.0.1:8000", (request()->root(true)), $path);
    }

    return (request()->root(true)) . '/storage/' . $path;
}