<?php
declare (strict_types=1);

namespace app\validate\api;

use think\Validate;

class Article extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'content|内容' => 'require',
        'category_id|分类' => 'require|integer|>=:0',
        'topic_id|话题' => 'integer',
        'user_id|用户ID' => 'integer|require|>=:0',
        'images|图片' => 'array',
        'id|ID' => 'integer|require',
        'page|页码' => 'require|integer|>=:1',
        'order|排序' => 'in:new,hot',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];

    /**
     * 验证场景
     * @var array[]
     */
    protected $scene = [
        // 详情接口验证参数
        'read' => ['id'],
        // 查询话题下帖子列表验证场景
//        'index' => ['page', 'topic_id', 'order']
    ];

    /**
     * save 场景
     * @return Article
     */
    protected function sceneSave(): Article
    {
        return $this->only(['category_id', 'topic_id', 'content', 'images'])
            ->append('category_id', 'isCategoryExist');
    }

    /**
     * index 验证场景
     */
    public function sceneIndex(): Article
    {
        $url = request()->url();
        // 包含了 topic
        // 为了查询指定话题下的帖子列表
        if (str_contains($url, "topic")) {
            return $this->only(['page', 'topic_id', 'order']);
        }
        // 包含了 user
        // 为了查询指定用户下的帖子列表
        if (str_contains($url, "user")) {
            return $this->only(['page', 'user_id']);
        }

        // 默认查询指定分类下的帖子列表
        return $this->only(['page', 'category_id', 'order']);
    }

    /**
     * 自定义规则：验证分类是否存在
     * @param $value 前端传递的值
     * @param $rule 验证规则的名称
     * @param $data 完整的数据
     * @param $field 字段名称
     * @return string|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isCategoryExist($value, $rule = '', $data = '', $field = '')
    {
        $category = \app\model\Category::find($value);
        if (!$category) {
            return '分类不存在';
        }
        return true;
    }
}
