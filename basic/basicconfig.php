<?php
	class basicconfig {
		
		protected $m_config = null;
		protected $m_data = null;
		
		public function __construct($srv_type = null,$host_name = null) {
			$this->m_config = array();
			$this->m_data = array();

			if(is_null($srv_type )) {
				$srv_type = SRV_TYPE_RELEASE;
			}
			if(is_null($host_name)) {
				$host_name = $_SERVER['HTTP_HOST'];
			}

			$this->init_data($srv_type,$host_name);

			switch ($srv_type) {
				case SRV_TYPE_DEBUG:
					$this->init_debug_server($host_name);
					break;
				case SRV_TYPE_TEST:
					$this->init_test_server($host_name);
					break;
				case SRV_TYPE_RELEASE:
					$this->init_release_server($host_name);
					break;
				default:
					$this->init_debug_server($host_name);
					break;
			}
			$this->init_weixin_info($host_name);
			$this->init_project_data('push_info','push_info');
			//var_dump($this->m_config);exit;

            // 扩展配置  Add by zhanghui At 2018-04-08
            $this->init_extend_config($srv_type, $host_name);
		}
		
		public function set($key,$value) {
			if(is_null($this->m_config) || is_null($key)) {
				return false;
			}
			$this->m_config[$key] = $value;
			return true;
		}
		
		public function get($key,$default) {
			if(is_null($this->m_config) || is_null($key) || !isset($this->m_config[$key])) {
				return $default;
			}
			return $this->m_config[$key];
		}

		//内网
		protected function init_debug_server($host_name) {
			$data = array();
			$data['mysql_host'] = '192.168.1.210';
			$data['mysql_port'] = '3306';
			$data['mysql_root'] = 'root';
			$data['mysql_psw'] = '123456';
			$data['mysql_name'] = 'dc_u3d_king';
			$data['mysql_charset'] = 'utf8';

			$this->set('mysql',$data);

			$redis =  array();
			$redis['redis_host'] = '192.168.1.210';
			$redis['redis_port'] = 55001;
			$redis['redis_root'] = '';
			$redis['redis_psw'] = 'zyl12345!QWEASD901';
			$redis['redis_name'] = 0;
			$this->set('redis_user',$redis);

			$redis['redis_port'] = 55002;
			$this->set('redis_room',$redis);

			$redis['redis_port'] = 55003;
			$this->set('redis_game',$redis);
		}
		//外网
		protected function init_test_server($host_name) {
			switch ($host_name) {
				case 'jinshi.dcgames.cn':
					$data = array();
					$data['mysql_host'] = '10.135.161.207';
					$data['mysql_port'] = '3306';
					$data['mysql_root'] = 'root';
					$data['mysql_psw'] = '';
					$data['mysql_name'] = 'jinshi';
					$data['mysql_charset'] = 'utf8';

					$this->set('mysql',$data);

					$redis =  array();
					$redis['redis_host'] = '10.135.161.207';
					$redis['redis_port'] = 55001;
					$redis['redis_root'] = '';
					$redis['redis_psw'] = 'zyl12345!QWEASD901';
					$redis['redis_name'] = 0;
					$this->set('redis_user',$redis);

					$redis['redis_port'] = 55002;
					$this->set('redis_room',$redis);

					$redis['redis_port'] = 55003;
					$this->set('redis_game',$redis);
					break;
				case 'kingdom.dcgames.cn':
					$data = array();
					$data['mysql_host'] = '10.104.114.133';
					$data['mysql_port'] = '3306';
					$data['mysql_root'] = 'root';
					$data['mysql_psw'] = '';
					$data['mysql_name'] = 'kingdom';
					$data['mysql_charset'] = 'utf8';

					$this->set('mysql',$data);

					$redis =  array();
					$redis['redis_host'] = '10.104.114.133';
					$redis['redis_port'] = 55001;
					$redis['redis_root'] = '';
					$redis['redis_psw'] = 'zyl12345!QWEASD901';
					$redis['redis_name'] = 0;
					$this->set('redis_user',$redis);

					$redis['redis_port'] = 55002;
					$this->set('redis_room',$redis);

					$redis['redis_port'] = 55003;
					$this->set('redis_game',$redis);
					break;
				case 'xingyun.dcgames.cn':
					$data = array();
					$data['mysql_host'] = '10.104.152.180';
					$data['mysql_port'] = '3306';
					$data['mysql_root'] = 'root';
					$data['mysql_psw'] = '';
					$data['mysql_name'] = 'xingyun';
					$data['mysql_charset'] = 'utf8';

					$this->set('mysql',$data);

					$redis =  array();
					$redis['redis_host'] = '10.104.152.180';
					$redis['redis_port'] = 55001;
					$redis['redis_root'] = '';
					$redis['redis_psw'] = 'zyl12345!QWEASD901';
					$redis['redis_name'] = 0;
					$this->set('redis_user',$redis);

					$redis['redis_port'] = 55002;
					$this->set('redis_room',$redis);

					$redis['redis_port'] = 55003;
					$this->set('redis_game',$redis);
					break;
				default:
					$data = array();
					$data['mysql_host'] = '127.0.0.1';
					$data['mysql_port'] = '3306';
					$data['mysql_root'] = 'root';
					$data['mysql_psw'] = 'feloe#@GDe%^733=-1Ef1';
					$data['mysql_name'] = 'dc_u3d';
					$data['mysql_charset'] = 'utf8';

					$this->set('mysql',$data);

					$redis =  array();
					$redis['redis_host'] = '127.0.0.1';
					$redis['redis_port'] = 55001;
					$redis['redis_root'] = '';
					$redis['redis_psw'] = 'dcyx9861@$%^ewvhHVGtETD0t7rG';
					$redis['redis_name'] = 0;
					$this->set('redis_user',$redis);

					$redis['redis_port'] = 55002;
					$this->set('redis_room',$redis);

					$redis['redis_port'] = 55003;
					$this->set('redis_game',$redis);
					break;
			}
			
		}

		//正式
		protected function init_release_server($host_name) {
			switch ($host_name) {
				case 'jinshi.dcyouxi.com':
				case 'jstj.dcyouxi.com':
					$data = array();
					$data['mysql_host'] = '10.66.223.56';
					$data['mysql_port'] = '3306';
					$data['mysql_root'] = 'root';
					$data['mysql_psw'] = 'NG*&(&J4345yy39r';
					$data['mysql_name'] = 'jinshi';
					$data['mysql_charset'] = 'utf8';

					$this->set('mysql',$data);

					$redis =  array();
					$redis['redis_host'] = '10.66.165.28';
					$redis['redis_port'] = 6379;
					$redis['redis_root'] = '';
					$redis['redis_psw'] = 'crs-kd36r74d:Skjhh3432skjd686';
					$redis['redis_name'] = 0;
					$this->set('redis_user',$redis);
					
					$redis['redis_host'] = '10.66.167.243';
					$redis['redis_port'] = 6379;
					$redis['redis_root'] = '';
					$redis['redis_psw'] = 'crs-0tnhmzlv:Skjhh3432skjd686';
					$redis['redis_name'] = 0;
					$this->set('redis_room',$redis);
					
					$redis['redis_host'] = '10.66.182.155';
					$redis['redis_port'] = 6379;
					$redis['redis_root'] = '';
					$redis['redis_psw'] = 'crs-7itv2obn:Skjhh3432skjd686';
					$redis['redis_name'] = 0;
					$this->set('redis_game',$redis);
					break;
				case 'king.dcyouxi.com':
				case 'xytj.dcyouxi.com':
					$data = array();
					$data['mysql_host'] = '10.66.223.55';
					$data['mysql_port'] = '3306';
					$data['mysql_root'] = 'root';
					$data['mysql_psw'] = 'NG*&(&J4895yy39r';
					$data['mysql_name'] = 'king';
					$data['mysql_charset'] = 'utf8';

					$this->set('mysql',$data);

					$redis =  array();
					$redis['redis_host'] = '10.66.185.63';
					$redis['redis_port'] = 6379;
					$redis['redis_root'] = '';
					$redis['redis_psw'] = 'crs-dodxfhcz:Skjhh3432skjd686';
					$redis['redis_name'] = 0;
					$this->set('redis_user',$redis);
					
					$redis['redis_host'] = '10.66.183.139';
					$redis['redis_port'] = 6379;
					$redis['redis_root'] = '';
					$redis['redis_psw'] = 'crs-furby83t:Skjhh3432skjd686';
					$redis['redis_name'] = 0;
					$this->set('redis_room',$redis);
					
					$redis['redis_host'] = '10.66.177.175';
					$redis['redis_port'] = 6379;
					$redis['redis_root'] = '';
					$redis['redis_psw'] = 'crs-qa3mvop7:Skjhh3432skjd686';
					$redis['redis_name'] = 0;
					$this->set('redis_game',$redis);
					break;
				default:
					# code...
					break;
			}
			
		}

		protected function init_data($srv_type,$host_name) {
			//$this->m_data

            //金石游戏相关配置
			$data = array();
			$data['weixin_app_id'] = 'wx89fc281767cbbef8';
			$data['weixin_app_secret'] = '417a78b8b7fb55e28fa657b52aeb9bd5';
			$data['weixin_pay_mch_id'] = '1497756592';
			$data['weixin_pay_key'] = 'DSHGiljhar9p387yaryeaor37ryeTkhg';
            $data['weixin_access_token_url'] = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
            $data['weixin_user_url'] = 'https://api.weixin.qq.com/sns/userinfo?';
            $data['weixin_refresh_url'] = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?';
            $data['weixin_notify_url'] = "http://jinshi.dcgames.cn/jinshigame/wxpayapi/wx/weixinnotify.php";
			$this->m_data['jinshigame'] = $data;

			//幸运王国的相关配置
			$data = array();
			$data['weixin_app_id'] = 'wx9e6c0b3e359d2ff6';
			$data['weixin_app_secret'] = '550114dad2560cd0f1c12229da5fc74c';
			$data['weixin_pay_mch_id'] = '1497805762';
			$data['weixin_pay_key'] = 'SNDbskjd239dsldkjsakdbsw8736ausd';
            $data['weixin_access_token_url'] = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
            $data['weixin_user_url'] = 'https://api.weixin.qq.com/sns/userinfo?';
            $data['weixin_refresh_url'] = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?';
            $data['weixin_notify_url'] = "http://kingdom.dcgames.cn/kingdom/wxpayapi/wx/weixinnotify.php";
			$this->m_data['kingdom'] = $data;

			//星云的相关配置
			$data = array();
			$data['weixin_app_id'] = 'wx9e6c0b3e359d2ff6';
			$data['weixin_app_secret'] = '550114dad2560cd0f1c12229da5fc74c';
			$data['weixin_pay_mch_id'] = '1497805762';
			$data['weixin_pay_key'] = 'SNDbskjd239dsldkjsakdbsw8736ausd';
            $data['weixin_access_token_url'] = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
            $data['weixin_user_url'] = 'https://api.weixin.qq.com/sns/userinfo?';
            $data['weixin_refresh_url'] = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?';
            $data['weixin_notify_url'] = "http://xingyun.dcgames.cn/xingyun/wxpayapi/wx/weixinnotify.php";
			$this->m_data['xingyun'] = $data;


			//推送的配置信息
			$data = array();
			$data['push_keys'] = 'bs1029384756';
			switch ($host_name) {
				case 'jinshi.dcgames.cn':
					//测试服
					$data['push_host'] = '123.207.101.104';
					$data['push_udp'] = 7001;
					$data['push_tcp'] = 7002;
					break;
				case 'kingdom.dcgames.cn':
					//测试服
					$data['push_host'] = '139.199.76.246';
					$data['push_udp'] = 7011;
					$data['push_tcp'] = 7012;
					break;
				case 'xingyun.dcgames.cn':
					$data['push_host'] = '139.199.68.131';
					$data['push_udp'] = 7011;
					$data['push_tcp'] = 7012;
					break;
				case 'jinshi.dcyouxi.com':
					$data['push_host'] = '122.152.217.50';
					$data['push_keys'] = 'bs1029384756';
					$data['push_udp'] = 7001;
					$data['push_tcp'] = 7002;
					break;
				case 'king.dcyouxi.com':
					$data['push_host'] = '111.231.120.252';
					$data['push_keys'] = 'bs1029384756';
					$data['push_udp'] = 7011;
					$data['push_tcp'] = 7012;
					break;
				default:
					//内网
					$data['push_host'] = '192.168.1.210';
					$data['push_udp'] = 7011;
					$data['push_tcp'] = 7012;
					break;
			}
			
			$this->m_data['push_info'] = $data;

			$this->init_apple_info($srv_type,$host_name);

		}

		protected function get_data($project_name) {
			return $this->m_data[$project_name];
		}

		protected function init_weixin_info($host_name) {
			switch ($host_name) {
				case 'jinshi.dcgames.cn':
					$weixin_info = $this->get_data('jinshigame');
					break;
				case 'kingdom.dcgames.cn':
					$weixin_info = $this->get_data('kingdom');
					$weixin_info['weixin_notify_url'] = "http://kingdom.dcgames.cn/kingdom/wxpayapi/wx/weixinnotify.php";
					break;
				case 'xingyun.dcgames.cn':
					$weixin_info = $this->get_data('xingyun');
					break;
				case 'jinshi.dcyouxi.com':
					$weixin_info = $this->get_data('jinshigame');
					$weixin_info['weixin_notify_url'] = "http://jinshi.dcyouxi.com/jinshigame/wxpayapi/wx/weixinnotify.php";
					break;
				case 'king.dcyouxi.com':
					$weixin_info = $this->get_data('kingdom');
					$weixin_info['weixin_notify_url'] = "http://king.dcyouxi.com/king/wxpayapi/wx/weixinnotify.php";
					break;
				default:
					$weixin_info = $this->get_data('jinshigame');
					break;
			}	
			
			if(is_null($weixin_info)) return;
			$this->set('weixin_info',$weixin_info);
			
		}

		protected function init_apple_info($srv_type,$host_name) {
			$data = array();
			$data['apple_release_url'] = 'https://buy.itunes.apple.com/verifyReceipt';
			$data['apple_test_url'] = 'https://sandbox.itunes.apple.com/verifyReceipt';
			$data['apple_bundle_id'] = 'com.yonjianmajiang.dachuan';
			$this->m_data['apple_info'] = $data;
			switch ($host_name) {
				case 'jinshi.dcgames.cn':
				case 'jinshi.dcyouxi.com':
					$data['apple_bundle_id'] = 'com.jinshigamecenter.dc';
					break;
				case 'kingdom.dcgames.cn':
				case 'king.dcyouxi.com':
					$data['apple_bundle_id'] = 'com.dachuan.cupid4';
					break;
				case 'xingyun.dcgames.cn':
				case 'xingyun.dcyouxi.com':
					$data['apple_bundle_id'] = 'com.dc.better';
					break;
				default:
					$data['apple_bundle_id'] = 'com.yonjianmajiang.dachuan';
					break;
			}	
			
			$this->set('apple_info',$data);
		}

		protected function init_project_data($project_name,$key_name) {
			$data = $this->get_data($project_name);
			if(is_null($data)) return;
			$this->set($key_name,$data);
		}

		//set database config
		private function set_database_info($keys,$host,$port,$user_name,$psw,$db_name,$charset) {
			$data = array();
			$data['mysql_host'] = $host;
			$data['mysql_port'] = $port;
			$data['mysql_root'] = $user_name;
			$data['mysql_psw'] = $psw;
			$data['mysql_name'] = $db_name;
			$data['mysql_charset'] = $charset;

			$this->set($keys,$data);
		}
		//set redis config
		private function set_redis_info($keys,$host,$port,$user_name,$psw,$db) {
			$redis =  array();
			$redis['redis_host'] = $host;
			$redis['redis_port'] = $port;
			$redis['redis_root'] = $user_name;
			$redis['redis_psw'] = $psw;
			$redis['redis_name'] = $db;
			$this->set($keys,$redis);
		}
		//set push config
		private function set_push_info($host,$udp_port,$tcp_post) {
			$data['push_host'] = $host;
			$data['push_keys'] = 'bs1029384756';
			$data['push_udp'] = $udp_port;
			$data['push_tcp'] = $tcp_post;
			$this->m_data['push_info'] = $data;
		}

        /**
         * 扩展配置
         * @param $srv_type
         * @param $host_name
         * @author Zhanghui
         */
		protected function init_extend_config($srv_type, $host_name)
        {
            $data = array();

            // 金石游戏配置
            $data['video_ip'] = '192.168.1.210';      // 录像服务器IP
            $data['video_dir'] = dirname(ROOT_DIR).DIRECTORY_SEPARATOR.'jinshi_download'.DIRECTORY_SEPARATOR;      // 录像文件目录

            $this->set('jinshigame_extend_config', $data);
        }
	}
?>