<?php
define('SRV_TYPE_RELEASE', 0);
define('SRV_TYPE_DEBUG', 1);
define('SRV_TYPE_TEST', 2);

/**
 * Class basicdi
 * @property  basicserver $m_server
 */
class basicdi implements ArrayAccess
{
    protected static $m_instance = null;
    protected $m_data = array();
    protected $m_hit_times = array();
    protected $m_debug = false;
    protected $m_host_name = 'localhost';

    public function __construct()
    {
        $this->check_srv_type();
    }

    public static function get_instance()
    {
        if (static::$m_instance == null) {
            static::$m_instance = new basicdi();
            static::$m_instance->init();
        }
        return static::$m_instance;
    }

    public function init()
    {
        $path_log = PROJECT_ROOT . '/logs';
        if (!file_exists($path_log)) return false;
        $log_dir = $path_log . '/logic';
        if (!file_exists($log_dir)) mkdir($log_dir,0777);//mkdir($log_dir,0777,true); true 递归
         $log_dir = $path_log . '/action';
        if (!file_exists($log_dir)) mkdir($log_dir,0777);
         $log_dir = $path_log . '/data';
        if (!file_exists($log_dir)) mkdir($log_dir,0777);
         $log_dir = $path_log . '/redis';
        if (!file_exists($log_dir)) mkdir($log_dir,0777);
         $log_dir = $path_log . '/system';
        if (!file_exists($log_dir)) mkdir($log_dir,0777);
        //var_dump($dir);
    }

    public function set($key, $value)
    {
        //$this->reset_hit($key);
        $this->m_data[$key] = $value;

        return $this;
    }

    public function get($key, $default = null)
    {
        /*
        if(!isset($this->data[$key])) {
            $this->data[$key] = $default;
        }

        $this->record_hit_times($key);

        if($this->is_first_hit($key)) {
            $this->data[$key] = $this->init_service($this->data[$key]);
        }

        return $this->data[$key];
        */
        if (!isset($this->data[$key])) {
            return $default;
        }
        return $this->data[$key];
    }

    public function __call($name, $arguments)
    {
    }


    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset, NULL);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function check_srv_type()
    {
        $host = $_SERVER['HTTP_HOST'];
        $this->m_host_name = $host;
        $srv_type = SRV_TYPE_RELEASE;
        switch ($host) {
            case '127.0.0.1':
            case '192.168.1.210':
            case 'localhost':
            case '192.168.1.160':
                $srv_type = SRV_TYPE_DEBUG;
                $this->m_debug = true;
                break;
            case 'apiu3d.cc':
                $srv_type = SRV_TYPE_DEBUG;
                $this->m_debug = true;
                break;
            case '118.89.65.247':
            case 'test.dcyouxi.com':
            case 'kingdom.dcgames.cn':
            case 'jinshi.dcgames.cn':
            case 'xingyun.dcgames.cn':
                $srv_type = SRV_TYPE_TEST;
                $this->m_debug = true;
            default:
                # code...
                break;
        }
        if ($this->m_debug) {
            error_reporting(E_ALL);
        }
        return $srv_type;
    }

    public function is_debug()
    {
        return $this->m_debug;
    }

    public function get_host_name() {
        return $this->m_host_name;
    }

    public function get_client_ip()
    {
        $unknown = 'unknown';
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)) {
            $ip = getenv('REMOTE_ADDR');
        } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '';
        }

        return $ip;
    }

    public function log_time_out()
    {
        $result = false;
        $file_name = 'logs/data.config';
        do {
            if (!file_exists($file_name)) {
                $result = true;
                break;
            }
            $last_time = filectime($file_name);
            $cur_time = time();
            if ($cur_time - $last_time < 3600 * 24) {
                break;
            }

            $my_file = fopen($file_name, "r");
            $last_time = (int)fread($my_file, filesize($file_name));
            fclose($my_file);
            if ($cur_time - $last_time > 3600 * 24) {
                $result = true;
                break;
            }
        } while (false);
        if ($result) {
            $my_file = fopen($file_name, "w");
            fwrite($my_file, time());
            fclose($my_file);
        }
        return $result;
    }

    public function clear_log_data()
    {
        $time_out = $this->log_time_out();
        //var_dump($time_out);
        if (!$time_out) return false;

        if (!is_null($this->m_logger)) {
            $this->m_logger->clear_time_out();
        }
        if (!is_null($this->m_data_logger)) {
            $this->m_data_logger->clear_time_out();
        }
        if (!is_null($this->m_redis_logger)) {
            $this->m_redis_logger->clear_time_out();
        }
        if (!is_null($this->m_action_logger)) {
            $this->m_action_logger->clear_time_out();
        }
        if (!is_null($this->m_system_logger)) {
            $this->m_system_logger->clear_time_out();
        }

        return true;
    }
}
