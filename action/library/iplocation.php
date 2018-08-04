<?php
/**
 * ip归属地定位
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/15
 * Time: 11:53
 * @author Zhanghui
 * @desc :
 *      全球 IPv4 地址归属地数据库(17MON.CN 版)
 *      高春辉(pAUL gAO) <gaochunhui@gmail.com>
 *      Build 20141009 版权所有 17MON.CN
 *      (C) 2006 - 2014 保留所有权利
 *      请注意及时更新 IP 数据库版本
 *      数据问题请加 QQ 群: 346280296
 *      Code for PHP 5.3+ only
 */

header("Content-type: text/html; charset=utf-8");
class iplocation
{

    private static $_s_fp     = NULL;
    private static $_s_offset = NULL;
    private static $_s_index  = NULL;

    private static $_s_cached = array();
    private static $_s_default_return = 'N/A';

    /**
     * 查找IP定位信息
     * @param $ip : IP地址
     * @return mixed|string
     * @author Zhanghui
     */
    public static function s_find($ip)
    {

        try {
            $nip = self::_s_verify_ip($ip);
        } catch (Exception $e) {
            BASIC_EXCEPTION_HANDLER($e);
            return self::$_s_default_return;
        }

        $ipdot = explode('.', $nip);
        if ($ipdot[0] < 0 || $ipdot[0] > 255 || count($ipdot) !== 4) {
            return self::$_s_default_return;
        }

        if (isset(self::$_s_cached[$nip]) === TRUE) {
            return self::$_s_cached[$nip];
        }

        if (self::$_s_fp === NULL) {
            try {
                self::init();
            } catch (Exception $e) {
                BASIC_EXCEPTION_HANDLER($e);
                return self::$_s_default_return;
            }
        }

        $nip2 = pack('N', ip2long($nip));

        $tmp_offset = (int)$ipdot[0] * 4;
        $start      = unpack('Vlen', self::$_s_index[$tmp_offset] . self::$_s_index[$tmp_offset + 1] . self::$_s_index[$tmp_offset + 2] . self::$_s_index[$tmp_offset + 3]);

        $_s_index_offset = $_s_index_length = NULL;
        $max_comp_len = self::$_s_offset['len'] - 1024 - 4;
        for ($start = $start['len'] * 8 + 1024; $start < $max_comp_len; $start += 8) {
            if (self::$_s_index{$start} . self::$_s_index{$start + 1} . self::$_s_index{$start + 2} . self::$_s_index{$start + 3} >= $nip2) {
                $_s_index_offset = unpack('Vlen', self::$_s_index{$start + 4} . self::$_s_index{$start + 5} . self::$_s_index{$start + 6} . "\x0");
                $_s_index_length = unpack('Clen', self::$_s_index{$start + 7});
                break;
            }
        }

        if ($_s_index_offset === NULL) {
            return 'N/A';
        }

        fseek(self::$_s_fp, self::$_s_offset['len'] + $_s_index_offset['len'] - 1024);

        self::$_s_cached[$nip] = explode("\t", fread(self::$_s_fp, $_s_index_length['len']));

        return self::$_s_cached[$nip];
    }

    /**
     * 验证IP参数
     * @param $ip : 要验证的IP
     * @return mixed
     * @throws Exception
     * @author Zhanghui
     */
    private static function _s_verify_ip($ip)
    {
        if (empty($ip)) {
            throw new Exception('IP不能为空');
        }

        $filter_val = filter_var($ip, FILTER_VALIDATE_IP,FILTER_FLAG_IPV4);
        if (!$filter_val) {
            throw new Exception('IP格式非法');
        }

        return $filter_val;
    }

    /**
     * 初始化 IP 定位信息库
     * @throws Exception
     * @author Zhanghui
     */
    private static function init()
    {
        if (self::$_s_fp === NULL) {
            $file_path = __DIR__.DIRECTORY_SEPARATOR.'iplocation/17monipdb.dat';
            self::$_s_fp = fopen($file_path, 'rb');

            if (self::$_s_fp === FALSE) {
                throw new Exception('Invalid 17monipdb.dat file!');
            }

            self::$_s_offset = unpack('Nlen', fread(self::$_s_fp, 4));
            if (self::$_s_offset['len'] < 4) {
                throw new Exception('Invalid 17monipdb.dat file!');
            }

            self::$_s_index = fread(self::$_s_fp, self::$_s_offset['len'] - 4);
        }
    }

    public function __destruct()
    {
        if (self::$_s_fp !== NULL) {
            @fclose(self::$_s_fp);
        }
    }
}