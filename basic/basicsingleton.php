<?php
	/*
	 *  @desc:   基本单例
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class basicsingleton {
		protected static $m_instance = null;
		protected static $m_models = array();

		private function __construct() {
			//禁止new
		}
		private function __clone() {
			//禁止复制
		}

		public static function get_static_instance() {
			//var_dump($class_name);$class_name = __CLASS__
			if(!(static::$m_instance instanceof static)) {
				static::$m_instance = new static(); 
				//var_dump(self::$m_instance);
			}
			return static::$m_instance;
		}

		public static function get_instance() {
			$class_name =  get_called_class();
	        if( !isset( self::$m_models[$class_name] ) ){
	            self::$m_models[$class_name] = new $class_name();
	        }
	        return self::$m_models[$class_name];
		}
	}
?>