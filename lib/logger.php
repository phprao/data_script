<?php
	define('LEVEL_FATAL', 0);
	define('LEVEL_ERROR', 1);
	define('LEVEL_WARN', 2);
	define('LEVEL_INFO', 3); 
	define('LEVEL_DEBUG', 4);  
    class logger{
        private $log_file;
		private $debug_log_file;
		private $level = LEVEL_DEBUG;
       
        function __construct() {
            $dir = dirname(dirname(__FILE__)).'/logs/scripts';
			$this->log_file = $dir . '/' . date('Y-m-d',time()) . ".log";
            if(!is_dir($dir)){
               mkdir($dir, 0777, true); 
            }
            if(!file_exists($this->log_file)){               
                touch($this->log_file);
            }
            if(!(is_writable($this->log_file) || $this->win_is_writable($this->log_file))){   
                throw new Exception("LOGGER ERROR: Can't write to log", 1);
            }
        }
		
		public function set_level($status) {
			$this->level = $status;
		}
     
        public function debug($tag, $message){
            $this->writeToLog("DEBUG", LEVEL_DEBUG, $tag, $message);
        }
       
        public function info($tag, $message){
            $this->writeToLog("INFO", LEVEL_INFO, $tag, $message);            
        }
		
      
        public function warn($tag, $message){
            $this->writeToLog("WARN", LEVEL_WARN, $tag, $message);            
        }
		       
        public function error($tag, $message){
            $this->writeToLog("ERROR", LEVEL_ERROR, $tag, $message);            
        }
		       
        public function fatal($tag, $message){
            $this->writeToLog("FATAL", LEVEL_FATAL, $tag, $message);            
        }
       
        private function writeToLog($status, $level, $tag, $message) {
			if ($level <= $this->level) {
	            $date = date('[Y-m-d H:i:s]');
	            $msg = "$date: [$tag][$status] - $message" . PHP_EOL;
	            file_put_contents($this->log_file, $msg, FILE_APPEND);
			}
        }

        private function win_is_writable( $path ) {
            if ( $path[strlen( $path ) - 1] == '/' ) 
                return win_is_writable( $path . uniqid( mt_rand() ) . '.tmp');
            else if ( is_dir( $path ) )
                return win_is_writable( $path . '/' . uniqid( mt_rand() ) . '.tmp' );
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