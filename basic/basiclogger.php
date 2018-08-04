<?php
     /**
      * Simple logger class based on a similer class created by 
      * Darko Bunic (http://www.redips.net/php/write-to-log-file/)     
      * Does simple logging to a specified file. See https://bitbucket.org/huntlyc/simple-php-logger for more details.
      *
      * @package default
      * @author Huntly Cameron <huntly.cameron@gmail.com>
      **/
	define('BASIC_LEVEL_FATAL', 0);
	define('BASIC_LEVEL_ERROR', 1);
	define('BASIC_LEVEL_WARN', 2);
	define('BASIC_LEVEL_TRACE', 3); 
	define('BASIC_LEVEL_INFO', 4); 
	define('BASIC_LEVEL_DEBUG', 5);  
    class basiclogger{
        /**
         * log_file - the log file to write to
         *  
         * @var string
         **/        
        private $log_file;
		private $debug_log_file;
        private $root_path;
		
		private $level = BASIC_LEVEL_WARN;
        /**
         * Constructor
         * @param String logfile - [optional] Absolute file name/path. Defaults to ubuntu apache log.
         * @return void
         **/        
        function __construct($log_file = "/var/log/apache2/error.log",$debug = null) {
			$ip = $_SERVER["REMOTE_ADDR"];
            $this->root_path = $log_file;
			if($ip=='127.0.0.1'){
				$file_url = $log_file.'127_0_0_1-'.date('Y-m-d',time()).".log";
			}else{
				$file_url = $log_file.date('Y-m-d',time())."_".date('H',time()).".log";
			}
			//$file_url = $log_file.date('Y-m-d',time()).".log";
			//var_dump($file_url);
			//$file_name = fopen("","w");
            $this->log_file = $file_url;

            if(!file_exists($file_url)){ //Attempt to create log file                
                touch($file_url);
            }

            //Make sure we'ge got permissions
            if(!(is_writable($file_url) || $this->win_is_writable($file_url))){   
                //Cant write to file,
                throw new Exception("LOGGER ERROR: Can't write to log", 1);
            }

            if(!is_null($debug) && $debug) {
                $this->level = BASIC_LEVEL_DEBUG;
            }
        }
        
        public function __destruct() {
            $this->level = BASIC_LEVEL_FATAL;
        }

        public function clear_time_out() {
            $path_parts = pathinfo($this->root_path);
            $path = $path_parts['dirname'].'/';
            $this->del_dir($path);
        }

        private function del_dir($root_path) {
            $my_dir = dir($root_path);
            if(!$my_dir) {
                return false;
            }
            while($file = $my_dir->read()) {
                $file_name = $root_path.$file;
                if(is_dir($file_name) && $file != '.' && $file != '..') {
                    chmod($file_name,0777);
                    $this->del_dir($file_name.'/');
                }elseif(is_file($file_name)) {
                    $ext_name = pathinfo($file_name, PATHINFO_EXTENSION);
                    if(strcasecmp('log',$ext_name) != 0) continue;
                    $file_time = filectime($file_name);
                    $cur_time = time();
                    $time_out = 3600*24*15;
                    if($this->level > BASIC_LEVEL_WARN) {
                        $time_out = 3600*24*7;
                    }
                    if($cur_time - $file_time >  $time_out) {
                        chmod($file_name,0777);
                        unlink($file_name);
                        //var_dump($file_name);
                    }
                }
            }
            $my_dir->close();
        }
		
		public function set_level($status) {
			$this->level = $status;
		}
        
        /**
         * debug - Log Debug
         * @param String tag - Log Tag
         * @param String message - message to spit out
         * @return void
         **/      
        public function debug($tag, $message){
            $this->writeToLog("DEBUG", BASIC_LEVEL_DEBUG, $tag, $message);
        }
		
        /**
         * info - Log Info
         * @param String tag - Log Tag
         * @param String message - message to spit out
         * @return void
         **/        
        public function info($tag, $message){
            $this->writeToLog("INFO", BASIC_LEVEL_INFO, $tag, $message);            
        }
		
		/**
         * info - Log Info
         * @param String tag - Log Tag
         * @param String message - message to spit out
         * @return void
         **/        
        public function trace($tag, $message){
            $this->writeToLog("TRACE", BASIC_LEVEL_TRACE, $tag, $message);            
        }
		
        /**
         * warn - Log Warning
         * @param String tag - Log Tag
         * @param String message - message to spit out
         * @author 
         **/        
        public function warn($tag, $message){
            $this->writeToLog("WARN", BASIC_LEVEL_WARN, $tag, $message);            
        }
		
        /**
         * error - Log Error
         * @param String tag - Log Tag
         * @param String message - message to spit out
         * @author 
         **/        
        public function error($tag, $message){
            $this->writeToLog("ERROR", BASIC_LEVEL_ERROR, $tag, $message);            
        }
		
        /**
         * fatal - Log Fatal
         * @param String tag - Log Tag
         * @param String message - message to spit out
         * @author 
         **/        
        public function fatal($tag, $message){
            $this->writeToLog("FATAL", BASIC_LEVEL_FATAL, $tag, $message);            
        }
		
        /**
         * writeToLog - writes out timestamped message to the log file as 
         * defined by the $log_file class variable.
         *
         * @param String status - "INFO"/"DEBUG"/"ERROR" e.t.c.
         * @param String tag - "Small tag to help find log entries"
         * @param String message - The message you want to output.
         * @return void
         **/        
        private function writeToLog($status, $level, $tag, $message) {
			if ($level <= $this->level) {
	            $date = date('[Y-m-d H:i:s]');
	            $msg = "$date: [$status][$tag] - $message" . PHP_EOL;
	            file_put_contents($this->log_file, $msg, FILE_APPEND);
			}
        }

        //Function lifted from wordpress
        //see: http://core.trac.wordpress.org/browser/tags/3.3/wp-admin/includes/misc.php#L537
        private function win_is_writable( $path ) {
            /* will work in despite of Windows ACLs bug
             * NOTE: use a trailing slash for folders!!!
             * see http://bugs.php.net/bug.php?id=27609
             * see http://bugs.php.net/bug.php?id=30931
             */
			 
            if ( $path[strlen( $path ) - 1] == '/' ) // recursively return a temporary file path
                return win_is_writable( $path . uniqid( mt_rand() ) . '.tmp');
            else if ( is_dir( $path ) )
                return win_is_writable( $path . '/' . uniqid( mt_rand() ) . '.tmp' );
            
            // check tmp file for read/write capabilities
            $should_delete_tmp_file = !file_exists( $path );
            $f = @fopen( $path, 'a' );
            if ( $f === false )
                return false;
            
            fclose( $f );

            if ( $should_delete_tmp_file )
                unlink( $path );

            return true;
        }        
    }
?>