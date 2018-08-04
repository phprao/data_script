<?php
	class weixinorder extends basicsingleton {

		protected $m_url = "https://api.mch.weixin.qq.com/pay/unifiedorder";

		public function unified_order(basicdi $app,$os_type,$order_info) {
			$result = array();
			$result['code'] = 1;
			$result['desc'] = 'unknow';
			$result['data'] = null;

			do
			{
				//todo 微信支付方式
            	$weixin_config = $app->m_config->get('weixin_info', '');
            	if(is_null($weixin_config)){
            		$result['code'] = 2;
					$result['desc'] = 'weixin weixin_info is null';
					break;
            	}

				$wx_result = $this->request_order_weixin($app,$order_info,$weixin_config);
				if(is_null($wx_result)) {
					$result['code'] = 3;
					$result['desc'] = 'weixin request oreder fail 1 ';
					break;
				}
				if(!($wx_result['return_code'] == 'SUCCESS' && $wx_result['result_code'] == 'SUCCESS')) {
					$result['code'] = 4;
					$result['desc'] = 'weixin request oreder return_code != SUCCESS ';
					break;
				}

				$key = $weixin_config['weixin_pay_key'];
				$result['data'] = $this->make_sign_data($os_type,$wx_result,$key);
				$result['code'] = 0;
				$result['desc'] = 'ok';
			}while(false);
			
			return $result;
		}

		protected function make_sign_data($os_type,$wx_result,$key) {
			$wx_result['pay_channel'] = 1;
            $wx_result['timestamp'] = time();

			$paramData = array();
			if ($os_type == 1) {
                // 安卓app
                $paramData = array(
                    'appid' => $wx_result['appid'],
                    'partnerid' => $wx_result['mch_id'],
                    'prepayid' => $wx_result['prepay_id'],
                    'package' => 'Sign=WXPay',
                    'noncestr' => $wx_result['nonce_str'],
                    'timestamp' => $wx_result['timestamp'],
                );
            } elseif ($os_type == 2) {
                // 苹果app
                $paramData = array(
                    'partnerid' => $wx_result['mch_id'],
                    'prepayid' => $wx_result['prepay_id'],
                    'package' => 'Sign=WXPay',
                    'noncestr' => $wx_result['nonce_str'],
                    'timestamp' => $wx_result['timestamp'],
                );
            }
            $sign = $this->MakeSign($paramData, $key);
            $returnData = array(
                'appid' => $wx_result['appid'],
                'partnerid' => $wx_result['mch_id'],
                'prepay_id' => $wx_result['prepay_id'],
                'package' => 'Sign=WXPay',
                'nonce_str' => $wx_result['nonce_str'],
            );
            $returnData['pay_channel'] = 1;
            $returnData['timestamp'] = $wx_result['timestamp'];
            $returnData['sign'] = $sign;
            return $returnData;
		}

		protected function request_order_weixin(basicdi $app,$order_info,$weixin_config) {
			

            $appid = $weixin_config['weixin_app_id'];//'wx70b70386db1a5571';
            $mch_id = $weixin_config['weixin_pay_mch_id'];//'1405654502';
            $key = $weixin_config['weixin_pay_key'];//'a0CJ8HPZAgMBAAECgYAuSfxGTlXGEcih';
            $notify_url = $weixin_config['weixin_notify_url'];
            $nonce_str = $this->getNonceStr();

            $body = '幸运游戏中心';
            $out_trade_no = $order_info['order_orderno'];
            $total_fee = $order_info['goods_price'];
            $spbill_create_ip = $_SERVER['REMOTE_ADDR'];
            $trade_type = 'APP';

            $param = array(
                'appid' => $appid,
                'mch_id' => $mch_id,
                'nonce_str' => $nonce_str,
                'body' => $body,
                'out_trade_no' => $out_trade_no,
                'total_fee' => $total_fee,
                'spbill_create_ip' => $spbill_create_ip,
                'notify_url' => $notify_url,
                'trade_type' => $trade_type
            );
            $sign = $this->MakeSign($param, $key);
            $param['sign'] = $sign;

            $xml = $this->ToXml($param);
            $url = $this->m_url;

            $response = $this->postXmlCurl($xml, $url);

            $wx_result = $this->FromXml($response);

            return $wx_result;
		}

		public function getNonceStr($length = 32) {
	        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
	        $str = "";
	        for ($i = 0; $i < $length; $i++) {
	            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	        }
	        return $str;
    	}

    	/**
	     * 生成签名
	     * @return
	     */
    	public function MakeSign($params, $key) {
	        //签名步骤一：按字典序排序参数
	        ksort($params);
	        $buff = "";
	        foreach ($params as $k => $v) {
	            if ($k != "sign" && $v != "" && !is_array($v)) {
	                $buff .= $k . "=" . $v . "&";
	            }
	        }
	        $buff = trim($buff, "&");
	        //签名步骤二：在string后加入KEY
	        $string = $buff . "&key=" . $key;
	        //签名步骤三：MD5加密
	        $string = md5($string);
	        //签名步骤四：所有字符转为大写
	        $result = strtoupper($string);
	        return $result;
	    }

	    /**
	     * 输出xml字符
	     * @throws WxPayException
	     **/
	    public function ToXml($param) {
	        $xml = "<xml>";
	        foreach ($param as $key => $val) {
	            if (is_numeric($val)) {
	                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
	            } else {
	                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
	            }
	        }
	        $xml .= "</xml>";
	        return $xml;
	    }

	    /**
	     * 以post方式提交xml到对应的接口url
	     *
	     * @param string $xml 需要post的xml数据
	     * @param string $url url
	     * @param bool $useCert 是否需要证书，默认不需要
	     * @param int $second url执行超时时间，默认30s
	     * @throws WxPayException
	     */
	    protected function postXmlCurl($xml, $url, $second = 30) {
	        $ch = curl_init();
	        //设置超时
	        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
	        curl_setopt($ch, CURLOPT_URL, $url);

	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	        //curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
	        //curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
	        //设置header
	        curl_setopt($ch, CURLOPT_HEADER, FALSE);
	        //要求结果为字符串且输出到屏幕上
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	        //post提交方式
	        curl_setopt($ch, CURLOPT_POST, TRUE);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
	        //运行curl
	        $data = curl_exec($ch);
	        //返回结果
	        if ($data) {
	            curl_close($ch);
	            return $data;
	        } else {
	            $error = curl_errno($ch);
	            curl_close($ch);
	            throw new Exception("curl出错，错误码:$error");
	        }
	    }

	    /**
	     * 将xml转为array
	     * @param string $xml
	     * @throws WxPayException
	     */
	    public function FromXml($xml) {
	        if (!$xml) {
	            throw new Exception("xml数据异常！");
	        }
	        //将XML转为array
	        //禁止引用外部xml实体
	        libxml_disable_entity_loader(true);
	        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	        return $data;
	    }
	}
?>