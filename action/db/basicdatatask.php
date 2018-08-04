<?php
	/*
	 *  @desc:   mysql任务
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class basicdatatask extends basictaskimpl {
		protected $m_other = '';
		protected $m_where_list = null;
		protected static $m_limit_model_value = 'limit 1';
		protected static $m_limit_list_value = 'limit 100000';
		protected static $m_limit_all_value = ' ';
		public static $WHERE_TYPE_IN = 1;
		public static $WHERE_TYPE_EQUAL = 2;
		public static $WHERE_TYPE_UN_EQUAL = 3;
		public static $WHERE_TYPE_LE = 4; //<
		public static $WHERE_TYPE_LT = 5; //>
		public static $WHERE_TYPE_ELE = 6; //<=
		public static $WHERE_TYPE_ELT = 7; //>=
		public static $WHERE_TYPE_LIKE = 8; //like
		public static $WHERE_TYPE_NOT_IN = 9; //not in
		
		public function __construct() {
			parent::__construct();
			$this->m_where_list = array();
		}
		
		public function set_other($other) {
			if(is_null($other) || is_array($other)) {
				return false;
			}

			$this->m_other = $other;
			return true;
		}

		public function append_where_list($where,$type) {
			if(is_null($where)) {
				return false;
			}

			if(!is_array($where)) {
				return false;
			}
			$data = $this->filter_keys_where($where);
			$item = array();
			$item['where_data'] = $data;
			$item['where_type'] = $type;

			//$this->m_where_list = array_merge($this->m_where_list,$item);
			array_push($this->m_where_list,$item);
			return true;
		}

		public function set_other_all_value() {
			$this->m_other = basicdatatask::$m_limit_all_value;
		}

		public function on_data_task(basicmysql $db,basicmodel $model = null,$param,$default) {
			if('select' == $this->m_action) {
				return $this->find_model($db,$model,$default);
			}else if('select_list' == $this->m_action) {
				return $this->find_list($db,$model,$default);
			}else if('select_list_in' == $this->m_action) {
				return $this->find_list_in($db,$model,$param,$default);
			}else if('insert' == $this->m_action) {
				return $this->create_model($db,$model);
			}else if('insert_fields' == $this->m_action) {
				return $this->create_fields($db,$model);
			}else if('delete' == $this->m_action) {
				return $this->delete_model($db,$model);
			}else if('delete_list' == $this->m_action) {
				return $this->delete_list($db);
			}else if('delete_list_in' == $this->m_action) {
				return $this->delete_list_in($db);
			}else if('update' == $this->m_action) {
				return $this->update_model($db,$model,$param);
			}else if('update_fields' == $this->m_action) {
				return $this->update_fields($db,$model,$param);
			}else if('select_all' == $this->m_action) {
				return $this->find_all_model($db,$model,$default);
			}else if('select_page' == $this->m_action) {
				return $this->find_model_on_page($db,$model,$default,$param);
			}else if('select_page_by_primary_key'== $this->m_action) {
				return $this->find_model_on_page($db,$model,$default,$param,true);
			}else if('select_count' == $this->m_action) {
				return $this->select_count($db,$model,$default);
			}else if('delete_all' == $this->m_action){
                return $this->delete_all($db);
            }
			return $default;
		}

		//==============以下是对mode 基本操作,如不足请在子类扩展===============
		protected function build_other_value($is_model) {
			if($this->m_other != '') {
				return true;
			}
			if($is_model) {
				$this->m_other = basicdatatask::$m_limit_model_value;
				return true;
			}

			if(empty($this->m_where)) {
				$this->m_other = basicdatatask::$m_limit_list_value;
			}else {
				$this->m_other = basicdatatask::$m_limit_all_value;
			}
			return true;
		}

		protected function find_model(basicmysql $db,basicmodel $model =null,$default) {

			if(is_null($db) || empty($this->m_where)) {
				return $default;
			}
			
			$this->build_other_value(true);

			$result = $db->select($this->m_table_name,'*',$this->m_where,$this->m_other);
			//var_dump($result);
			if(is_null($result) || 0 == count($result)) {
				return $default;
			}
			$data = $result[0];
			$this->m_data = $this->parse_model($model,$data);
			return $this->m_data;
		}

		protected function find_list(basicmysql $db,basicmodel $model =null,$default) {
			if(is_null($db)) {
				return $default;
			}

			$this->build_other_value(false);

			$result = $db->select($this->m_table_name,'*',$this->m_where,$this->m_other);
			//var_dump($result);
			if(is_null($result) || 0 == count($result)) {
				return $default;
			}
			$list = array();
			$use_primary_keys = false;
			foreach ($result as $key => $data) {
				$item = $this->parse_model($model,$data);
				$keys = $item->get($this->m_primary_keys,0);
				if(!is_null($use_primary_keys) && true == $use_primary_keys) {
					$list[$keys] = $item;
				}else {
					array_push($list, $item);
				}
			}
			return $list;
		}

		protected function build_data_in_param_value($where) {
			if(is_null($where)) {
				return '';
			}
			$in_param = '';
			foreach ($where as $key => $value) {
				if(is_array($value)) {
					$data_param = '';
					foreach ($value as $fields => $data) {
						$data_param = $data_param.' '.$data.',';
					}
					$data_param = substr($data_param, 0, strlen($data_param) - 1);
					$in_param = $in_param . $key.' in ('.$data_param.')';
				}else {
					$in_param = $in_param . $key.' in ('.$value.')';
				}
				$in_param = $in_param . ' AND ';
			}
			$in_param = substr($in_param, 0, strlen($in_param) - 5);
			return $in_param;
		}

		protected function build_data_not_in_param_value($where) {
			if(is_null($where)) {
				return '';
			}
			$in_param = '';
			foreach ($where as $key => $value) {
				if(is_array($value)) {
					$data_param = '';
					foreach ($value as $fields => $data) {
						$data_param = $data_param.' '.$data.',';
					}
					$data_param = substr($data_param, 0, strlen($data_param) - 1);
					$in_param = $in_param . $key.' not in ('.$data_param.')';
				}else {
					$in_param = $in_param . $key.' not in ('.$value.')';
				}
				$in_param = $in_param . ' AND ';
			}
			$in_param = substr($in_param, 0, strlen($in_param) - 5);
			return $in_param;
		}

		protected function build_data_equal_param_value($where) {
			if(is_null($where)) {
				return '';
			}
			
       		$in_param = '';
			foreach ($where as $key => $value) {
				$in_param = $in_param . $key.' = '.$value.' AND ';
			}
			$in_param = substr($in_param, 0, strlen($in_param) - 5);
			return $in_param;
		}

		protected function build_data_un_equal_param_value($where) {
			if(is_null($where)) {
				return '';
			}
			
       		$in_param = '';
			foreach ($where as $key => $value) {
				$in_param = $in_param . $key.' != '.$value.' AND ';
			}
			$in_param = substr($in_param, 0, strlen($in_param) - 5);
			return $in_param;
		}

		protected function build_data_le_param_value($where) {
			if(is_null($where)) {
				return '';
			}
			
       		$in_param = '';
			foreach ($where as $key => $value) {
				$in_param = $in_param . $key.' < '.$value.' AND ';
			}
			$in_param = substr($in_param, 0, strlen($in_param) - 5);
			return $in_param;
		}

		protected function build_data_ele_param_value($where) {
			if(is_null($where)) {
				return '';
			}
			
       		$in_param = '';
			foreach ($where as $key => $value) {
				$in_param = $in_param . $key.' <= '.$value.' AND ';
			}
			$in_param = substr($in_param, 0, strlen($in_param) - 5);
			return $in_param;
		}
		protected function build_data_lt_param_value($where) {
			if(is_null($where)) {
				return '';
			}
			
       		$in_param = '';
			foreach ($where as $key => $value) {
				$in_param = $in_param . $key.' > '.$value.' AND ';
			}
			$in_param = substr($in_param, 0, strlen($in_param) - 5);
			return $in_param;
		}
		protected function build_data_elt_param_value($where) {
			if(is_null($where)) {
				return '';
			}
			
       		$in_param = '';
			foreach ($where as $key => $value) {
				$in_param = $in_param . $key.' >= '.$value.' AND ';
			}
			$in_param = substr($in_param, 0, strlen($in_param) - 5);
			return $in_param;
		}

		protected function build_data_like_param_value($where) {
			if(is_null($where)) {
				return '';
			}
			
       		$in_param = '';
			foreach ($where as $key => $value) {
				$in_param = $in_param . $key.' like '.$value.' AND ';
			}
			$in_param = substr($in_param, 0, strlen($in_param) - 5);
			return $in_param;
		}

		protected function find_list_in(basicmysql $db,basicmodel $model =null,$param = null, $default = null) {
			if(is_null($db)) {
				return $default;
			}
			$this->build_other_value(false);
			$sql = 'select * from '.$this->m_table_name. ' '. $this->m_other;
			$in_param = '';
			do
			{
				if(!empty($this->m_where_list)) {
					$data_param = '';
					foreach ($this->m_where_list as $key => $value) {
						$type =  $value['where_type'];
						$where = $value['where_data'];
						switch ($type) {
							case basicdatatask::$WHERE_TYPE_IN:
								$data_param = $this->build_data_in_param_value($where);
								break;
							case basicdatatask::$WHERE_TYPE_NOT_IN:
								$data_param = $this->build_data_not_in_param_value($where);
								break;
							case basicdatatask::$WHERE_TYPE_EQUAL:
								$data_param = $this->build_data_equal_param_value($where);
								break;
							case basicdatatask::$WHERE_TYPE_UN_EQUAL:
								$data_param = $this->build_data_un_equal_param_value($where);
								break;
							case basicdatatask::$WHERE_TYPE_LE:
								$data_param = $this->build_data_le_param_value($where);
								break;
							case basicdatatask::$WHERE_TYPE_ELE:
								$data_param = $this->build_data_ele_param_value($where);
								break;
							case basicdatatask::$WHERE_TYPE_LT:
								$data_param = $this->build_data_lt_param_value($where);
								break;
							case basicdatatask::$WHERE_TYPE_ELT:
								$data_param = $this->build_data_elt_param_value($where);
								break;
							case basicdatatask::$WHERE_TYPE_LIKE:
								$data_param = $this->build_data_like_param_value($where);
								break;
							default:
								break;
						}
						$in_param = $in_param . ' ' . $data_param .' AND ';
					}
					$in_param = substr($in_param, 0, strlen($in_param) - 5);
					break;
				}
				if(!empty($this->m_where)) {
					/*foreach ($this->m_where as $key => $value) {
						if(is_array($value)) {
							$data_param = '';
							foreach ($value as $fields => $data) {
								$data_param = $data_param.' '.$data.',';
							}
							$data_param = substr($data_param, 0, strlen($data_param) - 1);
							$in_param = $in_param . $key.' in ('.$data_param.')';
						}else {
							$in_param = $in_param . $key.' in ('.$value.')';
						}
						$in_param = $in_param . ' AND ';
					}
					$in_param = substr($in_param, 0, strlen($in_param) - 5);
					*/
					$in_param = $this->build_data_in_param_value($this->m_where);
					break;
				}
				if(is_null($param)) {
					break;
				}
				if(is_string($param)) {
					$in_param = $param;
					break;
				}
				if(is_array($param)) {
					$in_param = '';
					foreach ($param as $key => $value) {
						if(is_array($value)) {
							$data_param = '';
							foreach ($value as $fileds => $data) {
								$data_param = $data_param.' '.$data.',';
							}
							$data_param = substr($data_param, 0, strlen($data_param) - 1);
							$in_param = $in_param . $key.' in ('.$data_param.')';
						}else {
							$in_param = $in_param . $key.' in ('.$value.')';
						}
						$in_param = $in_param . ' AND ';
					}
					$in_param = substr($in_param, 0, strlen($in_param) - 5);
				}
			}while(false);

			if('' == $in_param) {
				$this->m_other = 'limit 1';
			}

			$sql = 'select * from '.$this->m_table_name. ' where '. $in_param . ' '. $this->m_other;

			$result = $db->find($sql);
			//var_dump($result);
			if(is_null($result) || 0 == count($result)) {
				return $default;
			}
			$list = array();
			$use_primary_keys = false;
			foreach ($result as $key => $data) {
				$item = $this->parse_model($model,$data);
				$keys = $item->get($this->m_primary_keys,0);
				if(!is_null($use_primary_keys) && true == $use_primary_keys) {
					$list[$keys] = $item;
				}else {
					array_push($list, $item);
				}
			}
			return $list;
		}

		protected function find_all_model(basicmysql $db,basicmodel $model =null,$default) {
			if(is_null($db)) {
				return $default;
			}

			$this->build_other_value(false);

			$sql = 'select * from '.$this->m_table_name. ' '. $this->m_other;
			//if('limit 1' == $this->m_other) {
			//	$sql = 'select * from '.$this->m_table_name;
			//}
			$result = $db->find($sql);
			if(is_null($result) || 0 == count($result)) {
				return $default;
			}

			$list = array();
			foreach ($result as $key => $data) {
				$item = $this->parse_model($model,$data);
				//$keys = $item->get($this->m_primary_keys,0);
				array_push($list, $item );
			}

			return $list;
		}

		protected function find_model_ext(basicmysql $db,basicmodel $model =null,$default) {

			if(is_null($db) || is_null($model)) {
				return $default;
			}

			$this->build_other_value(true);

			$where = array($this->m_primary_keys => $model->get($this->m_primary_keys,0));
			$result = $db->select($this->m_table_name,'*',$where,$this->m_other);
			//var_dump($result);
			if(is_null($result) || 0 == count($result)) {
				return $default;
			}
			$data = $result[0];
			$this->m_data = $this->parse_model($model,$data);
			return $this->m_data;
		}

		protected function create_model(basicmysql $db,basicmodel $model) {
			if(is_null($db) || is_null($model)) {
				return 0;
			}

			//$model->delete($this->m_primary_keys);
			$data = $this->format_model($model);
			//var_dump($data);
			$result = $db->insert($this->m_table_name,$data);
			return $result;
		}

		protected function create_fields(basicmysql $db,basicmodel $model) {
			if(is_null($db) || is_null($model)) {
				return 0;
			}

			$model->delete($this->m_primary_keys);
			$data = $this->format_model_fields($model);
			//var_dump($data);
			$result = $db->insert($this->m_table_name,$data);
			return $result;
		}

		protected function delete_model(basicmysql $db,basicmodel $model) {
			if(is_null($db) || is_null($model)) {
				return 0;
			}

			$this->build_other_value(true);

			$sql = 'delete from '.$this->m_table_name.' where '.$this->m_fields[$this->m_primary_keys] . '= ' .$model->get($this->m_primary_keys,0). ' '.$this->m_other;
			$result = $db->query($sql);
			return $result;
		}

		protected function delete_list(basicmysql $db) {
			if(is_null($db) ) {
				return 0;
			}

			if(!empty($this->m_where)) {
				$this->m_other = '';
			}
			$result = $db->delete($this->m_table_name,$this->m_where,$this->m_other);
			return $result;
		}
        protected function delete_all(basicmysql $db) {
            if(is_null($db) ) {
                return 0;
            }

            $this->build_other_value(false);
            $sql = 'DELETE FROM '.$this->m_table_name;
            $result = $db->query($sql);
            return $result;
        }

        protected function delete_list_in(basicmysql $db) {
        	if(is_null($db) ) {
                return 0;
            }

			if(!empty($this->m_where_list)) {
				$in_param = '';
				$data_param = '';
				foreach ($this->m_where_list as $key => $value) {
					$type =  $value['where_type'];
					$where = $value['where_data'];
					switch ($type) {
						case basicdatatask::$WHERE_TYPE_IN:
							$data_param = $this->build_data_in_param_value($where);
							break;
						case basicdatatask::$WHERE_TYPE_NOT_IN:
							$data_param = $this->build_data_not_in_param_value($where);
							break;
						case basicdatatask::$WHERE_TYPE_EQUAL:
							$data_param = $this->build_data_equal_param_value($where);
							break;
						case basicdatatask::$WHERE_TYPE_UN_EQUAL:
							$data_param = $this->build_data_un_equal_param_value($where);
							break;
						case basicdatatask::$WHERE_TYPE_LE:
							$data_param = $this->build_data_le_param_value($where);
							break;
						case basicdatatask::$WHERE_TYPE_ELE:
							$data_param = $this->build_data_ele_param_value($where);
							break;
						case basicdatatask::$WHERE_TYPE_LT:
							$data_param = $this->build_data_lt_param_value($where);
							break;
						case basicdatatask::$WHERE_TYPE_ELT:
							$data_param = $this->build_data_elt_param_value($where);
							break;
						case basicdatatask::$WHERE_TYPE_LIKE:
							$data_param = $this->build_data_like_param_value($where);
							break;
						default:
							break;
					}
					$in_param = $in_param . ' ' . $data_param .' AND ';
				}
				$in_param = substr($in_param, 0, strlen($in_param) - 5);
				
				if('' == $in_param) {
					$this->m_other = 'limit 1';
				}
				$sql = 'DELETE FROM '.$this->m_table_name . ' where '. $in_param . ' '. $this->m_other;
	            $result = $db->query($sql);
	            return $result;
			}else {
				return $this->delete_list($db);
			}

			return false;
        }

		protected function build_update_where(basicmysql $db,basicmodel $model,$param = null) {
			if(is_null($db) || is_null($model)) {
				return array();
			}

			$where_list = array($this->m_primary_keys => $model->get($this->m_primary_keys,0));

			if(!is_null($this->m_where) && !empty($this->m_where)) {
				$where_list = $this->m_where;
			}

			if(!is_null($param) && is_array($param)) {
				$where_list = $param;
			}

			return $where_list;
		}

		protected function update_fields(basicmysql $db,basicmodel $model,$param = null) {
			if(is_null($db) || is_null($model)) {
				return 0;
			}

			$where_list = $this->build_update_where($db,$model,$param);

			$model->delete($this->m_primary_keys);
			$data = $this->format_model_fields($model);

			$result = $db->update($this->m_table_name,$data,$where_list);
			return $result;
		}

		protected function update_model(basicmysql $db,basicmodel $model,$param = null) {
			if(is_null($db) || is_null($model)) {
				return 0;
			}

			$where_list = $this->build_update_where($db,$model,$param);
			$model->delete($this->m_primary_keys);
			$data = $this->format_model($model);

			$result = $db->update($this->m_table_name,$data,$where_list);
			return $result;
		}

		protected function select(basicmysql $db,basicmodel $model =null,$default) {
			if(is_null($db)) {
				return $default;
			}
			
			$this->build_other_value(false);

			$sql = 'select * from '.$this->m_table_name. ' '. $this->m_other;
			$result = $db->find($sql);
			if(is_null($result) || 0 == count($result)) {
				return $default;
			}

			$list = array();
			foreach ($result as $key => $data) {
				$item = $this->parse_model($model,$data);
				$keys = $item->get($this->m_primary_keys,0);
				$list[$keys] = $item;
			}

			return $list;
		}

		protected function find_model_on_page(basicmysql $db,basicmodel $model =null,$default,$param,bool $use_primary_keys = null) {
			if(is_null($db) ) {
				return $default;
			}
			$result = null;
			if(empty($this->m_other)) {
				if(!is_null($param) && isset($param['index']) && isset($param['page_size'])) {
					$index = (int)$param['index'];
					$pag_size = (int)$param['page_size'];
					$this->m_other = 'limit'. ' '.$index.','.$pag_size;
				}else {
					$this->build_other_value(false);
				}
			}

			if(empty($this->m_where)) {
				$sql = 'select * from '.$this->m_table_name. ' '.$this->m_other;//$sql = 'select * from '.$this->m_table_name. ' limit'. ' '.$index.','.$pag_size;
				$result = $db->find($sql);
			}else {
				//$this->m_other = ' limit'. ' '.$index.','.$pag_size;
				$result = $db->select($this->m_table_name,'*',$this->m_where,$this->m_other);	

			}
			
			if(is_null($result) || 0 == count($result)) {
				return $default;
			}

			$list = array();

			foreach ($result as $key => $data) {
				$item = $this->parse_model($model,$data);
				$keys = $item->get($this->m_primary_keys,0);
				if(!is_null($use_primary_keys) && true == $use_primary_keys) {
					$list[$keys] = $item;
				}else {
					array_push($list, $item);
				}
				
			}
			//var_dump($result);
			return $list;
		}

		protected function select_count(basicmysql $db,basicmodel $model =null,$default) {
			if(is_null($db)) {
				return $default;
			}

			$result = null;
			if(empty($this->m_where)) {
				$sql = 'select count(*) as total from '.$this->m_table_name. ' '. $this->m_other;
				$result = $db->find($sql);
			}else {
				$result = $db->select($this->m_table_name,'count(*) as total',$this->m_where,$this->m_other);
			}
			
			//var_dump($result);
			if(is_null($result) || 0 == count($result)) {
				return $default;
			}
			$data = $result[0];
			$this->m_data = $data;
			return $this->m_data['total'];
		}
	}
?>