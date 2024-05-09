<?php
namespace app;

use http\Params;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * 全局异常处理
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制

        // 调试模式下交给系统处理
        if (env('app_debug')) {
            return parent::render($request, $e);
        }

        // 参数验证错误
        if ($e instanceof  ValidateException) {
            return apiFail($e->getError(), 422);
        }

        // 请求异常
        if ($e instanceof HttpException || $request->isAjax()) {
            return apiFail($e->getMessage(), $e->getStatusCode());
        }

        // 模型不存在
        if ($e instanceof ModelNotFoundException) {
            return apiFail("模型不存在");
        }

        // 数据不存在
        if ($e instanceof DataNotFoundException) {
            return apiFail('数据不存在');
        }

        return apiFail('服务器错误');
    }
}
