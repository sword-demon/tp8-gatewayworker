<?php
declare (strict_types=1);

namespace app\controller\api\v1;

use app\controller\api\Base;
use app\model\ArticleReadLog as ArticleReadLogModel;
use app\model\Article as ArticleModel;
use think\Request;

class ArticleReadLog extends Base
{
    /**
     * 查询文章观看历史记录列表
     */
    public function index()
    {
        // 获取当前登录的用户 id
        $userId = getCurrentUserIdByToken();
        $page = request()->param('page', 1);

        $where["user_id"] = $userId;

        $data = ArticleReadLogModel::page($page, 10)
            ->where($where)
            ->field('article_id,update_time')
            ->with(['article' => function ($query) {
                ArticleModel::withArticleDetail($query->hidden(['content']));
            }])
            ->order('update_time', 'desc')
            ->paginate(10)
            ->map(function ($item) {
                return ArticleModel::formatArticleItem($item->article);
            });

        return apiSuccess('ok', $data);
    }
}
