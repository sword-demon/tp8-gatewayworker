<?php
declare (strict_types=1);

namespace app\controller\api\v1;

use app\controller\api\Base;
use app\model\Category as CategoryModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;

class Category extends Base
{
    /**
     * 显示资源列表
     *
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        // 接收参数 type
        $type = request()->param('type');
        // 只查询 id 和 title，增加查询效率
        $data = CategoryModel::field('id,title')
            ->where('type', $type)
            ->where('status', 1) // 1 启用状态
            ->select();

        return apiSuccess('成功', $data);
    }
}
