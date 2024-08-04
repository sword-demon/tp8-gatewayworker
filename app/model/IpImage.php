<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class IpImage extends Model
{
    // 更新前
    public static function onAfterUpdate($model)
    {
        if ($model->getData("status") == 0) {
            $filePath = app()->getRootPath() . 'public/storage/' . $model->getData("url");
            $filePath = str_replace("\\", "/", $filePath);
            // 文件是否存在
            if (file_exists($filePath)) {
                // 删除文件  
                if (!unlink($filePath)) {
                    trace("文件删除失败：" . $filePath, "error");
                } else {
                    trace("文件删除成功：" . $filePath, "success");
                }
            }
        }
    }

    // 关联用户
    public function user()
    {
        return $this->belongsTo("User", "user_id")->bind([
            "name",
            "avatar",
            "user_status" => "status"
        ]);
    }

    // 获取完整图片路径
    public function getUrlAttr($value)
    {
        return getUploadPath($value);
    }
}
