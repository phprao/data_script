<?php
	/*
	 *  @desc:   基本接口
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   error code before 20001 is basic api use;
	 *		     所有文件命名以小写，所有子类名以小写
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php');
	
	class basicloader {
		protected $m_dirs = array();    //dir list
		protected $m_base_path = '';    //root path
		
		public function __construct($base_path,$dirs = array()) {
			$this->set_base_path($base_path);
			
			if(!empty($dirs)) {
				$this->add_dirs($dirs);
			}
			
			spl_autoload_register(array($this, 'load'));
		}
		
		public function add_dirs($dirs) {
			if(!is_array($dirs)) {
				$dirs = array($dirs);
			}
			
			$this->m_dirs = array_merge($this->m_dirs, $dirs);
		}
		
		public function set_base_path($path) {
			$this->m_base_path = $path;
		}
		
		public function load_file($file_path) {
			require_once (substr($file_path, 0, 1) != '/' && substr($file_path, 1, 1) != ':')
            ? $this->m_base_path . DIRECTORY_SEPARATOR . $file_path : $file_path;
		}
		
		public function load($class_name) {
			if (class_exists($class_name, FALSE) || interface_exists($class_name, FALSE)) {
				return true;
			}
			
			if ($this->load_class(BASIC_API_ROOT, $class_name)) {
				return true;
			}
			//var_dump($this->m_dirs);
			foreach ($this->m_dirs as $dir) {
				if ($this->load_class($this->m_base_path . DIRECTORY_SEPARATOR . $dir, $class_name)) {
					return true;
				}
			}
			//var_dump($class_name);
			BASIC_SYS_LOG_WARNING('system','basicloader','%s','load class fail, class name ='.$class_name);
			return false;
		}
		
		public function check_class_exists($class_name) {
			if (class_exists($class_name, FALSE) || interface_exists($class_name, FALSE)) {
				return true;
			}
			return false;
		}
		
		protected function load_class($path,$class_name) {
			$require_file = $path . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';
			//var_dump($require_file);
			if (file_exists($require_file)) {
				require_once $require_file;
				return TRUE;
			}

			return FALSE;
		}
		
		public function __set($name, $value) {
			$this->set($name, $value);
		}

		public function __get($name) {
			return $this->get($name, NULL);
		}
	}
?>