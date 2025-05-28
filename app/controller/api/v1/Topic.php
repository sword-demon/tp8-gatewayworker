<?php
declare (strict_types=1);

namespace app\controller\api\v1;

use app\controller\api\Base;
use app\model\Topic as TopicModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Request;
use think\Response;

class Topic extends Base
{
    /**
     * 查询指定分类下的话题列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 接收分类 id
        $category_id = request()->param('category_id', 0);
        // 获取分页页码
        $page = (int)request()->param('page', 1);
        $where = [];
        if ($category_id != 0) {
            $where = ['category_id' => $category_id];
        }

        $data = TopicModel::getTopicList($page, $where);

        return apiSuccess('ok', $data);
    }

    /**
     * 获取帖子详情
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function read()
    {
        $id = request()->param('id', 0);
        $data = TopicModel::withCount(['todayArticle'])->with('category')->find($id);
        if (!$data) {
            return apiFail('没有找到数据', 404);
        }
        return apiSuccess('ok', $data);
    }

    public function search()
    {
        $keyword = request()->param('keyword', '');
        $page = request()->param('page', 1);
        $where = [
            ['title', 'like', '%' . $keyword . '%']
        ];

        return apiSuccess('ok', TopicModel::getTopicList($page, $where));
    }
}
