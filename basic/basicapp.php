<?php

/**
 * Class basicapp
 * @property basicapp $app
 */
class basicapp
{
    protected $m_time_out = 1;
    protected $m_memory_out = 0;//
    protected $m_begin_time = 0;

    public function __construct()
    {
        $this->m_begin_time = $this->tick_time();
        $this->m_memory_out = (8 * 1024 * 1024);
        $this->m_time_out = 1;
    }

    public function __destruct()
    {
    }

    public function run()
    {
        $result = false;
        try {
            do {
                $action_name = basichelper::get_action_name();
                $version = basichelper::get_action_version();
                $action_path = 'action' . DIRECTORY_SEPARATOR . $version;
                //var_dump($action_name);
                $app = app();
                $app->m_loader->add_dirs($action_path);

                if (!$app->m_loader->load($action_name)) {
                    echo basichelper::basic_make_response_data();
                    break;
                }
                $debug = true;//$app->is_debug();
                $cmd = new $action_name($debug);
                $cmd->action($app);
                $result = true;
                unset($cmd);
            } while (false);
        } catch (Exception $e) {
            BASIC_SYS_LOG_WARNING('system','basicapp','%s',$e->getMessage());
        } finally {
            $this->check_use_memory();
            $this->check_use_time();
        }
        return $result;
    }

    protected function check_use_memory()
    {
        $use_memry = memory_get_usage(true);
        if ($use_memry > $this->m_memory_out) {
            $msg = "memory out  (" . $use_memry . ")";
            $this->printf_action_info($msg);
        }
    }

    protected function tick_time()
    {
        $mt_time = explode(' ', microtime());
        $time_value = $mt_time[1] + $mt_time[0];
        return $time_value;
    }

    protected function check_use_time()
    {
        $end_time = $this->tick_time();
        $use_time = $end_time - $this->m_begin_time;

        if ($use_time >= $this->m_time_out) {
            $msg = "time out (" . $use_time . ")";
            $this->printf_action_info($msg);
        }
    }


    protected function printf_action_info($msg)
    {
        $action_name = basichelper::get_action_name();
        $version = basichelper::get_action_version();
        BASIC_LOG_WARNING('basicapp', 'xxm_php: action(%s) version(%s) %s', $action_name, $version, $msg);
    }
}
