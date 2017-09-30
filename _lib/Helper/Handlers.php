<?php
	class Handlers {
	    public static $instance = null;
	    public function onError($type, $message, $file = null, $line = null, $context = null) {
	        $backtrace = debug_backtrace();
	        array_shift($backtrace);
	 
	        $this->notify($message, $backtrace);
	        return true;
	    }

	    public function onException(Exception $exception) {
	        $this->notify($exception->getMessage().' in '.$exception->getFile().' on line '.$exception->getLine(),$exception->getTrace());
	    }

	    public function onShutdown() {
	        if (!$error = error_get_last()) {
	            return;
	        }
	 
	        // Build a fake backtrace, so we at least can show where we came from.
	        $backtrace = array(
	            array(
	                'file' => $error['file'],
	                'line' => $error['line'],
	                'function' => '',
	                'args' => array(),
	            )
	        );
	        var_dump($backtrace);
	 
	        $this->notify('[Improper Shutdown] '.$error['message'], $backtrace);
	    }

	    public function notify($message,$backtrace) {
	        $notice = new stdclass();
	        $notice->message = $message;
	        $notice->backtrace = $backtrace;
	    }

	    public static function start(){
	        if ( !isset(self::$instance)) {
	            self::$instance = new self($client, $notifyOnWarning);
	 
	            set_error_handler(array(self::$instance, 'onError'));
	            set_exception_handler(array(self::$instance, 'onException'));
	            register_shutdown_function(array(self::$instance, 'onShutdown'));
	        }
	 
	        return self::$instance;
	    }
	}
?>