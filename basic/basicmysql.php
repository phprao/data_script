<?php
    //include_once(path_format('config/config.php'));
/*
 *  @desc:   基本mysql接口
 *  @author: xxm
 *  @email:  237886849@qq.com
 *  @note:   所有文件命名以小写，所有子类名以小写
 *		     
 */
class basicmysql implements basicevent{
    private $conn = null;
	private $tag = "basicmysql";
    private $debug = false;
    public function __construct() {
		
    }
 	public function __destruct() {
        $this->close();
    }

    public function set_debug($debug_flags) {
    	if(is_null($debug_flags)) {
    		return false;
    	}
    	$this->debug = $debug_flags;
    	return true;
    }

    public function connect($c) {
        if(!isset($c['port'])){
            $c['port'] = '3306';
        }
        $server = $c['host'] . ':' . $c['port'];
        $this->conn = mysqli_connect($server, $c['username'], $c['password']);
     
		if ($this->conn) {
			$ret = mysqli_select_db($this->conn,$c['dbname']);
			if (!$ret) {
				BASIC_SYS_LOG_ERROR('data',$this->tag,'%s','Can not use db : '. mysqli_connect_error());
				return false;
			}
			if ($c['charset']) {
				return mysqli_set_charset($this->conn,$c['charset']);
			}
			return true;
		}
		BASIC_SYS_LOG_ERROR('data',$this->tag,'%s','Could not connect :  '. mysqli_connect_error());
		return false;
    }
	
	public function close() {
		if(is_null($this->conn)) {
			return false;
		}
		$ret = mysqli_close($this->conn);
		$this->conn = null;
		return $ret;
	}
	
    public function find($sql) {
    	if($this->check_invalid()) return false;
        $data = array();
		$this->printf_info($sql);
		$tick = new basicticktimer($this,$sql);
        $result = mysqli_query($this->conn,$sql);
        $this->printf_mysql_error($sql);
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$data[] = $row;
			}
		}
        return $data;
    }
    
    public function select($table, $columns, $where, $other = '') {
    	if($this->check_invalid()) return false;
        $cond = '';
        foreach ($where as $k => $v) {
			$value = mysqli_real_escape_string($this->conn,$v);
            $cond .= "`$k` = '$value' AND ";
        }
		$cond = substr($cond, 0, strlen($cond) - 5);
        $sql = "SELECT $columns FROM `{$table}` WHERE $cond $other";
    	$data = array();
		$this->printf_info($sql);
		//$st = time();
		$tick = new basicticktimer($this,$sql);
    	$result = mysqli_query($this->conn,$sql);//MYSQLI_USE_RESULT
		//$en = time()-$st;
		//Config::$logger->debug($this->tag, $en);
		$this->printf_mysql_error($sql);
    	if ($result) {
    		while ($row = mysqli_fetch_assoc($result)) {
    			$data[] = $row;
    		}
    	}
    	return $data;
    }

    public function delete($table,$where, $other = '') {
    	if($this->check_invalid()) return false;
    	$cond = '';
        foreach ($where as $k => $v) {
			$value = mysqli_real_escape_string($this->conn,$v);
            $cond .= "`$k` = '$value' AND ";
        }
		$cond = substr($cond, 0, strlen($cond) - 5);
        $sql = "DELETE FROM `{$table}` WHERE $cond $other";
		$this->printf_info($sql);
		$tick = new basicticktimer($this,$sql);
    	$result = mysqli_query($this->conn,$sql);
    	$this->printf_mysql_error($sql);
		return $result;
    }
	
	public function insert($table, $row) {
		if($this->check_invalid()) return false;
        $stat = '';
        foreach ($row as $k => $v) {
			$value = mysqli_real_escape_string($this->conn,$v);
            $stat .= "`$k` = '$value',";
        }
		
        $stat = substr($stat, 0, strlen($stat) - 1);
        $sql = "INSERT INTO `{$table}` SET $stat";
		$this->printf_info($sql);
		$tick = new basicticktimer($this,$sql);
        mysqli_query($this->conn,$sql);
        $result = mysqli_insert_id($this->conn);
        $this->printf_mysql_error($sql);
		return $result;
	}
	public function update($table, $row, $where) {
		if($this->check_invalid()) return false;
        $stat = '';
        foreach ($row as $k => $v) {
			$value = mysqli_real_escape_string($this->conn,$v);
            $stat .= "`$k` = '$value',";
        }
        $stat = substr($stat, 0, strlen($stat) - 1);
		
        $cond = '';
        foreach ($where as $k => $v) {
			$value = mysqli_real_escape_string($this->conn,$v);
            $cond .= "`$k` = '$value' AND ";
        }
		$cond = substr($cond, 0, strlen($cond) - 5);
		
        $sql = "UPDATE `{$table}` SET $stat where $cond";
		$this->printf_info($sql);
		$tick = new basicticktimer($this,$sql);
        $result = mysqli_query($this->conn,$sql);
        $this->printf_mysql_error($sql);
		return $result;
	}
	
	public function insert_or_update($table, $row) {
		if($this->check_invalid()) return false;
        $stat = '';
        foreach ($row as $k => $v) {
			$value = mysqli_real_escape_string($this->conn,$v);
            $stat .= "`$k` = '$value',";
        }

        $stat = substr($stat, 0, strlen($stat) - 1);
        $sql = "INSERT INTO `{$table}` SET $stat ON DUPLICATE KEY UPDATE $stat";
		$this->printf_info($sql);
		$tick = new basicticktimer($this,$sql);
        $result = mysqli_query($this->conn,$sql);
        $this->printf_mysql_error($sql);
		return $result;
	}
	
	public function query($sql) {
		if($this->check_invalid()) return false;
		$this->printf_info($sql);
		$tick = new basicticktimer($this,$sql);
		$result = mysqli_query($this->conn,$sql);
		$this->printf_mysql_error($sql);
		return $result;
	}
	
	public function selectdb($db){
		if($this->check_invalid()) return false;
		$ret = mysqli_select_db($this->conn,$db);
		$this->printf_info('choose db error:'.$db);
	}
	

	protected function printf_info($msg) {
		if(!$this->debug) return;
		BASIC_SYS_LOG_DEBUG('data',$this->tag,'%s', $msg);
	}

	protected function printf_mysql_error($msg) {
		if(is_null($this->conn)) return;
		$mysql_code = mysqli_errno($this->conn); 
		if(0 == $mysql_code) return;

		if($mysql_code > 0 ) {
			$msg = $msg . ' (mysql_error =' . mysqli_error($this->conn) . ')';
		}

		BASIC_SYS_LOG_ERROR('data',$this->tag,'%s', $msg);
	}

	protected function check_invalid() {
		if(is_null($this->conn)) return true;
		return false;
	}

	public function on_event($action, $object, $in_buf, $out_buf){
		$msg = $in_buf;
		if(!is_null($object) && is_string($object)) {
			$msg = $in_buf . ' ' . $object;
		}
		BASIC_SYS_LOG_WARNING('data',$this->tag,'%s', $msg);
	}

}
 
	  
?>
