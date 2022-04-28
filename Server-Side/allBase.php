<?php
//error handling
if (!function_exists("err_redirect")) {
  error_reporting(~0);
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
  mysqli_report(MYSQLI_REPORT_OFF);

  /* Set the error handler. */
  set_error_handler(function ($errno, $errstr, $errfile, $errline) {
      /* Ignore @-suppressed errors */
      if (!($errno & error_reporting())) return;

      $e = array('type'=>$errno,
                 'message'=>$errstr,
                 'file'=>$errfile,
                 'line'=>$errline);
      err_redirect($e);
      return true;
  });


  /* Set the exception handler. */
  set_exception_handler(function ($e) {
      $e = array('type'=>$e->getCode(),
                 'message'=>$e->getMessage(),
                 'file'=>$e->getFile(),
                 'line'=>$e->getLine());
      err_redirect($e);
      return true;
  });


  /* Check if there were any errors on shutdown. */
  register_shutdown_function(function () {
      if (!is_null($e = error_get_last())) {
          err_redirect($e);
          return true;
      }
  });


  function err_redirect($e) {
      $now = date('d-M-Y H:i:s');
      $type = format_error_type($e['type']);
      $message = "[$now] $type: {$e['message']} in {$e['file']} on line {$e['line']}\n";
      //echo "<br><br>$message";//exit;
      mail("pantheon@manyisles.ch", "Bug Report", $_SERVER['DOCUMENT_ROOT']."\n$message");

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
            break;
          /* Redirect to "oops" page on the following errors. */
          case 0: /* Exceptions return zero for type */
          case E_ERROR:
          case E_PARSE:
          case E_CORE_ERROR:
          case E_COMPILE_ERROR:
          case E_USER_ERROR:
            echo "<script>window.location.replace('/Code/error');</script>";
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
}

//allBase

if (!trait_exists("allBase")){
  trait allBase {
    public $regArrayR = [
      "basic" => "/[^A-Za-z0-9_]/",
      "number" => "/[^0-9]/",
      "quotes" => "/[\"']/",
      "full" => "/[^A-Za-z0-9àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœĀāŌо̄Ūū_&\/,\(\)\.\-%:\? ]/",
      "cleanText" => "/^[^\"<>]+$/",
      "cleanText2" => "/^[^\"'<>]+$/",
      "wikiName" => "/^[A-Za-z0-9àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœĀāŌо̄Ūū',():\- ]{2,}$/",
      "tag" => "/[^A-Za-z0-9&]/",
      "dicWord" => "/[^A-Za-zàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇßØøÅåÆæœĀāŌо̄Ūū\-' ]/"
    ];

    function construct() {
      require($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");
      $this->conn = $conn;
    }

    function purate($input, $regex = "basic") {
      //for links
      $input = str_replace(" ", "_", $input);
      return preg_replace($this->regArrayR[$regex], "", $input);
    }
    function purify($input, $regex = "basic") {
      //for sql cleaning & more
      return preg_replace($this->regArrayR[$regex], "", $input);
    }
    function replaceSpecChar($input, $level = 1) {
      $input = str_replace("'", "%single_quote%", $input);
      if ($level > 0){
        $input = str_replace('"', "%double_quote%", $input);
        if ($level > 1) {
            $input = str_replace(":", '%colon%', $input);
            $input = str_replace(";", '%pcolon%', $input);
            $input = str_replace("-", '%hyphon%', $input);
            $input = str_replace(",", '%comma%', $input);
            $input = str_replace("[", '%sqbrak_left%', $input);
            $input = str_replace("]", '%sqbrak_right%', $input);
            if ($level > 2){
              $input = str_replace("'", '', $input);
            }
        }
      }
      return $input;
    }
    function placeSpecChar($input, $level = 1) {
      if ($input == ""){return "";}
      if ($level < 1){
        $input = str_replace('%double_quote%', '', $input);
        $input = str_replace("%single_quote%", "", $input);
      }
      else {
        $input = str_replace("%single_quote%", "'", $input);
        $input = str_replace("%double_quote%", '"', $input);
        $input = str_replace("%colon%", ':', $input);
        $input = str_replace("%pcolon%", ';', $input);
        $input = str_replace("%hyphon%", '-', $input);
        $input = str_replace("%comma%", ',', $input);
        $input = str_replace("%sqbrak_left%", '[', $input);
        $input = str_replace("%sqbrak_right%", ']', $input);
        $input = str_replace("%qbrak_left%", '{', $input);
        $input = str_replace("%qbrak_right%", '}', $input);
      }
      return $input;
    }
    function explodeA($str, $separator = ", ", $reg = null) {
      if ($str == "") {return [];}
      $arr = explode($separator, $str);
      return $arr;
    }
    function generateRandomString($length = 10) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
    }
    function isEmpty(array $array) {
        $empty = true;
        array_walk_recursive($array, function ($leaf) use (&$empty) {
            if ($leaf === [] || $leaf === '') {
                return false;
            }
            $empty = false;
        });
        return $empty;
    }
    function killCache() {
      header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
      header("Pragma: no-cache"); //HTTP 1.0
      header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }
    function go($place) {
      $this->killCache();
      echo "<script>window.location.replace('$place');</script>";
      exit;
    }
  }
}
if (!class_exists("useBase")){
  class useBase {
    use allBase;
  }
}
?>
