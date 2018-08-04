<?php
	class basicfields {
		//玩家基本信息
		public static function player_fields(basictask $task) {
			$task->set_fields_default('player_id','player_id',0,'int');
			$task->set_fields_default('player_name','player_name','');
			$task->set_fields_default('player_nickname','player_nickname','');
			$task->set_fields_default('player_password','player_password','');
            $task->set_fields_default('player_salt','player_salt','');
			$task->set_fields_default('player_phone','player_phone','');
			$task->set_fields_default('player_pcid','player_pcid','');
            $task->set_fields_default('player_openid_app','player_openid_app','');
            $task->set_fields_default('player_openid_gzh','player_openid_gzh','');
            $task->set_fields_default('player_unionid','player_unionid','');
			$task->set_fields_default('player_status','player_status',0);
			$task->set_fields_default('player_vip_level','player_vip_level',0);
			$task->set_fields_default('player_resigter_time','player_resigter_time',time());
			$task->set_fields_default('player_robot','player_robot',0);
			$task->set_fields_default('player_guest','player_guest',0);
			$task->set_fields_default('player_icon_id','player_icon_id',0);
            $task->set_fields_default('player_identification_number','player_identification_number','');
            $task->set_fields_default('player_identification_name','player_identification_name','');
			$task->set_fields_default('player_channel', 'player_channel', 0);
		}

		//玩家扩展信息
		public static function playerinfo_fields(basictask $task) {
            $task->set_fields_default('id','id',0,'int');
            $task->set_fields_default('player_id','player_id',0,'int');
			$task->set_fields_default('player_money','player_money',0);
			$task->set_fields_default('player_coins','player_coins',0);
			$task->set_fields_default('player_masonry','player_masonry',0);
			$task->set_fields_default('player_safe_box','player_safe_box',0);
			$task->set_fields_default('player_lottery','player_lottery',0);
			$task->set_fields_default('player_club_id','player_club_id',0);
            $task->set_fields_default('player_header_image','player_header_image','');
            $task->set_fields_default('player_sex','player_sex',0);
            $task->set_fields_default('player_signature','player_signature','');
            $task->set_fields_default('player_login_time','player_login_time','');
            $task->set_fields_default('player_login_ip','player_login_ip','');
            $task->set_fields_default('player_author','player_author',0);
            $task->set_fields_default('player_online','player_online',time());
		}

		//俱乐部信息
		public static function clubinfo_fields(basictask $task) {
			$task->set_fields_default('club_id','club_id',0,'int');
			$task->set_fields_default('club_name','club_name','');
            $task->set_fields_default('club_head_image','club_head_image','');
			$task->set_fields_default('club_time','club_time',time());
            $task->set_fields_default('club_head_image','club_head_image','');
            $task->set_fields_default('club_addr','club_addr','');
            $task->set_fields_default('club_tel','club_tel','');
            $task->set_fields_default('club_auth','club_auth',0,'int');
            $task->set_fields_default('club_pic','club_pic','');
            $task->set_fields_default('club_money_rate','club_money_rate',0);
		}

		//俱乐部游戏列表
		public static function clubgamelist_fields(basictask $task) {
			$task->set_fields_default('club_id','club_id',0,'int');
			$task->set_fields_default('game_id','game_id',0);
		}

        //玩家道具信息
        public static function playerpropsinfo_fields(basictask $task) {
            $task->set_fields_default('player_id','player_id',0,'int');
            $task->set_fields_default('prop_id','prop_id',0,'int');
            $task->set_fields_default('num','num',0,'int');
        }

		//俱乐部房间列表
		public static function clubroomlist_fields(basictask $task) {
			$task->set_fields_default('club_id','club_id',0,'int');
			$task->set_fields_default('room_id','room_id',0);
			$task->set_fields_default('game_id','game_id',0);
		}

		//游戏信息
		public static function gameinfo_fields(basictask $task) {
			$task->set_fields_default('game_id','game_id',0,'int');
			$task->set_fields_default('game_name','game_name','');
			$task->set_fields_default('game_desk_members_count','game_desk_members_count',0);
			$task->set_fields_default('game_kind','game_kind',0);
			$task->set_fields_default('game_version','game_version',0);
			$task->set_fields_default('game_status','game_status',0);
			$task->set_fields_default('game_free','game_free',0);
			$task->set_fields_default('game_sit_random','game_sit_random',0);
			$task->set_fields_default('game_dissolve_time','game_dissolve_time',0);
			$task->set_fields_default('game_deduction_rate','game_deduction_rate',0);
			$task->set_fields_default('game_play_count','game_play_count',0);
            $task->set_fields_default('game_active_time','game_active_time',0);
		}

		//房间信息
		public static function roominfo_fields(basictask $task) {
			$task->set_fields_default('room_id','room_id',0,'int');
			$task->set_fields_default('room_name','room_name','');
			$task->set_fields_default('room_game_id','room_game_id',0);
			$task->set_fields_default('room_rule','room_rule',0);
			$task->set_fields_default('room_desk_count','room_desk_count',0);
			$task->set_fields_default('room_status','room_status',0);
			$task->set_fields_default('room_is_open','room_is_open',0);
			$task->set_fields_default('room_min_money','room_min_money',0);
			$task->set_fields_default('room_max_money','room_max_money',0);
			$task->set_fields_default('room_srv_host','room_srv_host','');
			$task->set_fields_default('room_srv_port','room_srv_port',0);
			$task->set_fields_default('room_srv_dll_name','room_srv_dll_name','');
			$task->set_fields_default('room_encrypt','room_encrypt',0);
			$task->set_fields_default('room_password','room_password','');
			$task->set_fields_default('room_tax','room_tax',0);
			$task->set_fields_default('room_base_point','room_base_point',0);
			$task->set_fields_default('room_vip','room_vip',0);
			$task->set_fields_default('room_type','room_type',0);

			
		}

		//俱乐部房间信息
		public static function club_room_info_fields(basictask $task) {
			$task->set_fields_default('club_room_id','club_room_id',0,'int');
			$task->set_fields_default('club_room_club_id','club_room_club_id',0);
			$task->set_fields_default('club_room_game_id','club_room_game_id',0);
			$task->set_fields_default('club_room_desk_count','club_room_desk_count',0);
			$task->set_fields_default('club_room_is_work','club_room_is_work',0);
			$task->set_fields_default('club_room_is_open','club_room_is_open',0);
			$task->set_fields_default('club_room_desk_members_count','club_room_desk_members_count',0);
			$task->set_fields_default('club_room_type','club_room_type',0);
			$task->set_fields_default('club_room_level','club_room_level',0);
			$task->set_fields_default('club_room_basic_points','club_room_basic_points',0);
			$task->set_fields_default('club_room_min_coin','club_room_min_coin',0);
			$task->set_fields_default('club_room_max_coin','club_room_max_coin',0);
            $task->set_fields_default('club_room_rule_id','club_room_rule_id',0);
			$task->set_fields_default('club_room_name','club_room_name','');
            $task->set_fields_default('club_room_desk_param','club_room_desk_param','');
		}

        /**
         * 玩家和俱乐部信息表
         * @param basictask $task
         */
        public static function clubplayer_fields(basictask $task) {
            $task->set_fields_default('id','id',0,'int');
            $task->set_fields_default('club_id','club_id',0, 'int');
            $task->set_fields_default('player_id','player_id',0,'int');
            $task->set_fields_default('player_tokens','player_tokens',0,'int');
            $task->set_fields_default('join_time','join_time',0,'int');
            $task->set_fields_default('club_head_image','club_head_image','');
            $task->set_fields_default('club_name','club_name','');
            $task->set_fields_default('online_status','online_status','');
        }

        /**
         * 玩家和俱乐部信息表
         * @param basictask $task
         */
        public static function club_desk_info_fields(basictask $task) {
        	//平台管理
            $task->set_fields_default('club_desk_id','club_desk_id',0,'int');
            $task->set_fields_default('club_desk_club_room_id','club_desk_club_room_id',0, 'int');
            $task->set_fields_default('club_desk_club_id','club_desk_club_id',0,'int');
            $task->set_fields_default('club_desk_club_room_desk_no','club_desk_club_room_desk_no',0,'int');
            //真实服务room id ,game id ,room_desk id
            $task->set_fields_default('club_desk_room_id','club_desk_room_id',0,'int');//db 	room id
            $task->set_fields_default('club_desk_desk_no','club_desk_desk_no',0,'int');//0-99
            $task->set_fields_default('club_desk_game_id','club_desk_game_id',0,'int');//db     game id

            //包间信息
            $task->set_fields_default('club_desk_room_no','club_desk_room_no',0);
            $task->set_fields_default('club_desk_player_id','club_desk_player_id',0);
            $task->set_fields_default('club_desk_param','club_desk_param','');
            
            $task->set_fields_default('club_desk_time','club_desk_time',time());

            $task->set_fields_default('club_desk_status','club_desk_status',1);
           
        }

        public static function club_desk_info_redis_fields(basictask $task) {
        	 //以下是游戏服务管理
            $task->set_fields_default('club_desk_status','club_desk_status',0,'int');//0-3
            $task->set_fields_default('club_desk_player_list','club_desk_player_list','');
            $task->set_fields_default('club_desk_is_work','club_desk_is_work',0,'int');
            $task->set_fields_default('club_desk_members_count','club_desk_members_count',0,'int');
            $task->set_fields_default('club_desk_members_cur','club_desk_members_cur',0,'int');
            $task->set_fields_default('club_desk_rule_id','club_desk_rule_id',0,'int');//
        }

        /**
         * 商品
         * @param basictask $task
         */
        public static function goodsinfo_fields(basictask $task) {
            $task->set_fields_default('goods_id','goods_id',0,'int');
            $task->set_fields_default('goods_name','goods_name','');
            $task->set_fields_default('goods_price','goods_price',0);
            $task->set_fields_default('goods_get_price','goods_get_price',0);
            $task->set_fields_default('goods_type','goods_type',0,'int');
            $task->set_fields_default('goods_status','goods_status',0,'int');
            $task->set_fields_default('goods_card','goods_card',0,'int');
            $task->set_fields_default('goods_club_id','goods_club_id',0);
            $task->set_fields_default('goods_desc','goods_desc','');
            $task->set_fields_default('goods_time','goods_time','');
            $task->set_fields_default('goods_product_item','goods_product_item',0);
            $task->set_fields_default('goods_product_type','goods_product_type',0);
            $task->set_fields_default('goods_product_id','goods_product_id','');
            $task->set_fields_default('goods_club_id','goods_club_id','');
        }


        /**
         *订单
         * @param basictask $task
         */
        public static function orderlog_fields(basictask $task) {
            $task->set_fields_default('order_id','order_id',0,'int');
            $task->set_fields_default('order_player_id','order_player_id',0, 'int');
            $task->set_fields_default('order_goods_id','order_goods_id',0, 'int');
            $task->set_fields_default('order_price','order_price',0,'int');
            $task->set_fields_default('order_pay_type','order_pay_type',0,'int');
            $task->set_fields_default('order_is_send','order_is_send',0,'int');
            $task->set_fields_default('order_orderno','order_orderno','');
            $task->set_fields_default('order_get_type','order_get_type',0);
            $task->set_fields_default('order_get_price','order_get_price',0);
            $task->set_fields_default('order_out_transaction_id','order_out_transaction_id','');
            $task->set_fields_default('order_create_time','order_create_time',0);
            $task->set_fields_default('order_update_time','order_update_time',0);
            $task->set_fields_default('order_extension','order_extension','');
            $task->set_fields_default('order_pay_channel','order_pay_channel',0);
            $task->set_fields_default('order_club_id','order_club_id',0);
        }

        /*
        * 充值记录
        * */
        public static function payrecord_fields(basictask $task){

            $task->set_fields_default('recore_id','recore_id',0,'int');
            $task->set_fields_default('recore_player_id','recore_player_id',0, 'int');
            $task->set_fields_default('recore_type','recore_type',0, 'int');
            //$task->set_fields_default('recore_state','recore_state',0,'int');
            $task->set_fields_default('recore_price','recore_price',0,'int');
            $task->set_fields_default('recore_get_type','recore_get_type',0,'int');
            $task->set_fields_default('recore_get_price','recore_get_price',0,'int');
            $task->set_fields_default('recore_create_time','recore_create_time',0);
            $task->set_fields_default('recore_before_money','recore_before_money',0);
            $task->set_fields_default('recore_after_money','recore_after_money',0);
            $task->set_fields_default('recore_order_id','recore_order_id',0);
        }

        /*
         * @货币改变记录
         */
        public static function money_change_fields(basictask $task) {
        	$task->set_fields_default('change_money_id','change_money_id',0,'int');
        	$task->set_fields_default('change_money_player_id','change_money_player_id',0);
        	$task->set_fields_default('change_money_player_club_id','change_money_player_club_id',0);
        	$task->set_fields_default('change_money_club_id','change_money_club_id',0);
            $task->set_fields_default('change_money_club_room_no','change_money_club_room_no',0);
        	$task->set_fields_default('change_money_club_room_id','change_money_club_room_id',0);
        	// $task->set_fields_default('change_money_club_desk_no','change_money_club_desk_no',0);
        	$task->set_fields_default('change_money_club_desk_id','change_money_club_desk_id',0);
        	$task->set_fields_default('change_money_club_desk_no','change_money_club_desk_no',0);
        	$task->set_fields_default('change_money_game_id','change_money_game_id',0);
        	$task->set_fields_default('change_money_room_id','change_money_room_id',0);
        	$task->set_fields_default('change_money_desk_no','change_money_desk_no',0);
        	$task->set_fields_default('change_money_type','change_money_type',0);
            $task->set_fields_default('change_money_tax','change_money_tax',0);
        	$task->set_fields_default('change_money_money_type','change_money_money_type',0);
        	$task->set_fields_default('change_money_money_value','change_money_money_value',0);
        	$task->set_fields_default('change_money_begin_value','change_money_begin_value',0);
        	$task->set_fields_default('change_money_after_value','change_money_after_value',0);
        	$task->set_fields_default('change_money_time','change_money_time',time());
        	$task->set_fields_default('change_money_param','change_money_param','');
            $task->set_fields_default('change_money_update_time','change_money_update_time',0);
        }

        /*
         * @货币改变记录-备份
         */
        public static function moneychangestore_fileds(basictask $task) {
            $task->set_fields_default('change_money_id','change_money_id',0,'int');
            $task->set_fields_default('change_money_player_id','change_money_player_id',0);
            $task->set_fields_default('change_money_player_club_id','change_money_player_club_id',0);
            $task->set_fields_default('change_money_club_id','change_money_club_id',0);
            $task->set_fields_default('change_money_club_room_no','change_money_club_room_no',0);
            $task->set_fields_default('change_money_club_room_id','change_money_club_room_id',0);
            // $task->set_fields_default('change_money_club_desk_no','change_money_club_desk_no',0);
            $task->set_fields_default('change_money_club_desk_id','change_money_club_desk_id',0);
            $task->set_fields_default('change_money_club_desk_no','change_money_club_desk_no',0);
            $task->set_fields_default('change_money_game_id','change_money_game_id',0);
            $task->set_fields_default('change_money_room_id','change_money_room_id',0);
            $task->set_fields_default('change_money_desk_no','change_money_desk_no',0);
            $task->set_fields_default('change_money_type','change_money_type',0);
            $task->set_fields_default('change_money_tax','change_money_tax',0);
            $task->set_fields_default('change_money_money_type','change_money_money_type',0);
            $task->set_fields_default('change_money_money_value','change_money_money_value',0);
            $task->set_fields_default('change_money_begin_value','change_money_begin_value',0);
            $task->set_fields_default('change_money_after_value','change_money_after_value',0);
            $task->set_fields_default('change_money_time','change_money_time',time());
            $task->set_fields_default('change_money_param','change_money_param','');
        }

        /*
         * @货币改变记录
         */
        public static function money_change_player_fields(basictask $task) {
            $task->set_fields_default('change_money_player_id','change_money_player_id',0); 
            $task->set_fields_default('change_money_parent_agents_id','change_money_parent_agents_id',0);
            $task->set_fields_default('change_money_super_agents_id','change_money_super_agents_id',0);  
            $task->set_fields_default('change_money_club_id','change_money_club_id',0);          
            $task->set_fields_default('change_money_club_room_id','change_money_club_room_id',0);     
            $task->set_fields_default('change_money_club_desk_no','change_money_club_desk_no',0);     
            $task->set_fields_default('change_money_club_desk_id','change_money_club_desk_id',0);         
            $task->set_fields_default('change_money_room_id','change_money_room_id',0);         
            $task->set_fields_default('change_money_room_name','change_money_room_name',0);        
            $task->set_fields_default('change_money_desk_no','change_money_desk_no',0);          
            $task->set_fields_default('change_money_game_id','change_money_game_id',0);          
            $task->set_fields_default('change_money_game_name','change_money_game_name',0);        
            $task->set_fields_default('change_money_type','change_money_type',0);             
            $task->set_fields_default('change_money_tax','change_money_tax',0);              
            $task->set_fields_default('change_money_my_tax','change_money_my_tax',0);           
            $task->set_fields_default('change_money_share_rate','change_money_share_rate',0); 
            $task->set_fields_default('change_money_one_tax','change_money_one_tax',0); 
            $task->set_fields_default('change_money_one_rate','change_money_one_rate',0); 
            $task->set_fields_default('change_money_one_agents_id','change_money_one_agents_id',0); 
            $task->set_fields_default('change_money_two_tax','change_money_two_tax',0); 
            $task->set_fields_default('change_money_two_rate','change_money_two_rate',0); 
            $task->set_fields_default('change_money_two_agents_id','change_money_two_agents_id',0); 
            $task->set_fields_default('change_money_money_type','change_money_money_type',0);       
            $task->set_fields_default('change_money_money_value','change_money_money_value',0);      
            $task->set_fields_default('change_money_begin_value','change_money_begin_value',0);      
            $task->set_fields_default('change_money_after_value','change_money_after_value',0);      
            $task->set_fields_default('change_money_time','change_money_time',0);             
            $task->set_fields_default('change_money_date','change_money_date',0);             
            $task->set_fields_default('change_money_param','change_money_param',0);            
        }

        /*
         * @货币改变记录-玩家-小时
         */
        public static function playermoneychangehour_fileds(basictask $task) {
            $task->set_fields_default('statistics_id','statistics_id',0,'int');
            $task->set_fields_default('statistics_parent_agents_id','statistics_parent_agents_id',0);
            $task->set_fields_default('statistics_player_id','statistics_player_id',0);
            $task->set_fields_default('statistics_money_type','statistics_money_type',0);
            $task->set_fields_default('statistics_money_data','statistics_money_data',0);
            $task->set_fields_default('statistics_cost_detail','statistics_cost_detail',0);
            $task->set_fields_default('statistics_time','statistics_time',0);
            $task->set_fields_default('statistics_date','statistics_date',0);
            $task->set_fields_default('statistics_add_time','statistics_add_time',0);
        }

        /*
         * @货币改变记录-玩家-天
         */
        public static function playermoneychangeday_fileds(basictask $task) {
            $task->set_fields_default('statistics_id','statistics_id',0,'int');
            $task->set_fields_default('statistics_parent_agents_id','statistics_parent_agents_id',0);
            $task->set_fields_default('statistics_super_agents_id','statistics_super_agents_id',0);
            $task->set_fields_default('statistics_player_id','statistics_player_id',0);
            $task->set_fields_default('statistics_type','statistics_type',0);
            $task->set_fields_default('statistics_money_type','statistics_money_type',0);
            $task->set_fields_default('statistics_money_type_rate','statistics_money_type_rate',0);
            $task->set_fields_default('statistics_data','statistics_data',0);
            $task->set_fields_default('statistics_income','statistics_income',0);
            $task->set_fields_default('statistics_my_data','statistics_my_data',0);
            $task->set_fields_default('statistics_my_income','statistics_my_income',0);
            $task->set_fields_default('statistics_share_money_low','statistics_share_money_low',0);
            $task->set_fields_default('statistics_share_money_high','statistics_share_money_high',0);
            $task->set_fields_default('statistics_cost_detail','statistics_cost_detail',0);
            $task->set_fields_default('statistics_time','statistics_time',0);
            $task->set_fields_default('statistics_date','statistics_date',0);
            $task->set_fields_default('statistics_add_time','statistics_add_time',0);
        }

        /*
         * @货币改变记录-代理分成
         */
        public static function playermoneychangepromoter_fileds(basictask $task) {
            $task->set_fields_default('statistics_id','statistics_id',0,'int');
            $task->set_fields_default('statistics_agents_id','statistics_agents_id',0);
            $task->set_fields_default('statistics_agents_player_id','statistics_agents_player_id',0);
            $task->set_fields_default('statistics_super_agents_id','statistics_super_agents_id',0);
            $task->set_fields_default('statistics_from_value','statistics_from_value',0);
            $task->set_fields_default('statistics_from','statistics_from',0);
            $task->set_fields_default('statistics_type','statistics_type',0);
            $task->set_fields_default('statistics_money_type','statistics_money_type',0);
            $task->set_fields_default('statistics_money_type_rate','statistics_money_type_rate',0);
            $task->set_fields_default('statistics_data','statistics_data',0);
            $task->set_fields_default('statistics_income','statistics_income',0);
            $task->set_fields_default('statistics_my_data','statistics_my_data',0);
            $task->set_fields_default('statistics_my_income','statistics_my_income',0);
            $task->set_fields_default('statistics_share_money_low','statistics_share_money_low',0);
            $task->set_fields_default('statistics_share_money_high','statistics_share_money_high',0);
            $task->set_fields_default('statistics_status','statistics_status',0);
            $task->set_fields_default('statistics_time','statistics_time',0);
            $task->set_fields_default('statistics_date','statistics_date',0);
            $task->set_fields_default('statistics_add_time','statistics_add_time',0);
        }

        //游戏记录
        public static function gamerecord_fields(basictask $task) {

        	$task->set_fields_default('game_record_id','game_record_id',0,'int');
        	$task->set_fields_default('game_record_player_id','game_record_player_id',0,'int');
        	$task->set_fields_default('game_record_player_club_id','game_record_player_club_id',0,'int');
        	$task->set_fields_default('game_record_club_id','game_record_club_id',0,'int');
        	$task->set_fields_default('game_record_club_room_id','game_record_club_room_id',0,'int');
        	$task->set_fields_default('game_record_club_room_desk_no','game_record_club_room_desk_no',0,'int');
        	$task->set_fields_default('game_record_club_desk_id','game_record_club_desk_id',0,'int');
        	$task->set_fields_default('game_record_club_room_no','game_record_club_room_no',0,'int');
        	$task->set_fields_default('game_record_game_id','game_record_game_id',0,'int');
        	$task->set_fields_default('game_record_room_id','game_record_room_id',0,'int');
        	$task->set_fields_default('game_record_desk_no','game_record_desk_no',0,'int');
        	$task->set_fields_default('game_record_win_state','game_record_win_state',0,'int');
        	$task->set_fields_default('game_record_score_type','game_record_score_type',0,'int');
        	$task->set_fields_default('game_record_score_value','game_record_score_value',0,'int');
        	$task->set_fields_default('game_record_game_over_time','game_record_game_over_time',0,'int');
        	$task->set_fields_default('game_record_time','game_record_time',time(),'int');
        	$task->set_fields_default('game_record_desc','game_record_desc','');
        	$task->set_fields_default('game_record_param','game_record_param','');
        	$task->set_fields_default('game_record_data_type','game_record_data_type',0,'int');
        	$task->set_fields_default('game_record_data_value','game_record_data_value','');
            $task->set_fields_default('game_record_update_time','game_record_update_time',0,'int');
        	$task->set_fields_default('game_record_video_filename','game_record_video_filename','');
        }

        //游戏记录
        public static function gameuser_record_fields(basictask $task) {

            $task->set_fields_default('game_user_record_id','game_user_record_id',0,'int');
            $task->set_fields_default('game_board_id','game_board_id',0,'int');
            $task->set_fields_default('game_id','game_id',0,'int');
            $task->set_fields_default('player_nickname','player_nickname','');
            $task->set_fields_default('player_head_image','player_head_image','');
            $task->set_fields_default('game_record','game_record',0,'int');
            $task->set_fields_default('game_type','game_type',0,'int');
            $task->set_fields_default('game_over_time','game_over_time',0,'int');
            $task->set_fields_default('game_name','game_name','');
            $task->set_fields_default('player_id','player_id',0,'int');
            $task->set_fields_default('user_win_status','user_win_status',0,'int');
        }

        //游戏公告表
        public static function notice_fileds(basictask $task) {
            $task->set_fields_default('notice_id','notice_id',0,'int');
            $task->set_fields_default('notice_club_id','notice_club_id',0,'int');
            $task->set_fields_default('notice_type','notice_type',0,'int');
            $task->set_fields_default('notice_title','notice_title','');
            $task->set_fields_default('notice_name','notice_name','');
            $task->set_fields_default('notice_content','notice_content','');
            $task->set_fields_default('notice_create_time','notice_create_time',time(),'int');
            $task->set_fields_default('notice_param','notice_param','');
            $task->set_fields_default('notice_status','notice_status','');
	    $task->set_fields_default('notice_source','notice_source',1,'int');
        }

        //俱乐部
        public static function club_rule_fileds(basictask $task) {
            $task->set_fields_default('club_room_rule_id','club_room_rule_id',0,'int');
            $task->set_fields_default('club_room_rule_game_id','club_room_rule_game_id',0,'int');
            $task->set_fields_default('club_room_rule_club_id','club_room_rule_club_id',0,'int');
            $task->set_fields_default('club_room_rule_name','club_room_rule_name','');
            $task->set_fields_default('club_room_rule_param','club_room_rule_param','');
        }

        //游戏记录日志
        public static function gamerecordlog_fileds(basictask $task) {
            $task->set_fields_default('game_log_id','game_log_id',0);
            $task->set_fields_default('game_log_board_id','game_log_board_id',0);
            $task->set_fields_default('game_log_player_id','game_log_player_id',0);
            $task->set_fields_default('game_log_player_club_id','game_log_player_club_id',0);
            $task->set_fields_default('game_log_club_id','game_log_club_id',0);
            $task->set_fields_default('game_log_club_room_id','game_log_club_room_id',0);
            $task->set_fields_default('game_log_club_room_desk_no','game_log_club_room_desk_no',0);
            $task->set_fields_default('game_log_club_desk_id','game_log_club_desk_id',0);
            $task->set_fields_default('game_log_club_room_no','game_log_club_room_no',0);
            $task->set_fields_default('game_log_game_id','game_log_game_id',0);
            $task->set_fields_default('game_log_room_id','game_log_room_id',0);
            $task->set_fields_default('game_log_desk_no','game_log_desk_no',0);
            $task->set_fields_default('game_log_win_state','game_log_win_state',0);
            $task->set_fields_default('game_log_score_type','game_log_score_type',0);
            $task->set_fields_default('game_log_score_value','game_log_score_value',0);
            $task->set_fields_default('game_log_game_over_time','game_log_game_over_time',0);
            $task->set_fields_default('game_log_desc','game_log_desc',0);
            $task->set_fields_default('game_log_desc','game_log_desc',0);
            $task->set_fields_default('game_log_param','game_log_param',0);
            $task->set_fields_default('game_log_data_type','game_log_data_type',0);
            $task->set_fields_default('game_log_data_value','game_log_data_value',0);
            $task->set_fields_default('game_log_game_name','game_log_game_name',0);
            $task->set_fields_default('game_log_time','game_log_time',0);
            $task->set_fields_default('game_log_data','game_log_data',0);
            $task->set_fields_default('game_log_video_filename','game_log_video_filename','');
        }

        //游戏记录-备份
        public static function gamerecordstore_fileds(basictask $task) {
            $task->set_fields_default('game_log_id','game_log_id',0);
            $task->set_fields_default('game_log_board_id','game_log_board_id',0);
            $task->set_fields_default('game_log_player_id','game_log_player_id',0);
            $task->set_fields_default('game_log_player_club_id','game_log_player_club_id',0);
            $task->set_fields_default('game_log_club_id','game_log_club_id',0);
            $task->set_fields_default('game_log_club_room_id','game_log_club_room_id',0);
            $task->set_fields_default('game_log_club_room_desk_no','game_log_club_room_desk_no',0);
            $task->set_fields_default('game_log_club_desk_id','game_log_club_desk_id',0);
            $task->set_fields_default('game_log_club_room_no','game_log_club_room_no',0);
            $task->set_fields_default('game_log_game_id','game_log_game_id',0);
            $task->set_fields_default('game_log_room_id','game_log_room_id',0);
            $task->set_fields_default('game_log_desk_no','game_log_desk_no',0);
            $task->set_fields_default('game_log_win_state','game_log_win_state',0);
            $task->set_fields_default('game_log_score_type','game_log_score_type',0);
            $task->set_fields_default('game_log_score_value','game_log_score_value',0);
            $task->set_fields_default('game_log_game_over_time','game_log_game_over_time',0);
            $task->set_fields_default('game_log_desc','game_log_desc',0);
            $task->set_fields_default('game_log_desc','game_log_desc',0);
            $task->set_fields_default('game_log_param','game_log_param',0);
            $task->set_fields_default('game_log_data_type','game_log_data_type',0);
            $task->set_fields_default('game_log_data_value','game_log_data_value',0);
            $task->set_fields_default('game_log_game_name','game_log_game_name',0);
            $task->set_fields_default('game_log_time','game_log_time',0);
            $task->set_fields_default('game_log_data','game_log_data',0);
            $task->set_fields_default('game_log_video_filename','game_log_video_filename','');
        }

        // 牌局id信息
        public static function gameboardinfo_fileds(basictask $task){
            $task->set_fields_default('game_board_id','game_board_id',0);
            $task->set_fields_default('game_board_room_id','game_board_room_id',0);
            $task->set_fields_default('game_board_desk_no','game_board_desk_no',0);
            $task->set_fields_default('game_board_game_over_time','game_board_game_over_time',0);
            $task->set_fields_default('game_board_time','game_board_time',0);
        }

        // 战绩记录
        public static function gamebeatrecord_fileds(basictask $task){
            $task->set_fields_default('game_beat_id','game_beat_id',0);
            $task->set_fields_default('game_beat_board_id','game_beat_board_id',0);
            $task->set_fields_default('game_beat_player_id','game_beat_player_id',0);
            $task->set_fields_default('game_beat_player_nick','game_beat_player_nick',0);
            $task->set_fields_default('game_beat_player_head','game_beat_player_head',0);
            $task->set_fields_default('game_beat_room_no','game_beat_room_no',0);
            $task->set_fields_default('game_beat_readback','game_beat_readback',0);
            $task->set_fields_default('game_beat_over_time','game_beat_over_time',0);
            $task->set_fields_default('game_beat_game_id','game_beat_game_id',0);
            $task->set_fields_default('game_beat_game_name','game_beat_game_name',0);
            $task->set_fields_default('game_beat_player_club_id','game_beat_player_club_id',0);
            $task->set_fields_default('game_beat_club_id','game_beat_club_id',0);
            $task->set_fields_default('game_beat_win_state','game_beat_win_state',0);
            $task->set_fields_default('game_beat_score_type','game_beat_score_type',0);
            $task->set_fields_default('game_beat_score_value','game_beat_score_value',0);
            $task->set_fields_default('game_beat_time','game_beat_time',0);
            $task->set_fields_default('game_beat_room_id','game_beat_room_id',0);
            $task->set_fields_default('game_beat_room_name','game_beat_room_name',0);
        }

        //代理
        public static function agentinfo_fields(basictask $task){

            $task->set_fields_default('agent_id','agent_id',0,'int');
            $task->set_fields_default('agent_user_id','agent_user_id',0,'int');
            $task->set_fields_default('agent_player_id','agent_player_id',0, 'int');
            $task->set_fields_default('agent_parentid','agent_parentid',0, 'int');
            $task->set_fields_default('agent_p_parentid','agent_p_parentid',0, 'int');
            $task->set_fields_default('agent_top_agentid','agent_top_agentid',0, 'int');
            $task->set_fields_default('agent_name','agent_name',0,'');
            $task->set_fields_default('agent_level','agent_level',0,'int');
            $task->set_fields_default('agent_promote_count','agent_promote_count',0,'int');
            $task->set_fields_default('agent_permissions','agent_permissions',0,'int');
            $task->set_fields_default('agent_status','agent_status',0,'int');
            $task->set_fields_default('agent_login_status','agent_login_status',0,'');
            $task->set_fields_default('agent_remark','agent_remark',0,'');
            $task->set_fields_default('agent_is_agree','agent_is_agree',0,0);
            $task->set_fields_default('agent_createtime','agent_createtime',0,'');
            $task->set_fields_default('agent_star_time','agent_star_time',0,'');
            $task->set_fields_default('agent_login_status_man','agent_login_status_man',0,'int');

        }
        //代理信息表
        public static function agentaccountinfo_fields(basictask $task){

            $task->set_fields_default('agent_account_id','agent_account_id',0,'int');
            $task->set_fields_default('agent_account_agent_id','agent_account_agent_id',0, 'int');
            $task->set_fields_default('agent_account_money','agent_account_money',0, '');
            $task->set_fields_default('agent_account_alipay','agent_account_alipay',0, '');
            $task->set_fields_default('agent_account_username','agent_account_username',0, '');
            $task->set_fields_default('agent_account_mobile','agent_account_mobile',0,'');

        }

        /**
         * 代理条件
         * @param basictask $task
         */
        public static function agentconfig_fields(basictask $task)
        {
            $task->set_fields_default('agentconf_id','agentconf_id',0,'int');
            $task->set_fields_default('agent_id','agent_id',0, 'int');
            $task->set_fields_default('agent_conditions_id','agent_conditions_id',0, 'int');
            $task->set_fields_default('agentconf_time','agentconf_time',0,'int');
        }


        //代理配置选中表
        public static function agentconfiginfo_fields(basictask $task){

            $task->set_fields_default('agentconf_id','agentconf_id',0,'int');
            $task->set_fields_default('agent_id','agent_id',0, 'int');
            $task->set_fields_default('agent_conditions_id','agent_conditions_id',0, 'int');
            $task->set_fields_default('agentconf_time','agentconf_time',0,'');

        }

        /**
         * 代理条件
         * @param basictask $task
         */
        public static function agentconditions_fields(basictask $task)
        {
            $task->set_fields_default('agent_conditions_id','agent_conditions_id',0,'int');
            $task->set_fields_default('agent_conditions_name','agent_conditions_name','','string');
            $task->set_fields_default('agent_conditions_status','agent_conditions_status',0, 'int');
            $task->set_fields_default('agent_conditions_type','agent_conditions_type',0, 'int');
            $task->set_fields_default('agent_conditions_data','agent_conditions_data','','string');
        }

        /**
         * 用户上级代理记录表
         * @param basictask $task
         */
        public static function agentupgraderecord_fields(basictask $task)
        {
            $task->set_fields_default('agent_upgrade_record_id','agent_upgrade_record_id',0,'int');
            $task->set_fields_default('agent_upgrade_record_agent_id','agent_upgrade_record_agent_id',0,'int');
            $task->set_fields_default('agent_upgrade_record_player_id','agent_upgrade_record_player_id',0, 'int');
            $task->set_fields_default('agent_upgrade_record_time','agent_upgrade_record_time',0,'int');
        }


        //推广关系表
        public static function promotersinfo_fields(basictask $task){

            $task->set_fields_default('promoters_id','promoters_id',0,'int');
            $task->set_fields_default('promoters_player_id','promoters_player_id',0,'int');
            $task->set_fields_default('promoters_parent_id','promoters_parent_id',0, 'int');
            $task->set_fields_default('promoters_agent_id','promoters_agent_id',0, 'int');
            $task->set_fields_default('promoters_agent_parentid','promoters_agent_parentid',0, 'int');
            $task->set_fields_default('promoters_agent_top_agentid','promoters_agent_top_agentid',0, 'int');
            $task->set_fields_default('promoters_time','promoters_time',0,'');
        }

        //用户统计表
        public static function playerstatistical_fields(basictask $task)
        {
            $task->set_fields_default('statistical_id','statistical_id',0,'int');
            $task->set_fields_default('statistical_player_id','statistical_player_id',0,'int');
            $task->set_fields_default('statistical_agent_id','statistical_agent_id',0,'int');
            $task->set_fields_default('statistical_type','statistical_type',0, 'int');
            $task->set_fields_default('statistical_value','statistical_value',0, 'int');
            $task->set_fields_default('statistical_top_up','statistical_top_up',0, 'int');
            $task->set_fields_default('statistical_time','statistical_time',0,'');
            $task->set_fields_default('statistical_sub_total_cost','statistical_sub_total_cost',0,'int');
            $task->set_fields_default('statistical_award_money_status','statistical_award_money_status',0,'int');
            $task->set_fields_default('statistical_award_money','statistical_award_money',0,'int');
            $task->set_fields_default('statistical_award_status_time','statistical_award_status_time',0,'int');
        }

        public static function playerview_fileds(basictask $task)
        {
            $task->set_fields_default('player_id','player_id',0,'int');
            $task->set_fields_default('player_name','player_name','');
            $task->set_fields_default('player_nickname','player_nickname','');
            $task->set_fields_default('player_password','player_password','');
            $task->set_fields_default('player_phone','player_phone','');
            $task->set_fields_default('player_pcid','player_pcid','');
            $task->set_fields_default('player_status','player_status',0,'int');
            $task->set_fields_default('player_vip_level','player_vip_level',0,'int');
            $task->set_fields_default('player_resigter_time','player_resigter_time',0,'int');
            $task->set_fields_default('player_robot','player_robot',0,'int');
            $task->set_fields_default('player_guest','player_guest',0,'int');
            $task->set_fields_default('player_icon_id','player_icon_id',0,'int');
            $task->set_fields_default('player_money','player_money',0,'int');
            $task->set_fields_default('player_coins','player_coins',0,'int');
            $task->set_fields_default('player_safe_box','player_safe_box',0,'int');
            $task->set_fields_default('player_safe_box_password','player_safe_box_password','');
            $task->set_fields_default('player_lottery','player_lottery',0,'int');
            $task->set_fields_default('player_club_id','player_club_id',0,'int');
            $task->set_fields_default('player_header_image','player_header_image','');
            $task->set_fields_default('player_sex','player_sex',0,'int');
            $task->set_fields_default('player_signature','player_signature','');
            $task->set_fields_default('player_author','player_author',0,'int');
        }

        public static function ranking_fileds(basictask $task)
        {
            $task->set_fields_default('id','id',0);
            $task->set_fields_default('ranking_player_id','ranking_player_id',0);
            $task->set_fields_default('ranking_nick_name','ranking_nick_name','');
            $task->set_fields_default('ranking_player_image','ranking_player_image','');
            $task->set_fields_default('ranking_player_coins','ranking_player_coins',0);
            $task->set_fields_default('ranking_number','ranking_number',0);
            $task->set_fields_default('ranking_coins_type','ranking_coins_type',0);
            $task->set_fields_default('ranking_time','ranking_time','');
        }

        //用户
        public static function user_fileds(basictask $task)
        {
            $task->set_fields_default('id','id',0);
            $task->set_fields_default('user_login','user_login','');
            $task->set_fields_default('user_pass','user_pass','');
            $task->set_fields_default('user_email','user_email','');
            $task->set_fields_default('last_login_ip','last_login_ip','');
            $task->set_fields_default('last_login_time','last_login_time','');
            $task->set_fields_default('create_time','create_time','');
            $task->set_fields_default('user_status','user_status',0);
        }

        //用户
        public static function role_user(basictask $task)
        {
            $task->set_fields_default('role_id','role_id',0);
            $task->set_fields_default('user_id','user_id','0');
            $task->set_fields_default('kick_role','kick_role','0');
            $task->set_fields_default('pattern','pattern','0');

        }

        public static function system_config_fileds(basictask $task) {
            $task->set_fields_default('system_config_id','system_config_id',0);
            $task->set_fields_default('system_config_platform','system_config_platform',0);
            $task->set_fields_default('system_config_club_id','system_config_club_id',0);
            $task->set_fields_default('system_config_type','system_config_type',0);
            $task->set_fields_default('system_config_data','system_config_data','');
        }

        /**
         * 特代分成比例
         * @param basictask $task
         */
        public static function agentsuperincomeconfig_fields(basictask $task)
        {
            $task->set_fields_default('super_id','super_id',0, 'int');
            $task->set_fields_default('super_agent_id','super_agent_id',0, 'int');
            $task->set_fields_default('super_condition','super_condition',0, 'int');
            $task->set_fields_default('super_condition_compare','super_condition_compare','<');
            $task->set_fields_default('super_share','super_share',0, 'int');
        }

        /**
         * 玩家推广奖励配置
         * @param basictask $task
         */
        public static function promotersawardconfig_fields(basictask $task)
        {
            $task->set_fields_default('award_id','award_id',0, 'int');
            $task->set_fields_default('award_agent_id','award_agent_id',0, 'int');
            $task->set_fields_default('award_condition','award_condition',0, 'int');
            $task->set_fields_default('award_money','award_money',0, 'int');
        }

        /**
         * 代理分成比例
         * @param basictask $task
         */
        public static function agentincomeconfig_fields(basictask $task)
        {
            $task->set_fields_default('income_id','income_id',0, 'int');
            $task->set_fields_default('income_agent_id','income_agent_id',0, 'int');
            $task->set_fields_default('income_condition_number','income_condition_number',0, 'int');
            $task->set_fields_default('income_condition_money','income_condition_money',0, 'int');
            $task->set_fields_default('income_share','income_share','[]');
        }

        /**
         * 特代分成比例统计表
         * @param basictask $task
         */
        public static function agentsuperstatisticsdate_fields(basictask $task)
        {
            $task->set_fields_default('statistics_id','statistics_id',0, 'int');
            $task->set_fields_default('statistics_agent_id','statistics_agent_id',0, 'int');
            $task->set_fields_default('statistics_money_type','statistics_money_type',0, 'int');
            $task->set_fields_default('statistics_money_data_direct','statistics_money_data_direct',0, 'int');
            $task->set_fields_default('statistics_money_data','statistics_money_data',0, 'int');
            $task->set_fields_default('statistics_date','statistics_date',0, '');
            $task->set_fields_default('statistics_time','statistics_time',0, 'int');
            $task->set_fields_default('statistics_month','statistics_month',0, 'int');
            $task->set_fields_default('statistics_super_share_direct','statistics_super_share_direct',0, 'int');
            $task->set_fields_default('statistics_super_share','statistics_super_share',0, 'int');
            $task->set_fields_default('statistics_super_config','statistics_super_config','');
            $task->set_fields_default('statistics_money_rate_value','statistics_money_rate_value',1, 'int');
            $task->set_fields_default('statistics_money_rate_unit','statistics_money_rate_unit',1, 'int');
            $task->set_fields_default('statistics_money_rate_unit_type','statistics_money_rate_unit_type',1, 'int');
            $task->set_fields_default('statistics_money','statistics_money',0,'int');
            $task->set_fields_default('statistics_money_status','statistics_money_status',0, 'int');
            $task->set_fields_default('statistics_up_time','statistics_up_time',0, 'int');
            $task->set_fields_default('statistics_add_time','statistics_add_time',0, 'int');
        }

        /**
         * 货币兑换比例信息
         * @param basictask $task
         */
        public static function moneyrateinfo_fields(basictask $task)
        {
            $task->set_fields_default('money_rate_id','money_rate_id',0, 'int');
            $task->set_fields_default('money_rate_type','money_rate_type',0, 'int');
            $task->set_fields_default('money_rate_value','money_rate_value',0, 'int');
            $task->set_fields_default('money_rate_unit','money_rate_unit',0, 'int');
            $task->set_fields_default('money_rate_unit_type','money_rate_unit_type',0, 'int');
            $task->set_fields_default('money_rate_name','money_rate_name','');
            $task->set_fields_default('money_rate_param','money_rate_param','');
        }

        /**
         * 资金流水表
         * @param basictask $task
         */
        public static function agentaccountinfolog_fields(basictask $task)
        {
            $task->set_fields_default('log_id','log_id',0, 'int');
            $task->set_fields_default('log_money_type','log_money_type',1, 'int');
            $task->set_fields_default('log_agent_id','log_agent_id',0, 'int');
            $task->set_fields_default('log_bef_money','log_bef_money',0, 'int');
            $task->set_fields_default('log_money','log_money',0, 'int');
            $task->set_fields_default('log_aft_money','log_aft_money',0, 'int');
            $task->set_fields_default('log_add_time','log_add_time',0, 'int');
            $task->set_fields_default('log_type','log_type',0, 'int');
        }

        /**
         * 资金流水表
         * @param basictask $task
         */
        public static function changemoneyinfo_fields(basictask $task)
        {
            $task->set_fields_default('change_money_id','change_money_id',0, 'int');
            $task->set_fields_default('change_money_player_id','change_money_player_id',0, 'int');
            $task->set_fields_default('change_money_player_club_id','change_money_player_club_id',2, 'int');
            $task->set_fields_default('change_money_club_id','change_money_club_id',0, 'int');
            $task->set_fields_default('change_money_club_room_id','change_money_club_room_id',0, 'int');
            $task->set_fields_default('change_money_club_desk_no','change_money_club_desk_no',0, 'int');
            $task->set_fields_default('change_money_club_desk_id','change_money_club_desk_id',0, 'int');
            $task->set_fields_default('change_money_club_room_no','change_money_club_room_no',0, 'int');
            $task->set_fields_default('change_money_game_id','change_money_game_id',0, 'int');
            $task->set_fields_default('change_money_room_id','change_money_room_id',0, 'int');
            $task->set_fields_default('change_money_desk_no','change_money_desk_no',0, 'int');
            $task->set_fields_default('change_money_type','change_money_type',0, 'int');
            $task->set_fields_default('change_money_tax','change_money_tax',0, 'int');
            $task->set_fields_default('change_money_money_type','change_money_money_type',0, 'int');
            $task->set_fields_default('change_money_money_value','change_money_money_value',0, 'int');
            $task->set_fields_default('change_money_begin_value','change_money_begin_value',0, 'int');
            $task->set_fields_default('change_money_after_value','change_money_after_value',0, 'int');
            $task->set_fields_default('change_money_time','change_money_time',0, 'int');
            $task->set_fields_default('change_money_param','change_money_param','', 'string');
        }

        public static function feedback_fileds(basictask $task)
        {
            $task->set_fields_default('feedback_id','feedback_id',0, 'int');
            $task->set_fields_default('feedback_content','feedback_content','');
            $task->set_fields_default('feedback_player_id','feedback_player_id',0, 'int');
            $task->set_fields_default('feedback_create_time','feedback_create_time', time(), 'int');
        }


        /**
         * @param basictask $task
         *充值记录表
         */

        public static function  dpayinfolog_fileds(basictask $task)
        {
            $task->set_fields_default('pay_info_log_id','pay_info_log_id',0, 'int');
            $task->set_fields_default('pay_info_log_player_id','pay_info_log_player_id','int');
            $task->set_fields_default('pay_info_log_type','pay_info_log_type',0, 'int');
            $task->set_fields_default('pay_info_log_money','pay_info_log_money',0, 'int');
            $task->set_fields_default('pay_info_log_gett_money','pay_info_log_gett_money',0, 'int');
            $task->set_fields_default('pay_info_log_before_money','pay_info_log_before_money',0, 'int');
            $task->set_fields_default('pay_info_log_after_money','pay_info_log_after_money',0, 'int');
            $task->set_fields_default('pay_info_log_time','pay_info_log_time',0, 'int');

        }
        /**
         * 游戏局数
         * @param  basictask $task [description]
         * @return [type]          [description]
         */
        public static function gameround_fileds(basictask $task)
        {
            $task->set_fields_default('game_round_id','game_round_id',0);
            $task->set_fields_default('game_round_game_id','game_round_game_id',0);
            $task->set_fields_default('game_round_game_name','game_round_game_name','');
            $task->set_fields_default('game_round_num','game_round_num',0, 'int');
            $task->set_fields_default('game_round_channel_id','game_round_channel_id',0, 'int');
            $task->set_fields_default('game_round_coins','game_round_coins',0, 'int');
            $task->set_fields_default('game_round_day','game_round_day','');
            $task->set_fields_default('game_round_timestamp','game_round_timestamp',0, 'int');
            $task->set_fields_default('game_round_createtime','game_round_createtime',0, 'int');
        }
        /**
         * 充值累计表
         * @param  basictask $task [description]
         * @return [type]          [description]
         */
        public static function statisticstotal_fileds(basictask $task)
        {
            $task->set_fields_default('statistics_id','statistics_id',0);
            $task->set_fields_default('statistics_role_type','statistics_role_type',0);
            $task->set_fields_default('statistics_role_value','statistics_role_value',0);
            $task->set_fields_default('statistics_mode','statistics_mode',0);
            $task->set_fields_default('statistics_type','statistics_type',0);
            $task->set_fields_default('statistics_datetime','statistics_datetime','');
            $task->set_fields_default('statistics_timestamp','statistics_timestamp',0, 'int');
            $task->set_fields_default('statistics_sum','statistics_sum',0, 'int');
            $task->set_fields_default('statistics_money_rate','statistics_money_rate',0);
            $task->set_fields_default('statistics_update','statistics_update','');
            $task->set_fields_default('statistics_time','statistics_time',0);
        }


        /**
         * 推广奖励记录
         * @param  basictask $task [description]
         * @return [type]          [description]
         */
        public static function playerpromoteawardlog_fileds(basictask $task)
        {
            $task->set_fields_default('id','id',0);
            $task->set_fields_default('log_promoter_id','log_promoter_id',0);
            $task->set_fields_default('log_player_id','log_player_id',0);
            $task->set_fields_default('log_award','log_award',0);
            $task->set_fields_default('log_time','log_time',0);
            $task->set_fields_default('log_date','log_date','');
        }

        /**
         * @param basictask $task
         * 钻石对换列表
         */
        public static function playergoodsexchange_fileds(basictask $task)
        {
            $task->set_fields_default('goods_exchange_id','goods_exchange_id',0);
            $task->set_fields_default('goods_exchange_name','goods_exchange_name','');
            $task->set_fields_default('goods_exchange_diamond','goods_exchange_diamond',0);
            $task->set_fields_default('goods_exchange_type','goods_exchange_type',0);
            $task->set_fields_default('goods_exchange_get_price','goods_exchange_get_price',0);
            $task->set_fields_default('goods_exchange_status','goods_exchange_status','');
            $task->set_fields_default('goods_exchange_desc','goods_exchange_desc','');
            $task->set_fields_default('goods_exchange_time','goods_exchange_time','');
        }

        /**
         * @param basictask $task
         * 钻石对换记录
         */
        public static function playergoodsexchangelog_fileds(basictask $task)
        {
            $task->set_fields_default('goods_exchange_log_id','goods_exchange_log_id',0);
            $task->set_fields_default('goods_exchange_log_playerid','goods_exchange_log_playerid','');
            $task->set_fields_default('goods_exchange_log_exchange_id','goods_exchange_log_exchange_id',0);
            $task->set_fields_default('goods_exchange_log_money_value','goods_exchange_log_money_value',0);
            $task->set_fields_default('goods_exchange_log_begin_value','goods_exchange_log_begin_value',0);
            $task->set_fields_default('goods_exchange_log_after_value','goods_exchange_log_after_value','');
            $task->set_fields_default('goods_exchange_log_time','goods_exchange_log_time','');
        }

        /**
         * 玩家道具使用记录表(按天统计)
         * @param basictask $task
         * @author Zhanghui
         */
        public static function playerproplogday_fields(basictask $task)
        {
            $task->set_fields_default('log_day_id','log_day_id',0);
            $task->set_fields_default('log_day_player_id','log_day_player_id',0);
            $task->set_fields_default('log_day_prop_id','log_day_prop_id',0);
            $task->set_fields_default('log_day_prop_consumed_num','log_day_prop_consumed_num',0);
            $task->set_fields_default('log_day_prop_get_num','log_day_prop_get_num',0);
            $task->set_fields_default('log_day_prop_total_fee','log_day_prop_total_fee',0);
            $task->set_fields_default('log_day_date','log_day_date', date('Y-m-d'));
        }

        /**
         * 代理收益表
         * @param basictask $task
         * @author Zhanghui
         */
        public static function agentspromotersstatistics_fields(basictask $task)
        {
            $task->set_fields_default('statistics_id','statistics_id',0);
            $task->set_fields_default('statistics_agents_id','statistics_agents_id',0);
            $task->set_fields_default('statistics_agents_player_id','statistics_agents_player_id',0);
            $task->set_fields_default('statistics_super_agents_id','statistics_super_agents_id',0);
            $task->set_fields_default('statistics_from','statistics_from',0);
            $task->set_fields_default('statistics_from_value','statistics_from_value',0);
            $task->set_fields_default('statistics_type','statistics_type',0);
            $task->set_fields_default('statistics_money_type','statistics_money_type',0);
            $task->set_fields_default('statistics_money_type_rate','statistics_money_type_rate',0);
            $task->set_fields_default('statistics_data','statistics_data',0);
            $task->set_fields_default('statistics_income','statistics_income',0);
            $task->set_fields_default('statistics_my_data','statistics_my_data',0);
            $task->set_fields_default('statistics_my_income','statistics_my_income',0);
            $task->set_fields_default('statistics_share_money_low','statistics_share_money_low',0);
            $task->set_fields_default('statistics_share_money_high','statistics_share_money_high',0);
            $task->set_fields_default('statistics_status','statistics_status',0);
            $task->set_fields_default('statistics_time','statistics_time',0);
            $task->set_fields_default('statistics_date','statistics_date',0);
            $task->set_fields_default('statistics_add_time','statistics_add_time',0);

        }

        /**
         * 道具表
         * @param basictask $task
         * @author Zhanghui
         */
        public static function prop_fields(basictask $task)
        {
            $task->set_fields_default('prop_id','prop_id',0);
            $task->set_fields_default('prop_club_id','prop_club_id',-1);
            $task->set_fields_default('prop_game_id','prop_game_id',-1);
            $task->set_fields_default('prop_name','prop_name','');
            $task->set_fields_default('prop_category','prop_category', 0);
            $task->set_fields_default('prop_type','prop_type', 0);
            $task->set_fields_default('prop_apply_group','prop_apply_group', 0);
            $task->set_fields_default('prop_weight','prop_weight', 0);
            $task->set_fields_default('prop_num','prop_num', 0);
            $task->set_fields_default('prop_price','prop_price', 0);
            $task->set_fields_default('prop_vip_level','prop_vip_level', 0);
            $task->set_fields_default('prop_specific_config','prop_specific_config', '');
            $task->set_fields_default('prop_expire_time','prop_expire_time', 0);
            $task->set_fields_default('prop_created','prop_created', time());
            $task->set_fields_default('prop_modified','prop_modified', 0);
            $task->set_fields_default('prop_remark','prop_remark', '');
        }

        /**
         * 玩家道具使用记录表
         * @param basictask $task
         * @author Zhanghui
         */
        public static function playerproplog_fields(basictask $task)
        {
            $task->set_fields_default('log_id','log_id',0);
            $task->set_fields_default('log_prop_club_id','log_prop_club_id',-1);
            $task->set_fields_default('log_prop_game_id','log_prop_game_id',-1);
            $task->set_fields_default('log_player_id','log_player_id',0);
            $task->set_fields_default('log_prop_id','log_prop_id',0);
            $task->set_fields_default('log_prop_price','log_prop_price',0);
            $task->set_fields_default('log_action_type','log_action_type', 0);
            $task->set_fields_default('log_action_status','log_action_status', 0);
            $task->set_fields_default('log_action_prop_num_before','log_action_prop_num_before', 0);
            $task->set_fields_default('log_action_num','log_action_num', 0);
            $task->set_fields_default('log_action_prop_num_after','log_action_prop_num_after', 0);
            $task->set_fields_default('log_action_total_fee','log_action_total_fee', 0);
            $task->set_fields_default('log_to_other_player_id','log_to_other_player_id', 0);
            $task->set_fields_default('log_time','log_time', time());
            $task->set_fields_default('log_remark','log_remark', '');
        }

        /**
         * 消息信息表
         * @param basictask $task
         * @author Zhanghui
         */
        public static function message_fields(basictask $task)
        {
            $task->set_fields_default('message_id','message_id',0);
            $task->set_fields_default('message_type','message_type',0);
            $task->set_fields_default('message_apply_group','message_apply_group',0);
            $task->set_fields_default('message_apply_group_params','message_apply_group_params','');
            $task->set_fields_default('message_title','message_title','');
            $task->set_fields_default('message_content','message_content','');
            $task->set_fields_default('message_attach_type','message_attach_type', 0);
            $task->set_fields_default('message_attach_params','message_attach_params', '');
            $task->set_fields_default('message_create_time','message_create_time', 0);
            $task->set_fields_default('message_remark','message_remark', '');
        }

        /**
         * 玩家消息信息表
         * @param basictask $task
         * @author Zhanghui
         */
        public static function playermessage_fields(basictask $task)
        {
            $task->set_fields_default('player_message_id','player_message_id',0);
            $task->set_fields_default('player_message_message_id','player_message_message_id',0);
            $task->set_fields_default('player_message_player_id','player_message_player_id',0);
            $task->set_fields_default('player_message_is_read','player_message_is_read',0);
            $task->set_fields_default('player_message_is_delete','player_message_is_delete',0);
            $task->set_fields_default('player_message_is_attach_receive','player_message_is_attach_receive',0);
            $task->set_fields_default('player_message_create_time','player_message_create_time', 0);
            $task->set_fields_default('player_message_modify_time','player_message_modify_time', 0);
        }
        /**
         * 系统全局配置表 dc_config
         * @return [type] [description]
         */
        public static function config_fields(basictask $task)
        {
            $task->set_fields_default('config_id','config_id',0);
            $task->set_fields_default('config_name','config_name', '');
            $task->set_fields_default('config_desc','config_desc', '');
            $task->set_fields_default('config_type','config_type', 0);
            $task->set_fields_default('config_start_time','config_start_time',0);
            $task->set_fields_default('config_end_time','config_end_time', 0);
            $task->set_fields_default('config_config','config_config', '');
            $task->set_fields_default('config_status','config_status', 1);
            $task->set_fields_default('config_create_time','config_create_time',0);
        }

        /**
         * @param basictask $task
         *道具使用记录
         */
        public static function prop_log_fields(basictask $task)
        {
            $task->set_fields_default('prop_log_id','prop_log_id',0);
            $task->set_fields_default('prop_log_player_id','prop_log_player_id', '');
            $task->set_fields_default('prop_log_game_id','prop_log_game_id', '');
            $task->set_fields_default('prop_log_prop_id','prop_log_prop_id', 0);
            $task->set_fields_default('prop_log_coins','prop_log_coins',0);
            $task->set_fields_default('prop_log_take_time','prop_log_take_time', 0);
            $task->set_fields_default('prop_log_add_time','prop_log_add_time', '');
        }
    }
?>