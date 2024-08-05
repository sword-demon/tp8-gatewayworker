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


}
