<?php
declare (strict_types=1);

namespace app\controller\api;

use think\facade\Log;
use think\Request;

class Base
{
    // 是否开启自动验证
    protected bool $autoValidateCheck = true;

    // 不需要验证的方法
    protected array $excludeValidateCheck = [];

    // 验证场景配置
    protected array $autoValidateScenes = [];

    // 当前控制器信息
    protected array $controllerInfo = [];

    // 构造函数
    public function __construct(Request $request)
    {
        // 初始化控制器相关信息
        $this->initControllerInfo($request);
        // 自动验证
        $this->autoValidateAction();
    }

    private function initControllerInfo(Request $request): void
    {
        $str = $request->controller();
        // 获取真实控制器名称
        $this->controllerInfo = [
            'controller_name' => class_basename($this),
            'controller_path' => str_replace('.', '\\', $str),
            'action' => $request->action()
        ];
    }

    private function autoValidateAction(): void
    {
        // app 地址
//        define('__APP_PATH__', __DIR__ . '/../../');
//        Log::info(__APP_PATH__);
//        Log::info(app_path());
        $action = $this->controllerInfo['action'];
        // 判断是否需要验证
        if ($this->autoValidateCheck && !in_array($action, $this->excludeValidateCheck)) {
            // 获取验证实例
            $validateName = file_exists(app_path() . '/validate/' . $this->controllerInfo['controller_path'] . '.php')
                ? $this->controllerInfo['controller_path'] : $this->controllerInfo['controller_name'];
//            Log::info('validateName :' . $validateName);
//            Log::info('controllerInfo :' . json_encode($this->controllerInfo));
            // 实例化验证器
            $validate = app('app\validate\api\\' . $validateName);
            // 获取验证场景
            // 注意这里的以后场景都是对应的控制器方法，两者名称一定要一致
            $scene = $action;
            // 自定义去配置一些验证场景
            if (array_key_exists($action, $this->autoValidateScenes)) {
                $scene = $this->autoValidateScenes[$action];
            }
            // 开始验证
            $params = request()->param();
            if (!$validate->scene($scene)->check($params)) {
                // 抛出异常
                ApiException($validate->getError());
            }
        }
    }
}
