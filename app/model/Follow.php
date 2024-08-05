<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Follow extends Model
{
    /**
     * 获取我是否已关注
     * @param $value
     * @param $data
     * @return true
     */
    public function getCurrentFollowAttr($value, $data)
    {
        return true;
    }
}
