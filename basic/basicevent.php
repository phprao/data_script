<?php
	/*
	 *  @desc:   基本事件接口
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	interface basicevent {
		public function on_event($action, $object, $in_buf, $out_buf);
	}
?>