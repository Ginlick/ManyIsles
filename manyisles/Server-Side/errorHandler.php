<?php
//error handling (normally called by allBase)
//dependencies: allBase

if (!class_exists("errorHandler")) {
  class errorHandler {
    use allBase;
    public $testMode = false;
    private $mailer;

    function __construct() {
      $this->construct();

      if (file_exists(dirname(dirname($_SERVER['DOCUMENT_ROOT']))."/test.txt")) {
        $this->testMode = true;
      }

      $this->mailer = $this->addMailer();
    }

    function redirect($e) {
      $now = date('d-M-Y H:i:s');
      $type = $this->format_error_type($e['type']);
      $message = "$type: {$e['message']} in {$e['file']} on line {$e['line']}";
      $fullmessage = "[$now] ".$message;
      //echo "<br><br>$message";//exit;

      switch ($e['type']) {
          /* We'll ignore these errors.  They're only here for reference. */
          case E_WARNING:
          case E_NOTICE:
          case E_CORE_WARNING:
          case E_COMPILE_WARNING:
          case E_USER_WARNING:
          case E_USER_NOTICE:
          case E_STRICT:
          case E_RECOVERABLE_ERROR:
          case E_DEPRECATED:
          case E_USER_DEPRECATED:
          case E_ALL:
            $this->log_error($e, $message, 1);
            break;
          /* Redirect to "oops" page on the following errors. */
          case 0: /* Exceptions return zero for type */
          case E_ERROR:
          case E_PARSE:
          case E_CORE_ERROR:
          case E_COMPILE_ERROR:
          case E_USER_ERROR:
            $this->log_error($e, $message, 3);
            if (!$this->testMode){
              echo "<script>window.location.replace('/Code/error');</script>";
            }
            die();
        }
    }

    function format_error_type($type) {
        switch($type) {
            case 0:
                return 'Uncaught exception';
            case E_ERROR: /* 1 */
                return 'E_ERROR';
            case E_WARNING: /* 2 */
                return 'E_WARNING';
            case E_PARSE: /* 4 */
                return 'E_PARSE';
            case E_NOTICE: /* 8 */
                return 'E_NOTICE';
            case E_CORE_ERROR: /* 16 */
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: /* 32 */
                return 'E_CORE_WARNING';
            case E_CORE_ERROR: /* 64 */
                return 'E_COMPILE_ERROR';
            case E_CORE_WARNING: /* 128 */
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: /* 256 */
                return 'E_USER_ERROR';
            case E_USER_WARNING: /* 512 */
                return 'E_USER_WARNING';
            case E_USER_NOTICE: /* 1024 */
                return 'E_USER_NOTICE';
            case E_STRICT: /* 2048 */
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: /* 4096 */
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: /* 8192 */
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: /* 16384 */
                return 'E_USER_DEPRECATED';
        }
        return $type;
    }

    function log_error($e, $message, int $severity = 1) {
        $type = $e["type"];

        if ($this->testMode){echo $message;}

        $trace = debug_backtrace(); $strTrace = "";
        if (count($trace) <= 3){$trace = []; $strTrace = "No stack trace";}
        else {
            for ($i = 3; $i < count($trace); $i++){
                $item = $trace[$i];
                $strTrace .= '  ' . (isset($item['file']) ? $item['file'] : '<unknown file>') . ' on line ' . (isset($item['line']) ? $item['line'] : '<unknown>') . ' calling ' . $item['function'] . '()' . "<br>";
            }
        }


        $message = str_replace($_SERVER["DOCUMENT_ROOT"], "", $message);
        $message = $this->replaceSpecChar($message, 0);
        $trace = $this->replaceSpecChar($strTrace, 0);
        $type = $this->replaceSpecChar($type, 0);
        $query = "UPDATE errors SET occurrences = occurrences + 1, status = 1 WHERE message = '$message'";
        if ($result = $this->conn->query($query)){
            if (mysqli_affected_rows($this->conn)==0){
              $query = "INSERT INTO errors (type, message, trace, severity) VALUES ('$type', '$message', '$trace', '$severity')";
              $this->conn->query($query);
              if ($severity > 1 AND !$this->testMode){
                $message .= "<br><br> https://".$this->giveServerInfo("servername")."/account/admin/errors";
                $this->mailer->easyMail("Bug Report: Severity $severity", $message, "pantheon@manyisles.ch", "website");
              }
            }
        }
    }
  }

  error_reporting(~0);
  ini_set('display_errors', 1);
  ini_set('log_errors', 1);
  mysqli_report(MYSQLI_REPORT_OFF);
  $handler = new errorHandler;

  /* Set the error handler. */
  set_error_handler(function ($errno, $errstr, $errfile, $errline) use (&$handler) {
    /* Ignore @-suppressed errors */
    if (!($errno & error_reporting())) return;

    $e = array('type'=>$errno,
               'message'=>$errstr,
               'file'=>$errfile,
               'line'=>$errline);
    $handler->redirect($e);
    return true;
  });
  /* Set the exception handler. */
  set_exception_handler(function ($e) use (&$handler) {
    $e = array('type'=>$e->getCode(),
               'message'=>$e->getMessage(),
               'file'=>$e->getFile(),
               'line'=>$e->getLine());
    $handler->redirect($e);
    return true;
  });
  /* Check if there were any errors on shutdown. */
  register_shutdown_function(function () use (&$handler) {
    if (!is_null($e = error_get_last())) {
        $handler->redirect($e);
        return true;
    }
  });

}

//trigger_error("ono uwu", E_USER_ERROR);

?>
