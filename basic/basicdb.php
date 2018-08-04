<?php
	/*
	 *  @desc:   DB接口
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	interface basicdb {
		public function process(basicdi $app, $action, basictask $task) ;
	}
?>