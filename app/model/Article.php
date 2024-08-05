<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Article extends Model
{
    /**
     * 查询帖子详情
     * - 关联关注表
     * - 关联用户表
     * - 关联帖子表
     * - 关联点赞，收藏表
     * @param int $id
     * @return mixed
     */
    public static function getArticleById(int $id)
    {
        $query = self::where('id', $id);
        $query = self::withArticleDetail($query);
        $data = $query->find();
        if (!$data) {
            ApiException('帖子不存在');
        }
        return self::formatArticleItem($data);
    }

    private static function withArticleDetail(Article $query)
    {
        // 获取当前登录的用户 ID
        $currentUserId = getCurrentUserIdByToken();

        $query = $query->with([
            // 关联查询用户
            'user',
            // 关联查询话题的名称
            'topic',
            // 判断是否已经顶踩帖子
            'mySupport' => function (\think\Db\Query $query) use ($currentUserId) {
                // 子查询 where 条件 key 值必须是 表名称.表字段名
                $query->where('support.user_id', $currentUserId);
            },
            // 判断是否已经关注该作者
            "isFollowCurrentUser" => function (\think\Db\Query $query) use ($currentUserId) {
                $query->where('follow.user_id', $currentUserId);
            }
        ]);

        return $query;
    }

    /**
     * 格式化结果
     * @param $item
     * @return mixed
     */
    private static function formatArticleItem($item)
    {
        $item->isfollow = $item->isfollow == null ? false : true;
        if ($item->user_support_action == null) {
            $item->user_support_action = "";
        }
        return $item;
    }

    /**
     * 关联我得 support 当前用户是否顶踩了帖子
     * @return \think\model\relation\HasOne
     */
    public function mySupport()
    {
        // user_support_action 是 Support 模型获取器的 虚拟字段
        return $this->hasOne(Support::class)->field('id,type,user_id,article_id')
            ->bind(['user_support_action']);
    }

    public function isFollowCurrentUser()
    {
        return $this->hasOne(Follow::class, 'follow_id', 'user_id')
            ->field("id,user_id,follow_id")
            ->bind(['isfollow' => 'current_follow']);
    }

    /**
     * 自动设置标题
     * @param $value
     * @param $data
     * @return void
     */
    public function setContentAttr($value, $data)
    {
        $content = $value;
        // 从 content 中截取前 100 个字符作为标题
        $title = mb_substr($value, 0, 100, 'utf-8');
        // 如果截取的内容包含 HTML 标签， 去除这些标签
        $title = strip_tags($title);
        if (mb_strlen($title, 'utf-8') > 100) {
            $title .= '...';
        }
        $this->set('title', $title);
        return $content;
    }

    /**
     * 自动将图片数组转换成字符串
     * @param $value
     * @return string
     */
    public function setImagesAttr($value)
    {
        if (is_array($value)) {
            return implode(',', $value);
        }
        return $value;
    }

    /**
     * 发帖成功后的触发事件
     * @param $article
     * @return void
     */
    public static function onAfterInsert($article)
    {
        // 更新用户帖子数
        User::updateArticlesCount($article->user);
        // 更新话题帖子数
        // 话题不一定必填
        if ($article->topic_id) {
            Topic::updateArticlesCount($article->topic);
        }
    }

    // 关联用户
    public function user()
    {
        // 只查询关联的表的几个字段或者给字段起别名
        return $this->belongsTo(User::class)->bind(['name', 'avatar', 'user_status' => 'status']);
    }

    // 关联话题
    public function topic()
    {
        // 只查询关联的话题的名称
        return $this->belongsTo(Topic::class)->bind(['topic_name' => 'title']);
    }
}
