<?php
	/*
	 *  @desc:   数据模型实现
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class basicmodelimpl implements basicmodel {
		protected $m_data = null;

		public function __construct() {
			$this->m_data = array();
		}

		public function get($id,$default) {
			if(is_null($id) || is_null($this->m_data) || !isset($this->m_data[$id])) {
				return $default;
			}
			return $this->m_data[$id];
		}
		
		public function set($id,$data) {
			if(is_null($id) || is_null($this->m_data) || !isset($this->m_data[$id]) || is_null($data)) {
				return false;
			}
			$this->m_data[$id] = $data;
			return true;
		}

		public function insert($id,$data) {
			if(is_null($id) || is_null($this->m_data)) {
				return false;
			}
			$this->m_data[$id] = $data;
			return true;
		}


		public function update($id,$data) {
			if(is_null($id) || is_null($this->m_data)) {
				return false;
			}
			$this->m_data[$id] = $data;
			return true;
		}

		
		public function delete($id) {
			if(is_null($id) || is_null($this->m_data)) {
				return false;
			}
			unset($this->m_data[$id]);
			return true;
		}

		public function copy(basicmodel $model) {
			if(is_null($this->m_data)) {
				return false;
			}

			foreach ($this->m_data as $key => $value) {
				$model->insert($key,$value);
			}
			return true;
		}


		public function copy_to_other_model(basicdatamodel $model,$map_fileds,$def_value) {
			if(!is_array($map_fileds) || !is_array($def_value))
			foreach ($map_fileds as $key => $fields) {
				$default = $def_value[$key];
				if(is_null($default)) {
					$default = 0;
				}
				$value = $this->get($key,$default);
				$two->insert($fields,$value);
			}

			return true;
		}

		//==================arrayaccess begin ======
		public function offsetSet($offset, $value) {
			$this->set($offset, $value);
		}

		public function offsetGet($offset) {
			return $this->get($offset, null);
		}

		public function offsetUnset($offset) {
			$this->delete($offset);
		}

		public function offsetExists($offset) {
			if(is_null($offset) || is_null($this->m_data)) {
				return false;
			}
			return isset($this->data[$offset]);
		}
		//==================arrayaccess end ======
	}
?>