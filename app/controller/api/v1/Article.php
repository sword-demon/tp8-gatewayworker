<?php
declare (strict_types=1);

namespace app\controller\api\v1;

use app\model\ArticleReadLog;
use app\model\Collection;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Request;
use app\controller\api\Base;
use app\model\Article as ArticleModel;
use app\model\Topic as TopicModel;
use app\model\Category as CategoryModel;
use think\response\Json;

class Article extends Base
{

    public function index(): Json
    {
        // 排序
        $orderBy = request()->param("order");
        $order = "id desc";

        // 最新
        if ($orderBy == "new") {
            $order = "create_time desc";
        } // 最热门
        elseif ($orderBy == "hot") {
            $order = "ding_count,id desc";
        }

        $param = request()->param();
        // 分类
        $value = \request()->param('category_id', 0);
        $key = "category_id";
        if (array_key_exists("topic_id", $param)) {
            $value = \request()->param('topic_id', 0);
            $key = "topic_id";
        }
        if (array_key_exists("user_id", $param)) {
            $value = \request()->param('user_id', 0);
            $key = "user_id";
        }

        // 话题 id 或者分类 id或者用户 id
        $where = [
            $key => $value,
        ];

        // 分页页码
        $page = request()->param('page', 1);

        $data = [];
        if ($key == "category_id" && $value == 0) {
            // 查询我关注人的帖子列表
            $data = ArticleModel::getMyFollowArticleList($page, $order);
        } else {
            $data = ArticleModel::getArticleList($page, $where, $order);
        }

        return apiSuccess('ok', $data);
    }

    /**
     * 发布帖子
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $user = request()->currentUser;
        $param = $request->param();
        $data = [
            'category_id' => $param['category_id'],
            'user_id' => $user->id,
            'content' => $param['content'],
            'images' => $param['images']
        ];
        // 话题是否存在
        if (array_key_exists('topic_id', $param) && $param['topic_id'] > 0) {
            if (!TopicModel::find($param['topic_id'])) {
                ApiException('话题不存在');
            }
            $data['topic_id'] = $param['topic_id'];
        }
        $article = new ArticleModel();
        $res = $article->save($data);
        if ($res) {
            return apiSuccess('发布成功');
        }
        return apiFail('发布失败');
    }


    /**
     * 帖子详情
     * @param int $id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function read(int $id)
    {
        $data = ArticleModel::getArticleById($id);
        if (!$data) {
            ApiException('帖子不存在');
        }
        // 更新阅读记录
        $data = ArticleReadLog::updateReadLog($id, $data);
        // 判断当前用户是否收藏该帖子

        // 追加一个字段进行展示
        $data->isCollect = Collection::isCurrentUserCollectArticle($id);
        return apiSuccess('ok', $data);
    }
}
