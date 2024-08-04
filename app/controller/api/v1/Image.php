<?php
declare (strict_types=1);

namespace app\controller\api\v1;

use ImageUploader\ImageUploader;
use think\Request;
use app\controller\api\Base;

class Image extends Base
{
    public function upload(Request $request)
    {
        // 获取上传图片
        $file = $request->file('image');
        if (!$file) {
            ApiException('请选择上传图片');
        }
        // 实例化上传类并调用方法上传图片
        $uploader = new ImageUploader();
        try {
            $savedPath = $uploader->uploadAndCompress($file);
            return apiSuccess('上传成功', getUploadPath($savedPath));
        } catch (\Exception $e) {
            ApiException($e->getMessage());
        }
    }
}
