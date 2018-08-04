<?php
	/*
	 *  @desc:   记录货币消耗数据 logic
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class record_recordmoneydatahepler extends basicsingleton{

		//redis to db
		public function record_data(basicdi $app,$value) {
			$trans = new basictransactiontask();
        	$app->m_server->process_database($trans,null,null,null);
        	$ok = false;
        	$result = array();
        	$result['code'] = 0;
        	$result['desc'] = '';
        	do
        	{
        		$player_id = $value['change_money_player_id'];
        		$money_time = $value['change_money_time'];
        		$model = new moneychangemodel($app);
				$model->insert('change_money_player_id',$player_id);
				$model->insert('change_money_time',$money_time);


				$tips = " player_id($player_id) money_time($money_time)";

				if(!$model->get_redis_model() ) {
					$result['code'] = 1;
					$result['desc'] = 'money record get_redis_model fail'.$tips;
					break;
				}

				if(!$model->check_data()) {
					$result['code'] = 2;
					$result['desc'] = 'money record get_redis_model fail'.$tips;
					break;
				}
				$ret = $model->save_data_model();
				if($ret == 0 ) {
					$result['code'] = 3;
					$result['desc'] = 'money record save_data_model fail'.$tips;
					break;
				}

				if(!$model->delete_redis_model()) {
					$result['code'] = 4;
					$result['desc'] = 'money record delete_redis_model fail'.$tips;
					break;
				}
        		$ok = true;
        	}while(false);
        	if($ok) {
        		$trans->commit();
        	}else {
        		$trans->rollback();
        	}
        	return $result;
		}

		//all redis to db 
		public function record_money_all(basicdi $app) {
			
			for ($i=0; $i < 10; $i++) { 
				$this->record_money_data($app,$i);
			}
			
		}


		//add db queue
		public function add_money_change_model_queue_record(moneychangemodel $model) {
			if(is_null($model)) return false;
			if(!$model->check_data()) {
				return false;
			}
			$ret = $model->save_data_model();
			return $ret;
		}

		public function add_pay_money_change_queue_record(basicdi $app,$data) {
			$model = new moneychangemodel($app);
			$model->insert('change_money_player_id',$data['change_money_player_id']);
			$model->insert('change_money_type',1);
			$model->insert('change_money_money_type',1);
			$model->insert('change_money_money_value',$data['change_money_money_value']);
			$model->insert('change_money_money_value',$data['change_money_money_value']);
		}


		

		protected function record_money_data(basicdi $app,$db_index) {
			$task = new mainscriptredistask();
			$task->set_action('scan_money_keys');
			$task->select_redis_database($db_index);

			$list = array();
			$list['iter'] = null;
			$list['list'] = array();
			do
			{
				$list = $app->m_server->process_redis($task,$list['iter'],null,null);
				//var_dump($list);
				$this->record_list_data($app,$list['list']);

			}while($list['iter'] >0);
		}

		protected function record_list_data(basicdi $app,$list) {
			foreach ($list as $value) {
				$this->save_data($app,$value);
			}
		}

		protected function save_data(basicdi $app,$item) {
			//var_dump($value);
			$list = explode(':',$item);
			if(count($list) < 4) return ;
			$player_id = $list[2];
			$time_value = $list[3];
			//var_dump($player_id);
			//var_dump($time_value);
			$cur_time = time();
			$use_time = $cur_time - $time_value;
			if( $use_time < 300) {
				//var_dump($use_time);
				return;
			}
			//var_dump($item);
			$value['change_money_player_id'] = $player_id;
			$value['change_money_time'] = $time_value;
			$result = $this->record_data($app,$value);
		}
	}
?>