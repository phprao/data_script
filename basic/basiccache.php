<?php
	/*
	 *  @desc:   缓存接口
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	interface basiccache {
		
		public function set($key,$value,$expire = 600);
		
		public function get($key);
		
		public function delete($key);
	}
?>