<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Follow extends Model
{
    /**
     * 获取用户关注的用户 id 列表
     * @param int $user_id
     */
    public static function getFollowIdListByUserId(int $user_id)
    {
        $follow_user_ids = self::where('user_id', $user_id)
            // 排除被封禁的用户
            ->hasWhere('_follow', ['status' => 1])
            ->column('follow_id');

        return $follow_user_ids;
    }

    public function _follow()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取我是否已关注
     * @param $value
     * @param $data
     * @return true
     */
    public function getCurrentFollowAttr($value, $data)
    {
        return true;
    }
}
