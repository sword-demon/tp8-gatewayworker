<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Topic extends Model
{
    // 关联今日帖子
    public function todayArticle()
    {
        // 查询 article 表的 创建时间字段 在今天的
        return $this->hasMany(Article::class)->whereDay('article.create_time');
    }

    public function category()
    {
        // 重命名 title 字段为 category_name
        return $this->belongsTo(Category::class)->bind(['category_name' => 'title']);
    }

    public static function getTopicList($page, $where)
    {
        $query = self::page($page, 10)
            ->order('id', 'desc');
        if (count($where) > 0) {
            $query = $query->where($where);
        }
        // 分页，按照 id 降序
        return $query->withCount('todayArticle')
            ->paginate(10);
    }
}
