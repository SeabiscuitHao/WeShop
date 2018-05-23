<?php
namespace app\lib\exception;
use think\exception\Handle;
use think\Exception;
use think\Request;
class ExceptionHandler extends Handle {
	public function render(\Exception $e) {
	    if($e instanceof BaseException){
	        //如果是自定义异常，证明是第一种情况，否则为第二种
	        $this->code 	 = $e->code;
	        $this->msg 	 	 = $e->msg;
	        $this->errorCode = $e->errorCode;
	    } else {
	    	if (config('app_debug')) {
	    		return parent::render($e);
	    	} else {
		    	$this->code 	 = 500;
		    	$this->msg  	 = '服务器内部错误';
		    	$this->errorCode = 999;	
	    	}
	    }
	    $request = Request::instance();
	    $result = [
	        'msg'    		 => $this->msg,
	        'error_code'     => $this->errorCode,
	        'request_url'    => $request->url()
	    ];
	    return json($result, $this->code);
	}
	private function recordErrorLog(\Exception $e) {
		Log::init([
			//自动生成日志文件
			'type'	=> 'File',
			'path'	=> LOG_PATH,
			//低于error级别的异常不会生成相关的日志
			'level' => ['error'],
		]);
		Log::record($e->getMessage(),'error');
	}
}