<?php
	header('content-type:text/html; charset=utf-8');
    /*$tcp = new basicsocket;
	$tcp->init_socket();
	$tcp->connect_socket("192.168.1.160",7002);
	$data = pack("CIIIIIIIc512c512",1,21,22,0,0,1,2,3,'123','bs1029384756');
	$data = "123";
	//$psw = 'bs1029384756';
	//tcp header(size:flag,key)
	$buf = pack("ICI",1053,0,0);
	//com header(version,main_id,sub_id,handle_code,check_code)
	$buf .= pack("CIIII",1,21,22,0,0);
	//data(task_id,user_id,action_id)
	$buf .= pack("III",1,10001,3);
	//data buf
	$buf .= pack("a512",$data);
	//task pws
	$buf .= pack("a512",$psw);
	$tcp->send_buf($buf);
	$tcp->uninit_socket();*/
    //
	/*$socket_data['user_id']=0;
	$socket_data['action_id']=1;
	$socket_data['msg']='';
	$socket->set_buf($socket_data);
	$socket->send_buf();
	unset($socket_data);*/
	
	class socket {
		protected		$m_socket = NULL;
		//private $ip="";
		//private $port = "";
	    public $buf=NULL;
		private $pws = "";
		
		function __construct($ip,$port,$pws) {
		   //
		   $this->buf  = pack("ICI",1057,0,0);
	       $this->buf .= pack("CIIII",2,21,22,0,0);
		   $this->pws = $pws;
		   $this->m_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		   if(NULL == $this->m_socket) {
				return false;
			}
			//
			$result = socket_connect($this->m_socket,$ip,$port);
			
	    }
		
		public function uninit_socket() {
			if($this->m_socket != NULL ) {
				socket_shutdown($this->m_socket);
				socket_close($this->m_socket);
				$this->m_socket = NULL;
			}
		}
		
		public function set_buf($data) {
           $task_id   = (int)(time().rand(100000,100000)); //任务id
		   $user_id   = (int)$data['user_id'];//0全部
		   $action_id = (int)$data['action_id'];//行为 1更新用户信息
		   $task_type = (int)$data['task_type'];//0只推送一次,不保证客户端收到,1是保证客户端收到
		   $msg       = $data['msg'];
		   //task_id,user_id,action_id
		   $this->buf .= pack("IIII",$task_id,$user_id,$action_id,$task_type);
	       $this->buf .= pack("a512",$msg);
	       $this->buf .= pack("a512",$this->pws);
			
		}
		public function send_buf() {
			if(NULL == $this->m_socket) {
				return false;
			}
			socket_write($this->m_socket, $this->buf);
			return true;
		}

		public function recv_buf($len) {
			if(NULL == $this->m_socket) {
				return NULL;
			}
			
			$buf = $buf = socket_read($this->m_socket, $len);
			return $buf;
		}
	}
	
	
?>