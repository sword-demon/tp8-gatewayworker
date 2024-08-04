<?php
declare (strict_types=1);

namespace app\controller\api\v1;

use think\Request;
use app\controller\api\Base;
use app\model\Article as ArticleModel;
use app\model\Topic as TopicModel;
use app\model\Category as CategoryModel;

class Article extends Base
{

    /**
     * 发布帖子
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }


}
