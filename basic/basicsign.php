<?php
	/*
	 *  @desc:   基本数据签名
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	class basicsign extends basicsingleton{

		protected $m_keys = '292006258b4c09247ec02edce69f6a2d';

		public function sign_data($data) {
			$sign_value = '';
			if(is_null($data) || !is_array($data) || empty($data)) {
				return $sign_value;
			}
			ksort($data);
			$buff = '';
	        foreach ($data as $key => $value) {
	            if ($key != 'sign_value' && $value != '' && !is_array($value)) {
	                $buff .= $key . '=' . $value . '&';
	            }
	        }

	        $buff = trim($buff, '&');
	        //签名步骤二：在string后加入KEY
	        $string = $buff . '&key=' . $this->m_keys;
	        //签名步骤三：MD5加密
	        $string = md5($string);
	        //签名步骤四：所有字符转为大写
	        $sign_value = strtoupper($string);
			//var_dump($sign_value);
			return $sign_value;
		}

		//检测签名
		public function check_sign($data,$sign_value) {

			$sign = $this->sign_data($data);
			if($sign != $sign_value) {
				return false;
			}

			return true;
		}
	}
?>