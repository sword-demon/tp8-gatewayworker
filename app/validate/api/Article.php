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
        'images|图片' => 'array',
        'id|ID' => 'integer|require'
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
        'read' => ['id']
    ];

    /**
     * save 场景
     * @return Article
     */
    protected function sceneSave()
    {
        return $this->only(['category_id', 'topic_id', 'content', 'images'])
            ->append('category_id', 'isCategoryExist');
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
