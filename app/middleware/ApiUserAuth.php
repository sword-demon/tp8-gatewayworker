<?php
declare (strict_types=1);

namespace app\middleware;

use app\model\User;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Request;

class ApiUserAuth
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param \Closure $next
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function handle($request, \Closure $next)
    {
        // 获取头部信息
        $param = $request->header();
        // 不含 token
        if (!array_key_exists('token', $param)) {
            ApiException('登录已失效，请重新登录');
        }
        // 当前用户 token 是否存在
        $user = cache($param['token']);
        if (!$user) {
            ApiException('登录已失效，请重新登录');
        }
        // 将 token 和 userid 这类常用的参数放在 request 中
        $request->userToken = $param['token'];
        $request->userId = $user['id'];
        $u = User::isUserExist('id', $user['id']);
        if (!$u) {
            ApiException('当前用户不存在');
        }
        // 当前用户信息存储到 $request 中
        $request->currentUser = $u;

        // 继续执行
        return $next($request);
    }
}
