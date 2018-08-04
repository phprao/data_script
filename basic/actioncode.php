<?php
	class actioncode {

		public static $param_name = 'param';
		public static $action_name = 'action';
		public static $version_name = 'version';
		public static $key_name = 'key_value';
		public static $flag_name = 'flag_value';
		public static $data_name = 'data_value';
		public static $code_name = "code_value";
		public static $desc_name = "desc_value";
		public static $sign_name = "sign_value";
		//system code by 0 - 20000, user code 20000 after
		public static $basic_api_ok_code = 0;								// ok
		public static $basic_api_request_action_code = 10001; 				//request error
		public static $basic_api_request_action_type_code = 10002;			//request data error
		public static $basic_api_request_no_action_code = 10003;			//no request action
		public static $basic_api_request_action_sign_out = 10004;			//no request action time out
        public static $basic_api_request_action_param_code = 10005;         //request action param error
		//redis mysql
		public static $basic_api_redis_connect_fail_code = 40001; 
        public static $basic_api_redis_connect_mysql_code = 40002;
        public static $basic_api_redis_login_fail_code = 40003;
        public static $basic_api_redis_select_fail_code = 40004;
		//basicaction
		//public static $basicaction_error_code1=10004;
		//public static $basicaction_error_code2=10005;
		//public static $basicaction_error_code3=10006;
		//public static $basicaction_error_code4=10007;
		//public static $basicaction_error_code5=10008;

		public static $basicaction_player_code = 100100;

        /**
         * 玩家和俱乐部信息不存在
         * @var int
         */
        public static $basicaction_clubplayer_code = 100200;
        /**
         * 玩家和俱乐部信息写入redis失败不存在
         * @var int
         */
        public static $basicaction_clubplayer_fail_code = 100300;

        public static $basicaction_club_desk_code = 100400;

        public static $basicaction_query_room_code = 100500;

        /**
         * 微信登录的请求参数错误
         * @var int
         */
        public static $basicaction_player_login = 100600;

        /**
         * 微信登录获取token错误
         * @var int
         */
        public static $basicaction_wxlogin_token = 100700;

        /**
         * 获取微信的用户的基本信息
         * @var int
         */
        public static $basicaction_wxlogin_userinfo = 100800;

        /**
         * 用户数据异常
         * @var int
         */
        public static $basicaction_wxlogin_data_error = 100900;

        /**
         * 用户注册失败
         * @var int
         */
        public static $basicaction_wxlogin_register_error = 101000;

        /**
         * 写入redis用户的信息失败。
         * @var int
         */
        public static $basicaction_wxlogin_player_faile_code = 101100;

        public static $basicaction_player_room_code = 101200;

        /**
         * 手机号码不能为空
         * @var int
         */
        public static $basicaction_player_mobile_error = 101300;
        public static $basicaction_player_mobile_bind = 101301;
        public static $basicaction_player_mobile_user = 101302;
        public static $basicaction_player_mobile_user_exit = 101303;
        public static $basicaction_player_mobile_mobile = 101304;

        public static $basicaction_pay_error = 101400;

        public static $basicaction_apple_pay_error = 101500;


        /**
         * 玩家金库转入转出错误码
         * @var int
         */
        public static $basicaction_player_transfer_coins_error = 200100;

        /**
         * 查询玩家金库金币错误码
         * @var int
         */
        public static $basicaction_query_player_safe_box_error = 200200;

        /**
         * 玩家互动表情错误码
         * @var int
         */
        public static $basicaction_player_emoticon_error = 200300;

        /**
         * 更新玩家最后登陆IP错误码
         * @var int
         */
        public static $basicaction_update_player_login_ip_error = 200400;

        /**
         * 更新玩家最后登陆IP错误码
         * @var int
         */
        public static $basicaction_query_player_login_ip_error = 200500;

        /**
         * 更新玩家互动表情使用次数
         * @var int
         */
        public static $basicaction_update_player_emoticon_used_times_error = 200600;

        /**
         * 玩家消息信息 错误码
         * @var int
         */
        public static $basicaction_player_message_error = 200700;

        /**
         * 玩家游戏记录 错误码
         * @var int
         */
        public static $basicaction_player_game_record_error = 200800;

        /**
         * 同步玩家游戏记录 错误码
         * @var int
         */
        public static $basicaction_sync_player_record_error = 200900;

        /**
         * 查询玩家录像 错误码
         * @var int
         */
        public static $basicaction_download_player_video_error = 201000;
    }
?>