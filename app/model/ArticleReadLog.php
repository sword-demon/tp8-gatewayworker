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
class ArticleReadLog extends Model
{

    public function article(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * 更新阅读记录
     * @param int $id 帖子 id
     * @param mixed $data 当前的帖子对象数据
     * @return array|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function updateReadLog(int $id, mixed $data)
    {
        // 获取当前的登录用户 id
        $user_id = getCurrentUserIdByToken();
        // 获取当前帖子对象
        if (!$data) {
            $data = Article::find($id);
        }
        // 获取请求客户端的 ip 地址
        $ip = request()->ip();
        $where = [
            'ip' => $ip,
            'article_id' => $id,
        ];
        // 如果对方登录了，就记录一下当前登录用户 id
        if ($user_id) {
            // 追加一个查询条件
            $where['user_id'] = $user_id;
        }
        // 是否之前阅读过该帖子
        $log = self::where($where)->find();
        // 不存在就创建 存在更新最后一次阅读时间
        if ($log) {
            $log->update_time = time();
            $log->save();
            return $data;
        }
        // 阅读数+1
        $data->read_count += 1;
        $data->save();

        // 添加阅读记录
        self::create($where);

        return $data;
    }
}
