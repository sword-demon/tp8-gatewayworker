<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Collection extends Model
{
    /**
     * 判断当前用户是否收藏该帖子
     * @param int $id 帖子 id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function isCurrentUserCollectArticle(int $id)
    {
        // 获取当前登录用户 id
        $user_id = getCurrentUserIdByToken();
        if (!$user_id) {
            return false;
        }
        $collection = self::where(['user_id' => $user_id, 'article_id' => $id])->find();
        if ($collection) {
            return true;
        }
        return false;
    }
}
