<?php
	/*
	 *  @desc:   多级缓存接口
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class basicmulticache implements basiccache {
		protected $m_caches = array();
		
		public function add_cache(basiccache $cache) {
			$this->m_caches[] = $cache;
		}
		
		public function set($key,$value,$expire = 600) {
			foreach ($this->m_caches as $cache) {
				$cache->set($key, $value, $expire);
			}
		}
		
		public function get($key) {
			 foreach ($this->m_caches as $cache) {
				$value = $cache->get($key);
				if (!is_null($value)) {
					return $value;
				}
			}
			return null;
		}
		
		public function delete($key) {
			foreach ($this->m_caches as $cache) {
				$cache->delete($key);
			}
		}
	}
?>