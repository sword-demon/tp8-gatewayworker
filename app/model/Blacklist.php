<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Blacklist extends Model
{
    // 获取 被我拉黑/我被拉黑作者 ID
    public static function getBlackUsers(): array
    {
        // 获取当前登录的用户 ID
        $user_id = getCurrentUserIdByToken();
        if (!$user_id) {
            return [];
        }
        // 查询我拉黑的用户 ID
        $v1 = self::where("user_id", $user_id)->column("black_id");
        // 查询我被对方拉黑的用户 ID
        $v2 = self::where("black_id", $user_id)->column("user_id");

        return array_merge($v1, $v2);
    }
}
