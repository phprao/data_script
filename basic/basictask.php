<?php
	/*
	 *  @desc:   任务接口
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	interface basictask {
		public function on_data_task(basicmysql $db,basicmodel $model = null,$param,$default);
		public function on_redis_task(basicredis $redis,basicmodel $model = null,$param,$default);
		public function parse_model(basicmodel $model,array $data);
		public function format_model(basicmodel $model);
		public function format_response(basicmodel $model);
		public function set_action($action);
		public function set_fields_default($keys,$fields,$default,$type_value = null);
		public function select_task_database(basicserver $server);
		public function select_task_redis(basicserver $server);
	}
?>