<?php
/**
 +---------------------------------------------------------- 
 * date: 2018-02-09 16:00:36
 +---------------------------------------------------------- 
 * author: Raoxiaoya
 +---------------------------------------------------------- 
 * describe: // 充值金额累计处理
 +---------------------------------------------------------- 
 */

class chargesumtaskmanager
{
	protected $logtag = 'playerstatistical';
	protected $app;
	protected $record;

	public function stat_logic(basicdi $app, basicmodelimpl $record, $param = null) {
		$this->app = $app;
		$this->money_task = new moneychangedatatask();
		$this->record = $this->money_task->format_model($record);

		// 充值累计-小时
    	if(!$this->charge_sum($param,1)) {
			return false;
		}
		// 充值累计-天
    	if(!$this->charge_sum($param,2)) {
			return false;
		}

		return true;

	}

	public function charge_sum($param,$type){
		$pay_model = new payrecordlogmodel($this->app);
		if($type == 1){
			$time = strtotime(date('Y-m-d H:00:00',$this->record['change_money_time']));
			$date = date('Y-m-d H',$this->record['change_money_time']);
		}elseif($type == 2){
			$time = strtotime(date('Y-m-d',$this->record['change_money_time']));
			$date = date('Y-m-d',$this->record['change_money_time']);
		}
		$orders = $this->record['change_money_param'] ? json_decode($this->record['change_money_param'],true) : array();
		if(!empty($orders) && isset($orders['price_value'])){
			$money = $orders['price_value'];
		}else{
			$money = 0;
		}
		
		$arr = array(
			'record_type'        =>$type,
			'record_timestamp'   =>$time
		);

		// 先查找是否存在
		$log = $pay_model->get_one_log($arr);
		$arr['record_sum'] = $money;
		if($log){
			$re = $pay_model->update_pay_sum($arr);
		}else{
			$arr['record_datetime'] = $date;
			$arr['record_sum']      = $money;
			$arr['record_time']     = time();
			$re = $pay_model->save_data_model($arr);
		}

		return $re;
	}
}