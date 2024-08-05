<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Support extends Model
{
    /**
     * 获取器 生成的虚拟字段
     * @param $value
     * @param $data
     * @return string
     */
    public function getUserSupportActionAttr($value, $data)
    {
        return $data['type'] == 1 ? 'ding' : 'cai';
    }
}
