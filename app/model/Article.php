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
        return $this->belongsTo(User::class);
    }

    // 关联话题
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
