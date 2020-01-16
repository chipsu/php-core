<?php

namespace metrica\core;

class Error {
  public static $errorHandler;
  public static $exceptionHandler;
  public static $shutdownFunction;

  public static function init() {
    error_reporting(E_ALL);

    if(!static::$exceptionHandler) {
      static::$exceptionHandler = [__CLASS__, 'defaultExceptionHandler'];
    }
    set_exception_handler(static::$exceptionHandler);

    if(!static::$errorHandler) {
      static::$errorHandler = [__CLASS__, 'defaultErrorHandler'];
    }
    set_error_handler(static::$errorHandler);

    if(!static::$shutdownFunction) {
      static::$shutdownFunction = [__CLASS__, 'defaultShutdownFunction'];
    }
    register_shutdown_function(static::$shutdownFunction);
  }

  public static function exceptionHandler(\Throwable $exception, $die = true) {
    call_user_func(static::$exceptionHandler ?? [__CLASS__, 'defaultExceptionHandler'], $exception);
    if($die) {
      die;
    }
  }

  public static function defaultExceptionHandler(\Throwable $exception) {
    if(!headers_sent()) {
      $code = $exception instanceof HttpException ? $exception->getHttpStatus() : 500;
      http_response_code($code);
    }
    error_log($exception);
    $accept = isset($_SERVER['HTTP_ACCEPT']) ? explode(',', $_SERVER['HTTP_ACCEPT']) : ['text/plain'];
    $error = [
      'file' => basename($exception->getFile()),
      'line' => $exception->getLine(),
      'message' => $exception->getMessage(),
    ];
    if(defined('HI_DEBUG') && HI_DEBUG) {
      $error['summary'] = (string)$exception;
      $error['trace'] = [];
      foreach($exception->getTrace() as $trace) {
        $error['trace'][] = $trace;
      }
    } else {
      $error['summary'] = sprintf(
        '%3$s in %1$s on line %2$d',
        $error['file'], $error['line'], $error['message']
      );
    }
    switch($accept[0]) {
    case 'text/html':
      if(!headers_sent()) {
        header('Content-Type: text/html');
      }
      echo '<!DOCTYPE HTML>';
      echo '<html><body>';
      echo '<h1>Error in application</h1>';
      if(defined('HI_DEBUG') && HI_DEBUG) {
        echo '<pre style="width:100%;white-space:pre-wrap">';
        var_dump($error['summary']);
        echo '</pre>';
      } else {
        echo '<p>' . htmlentities($error['summary']) . '</p>';
      }
      echo '</body></html>';
      break;
    case 'application/json':
      if(!headers_sent()) {
        header('Content-Type: application/json');
      }
      echo json_encode(['$error' => $error]);
      break;
    case 'text/plain':
    default:
      if(!headers_sent()) {
        header('Content-Type: text/plain');
      }
      echo $error['summary'];
      break;
    }
  }

  public static function defaultErrorHandler($code, $message, $filename, $lineno) {
    throw new \ErrorException($message, $code, 1, $filename, $lineno);
  }

  public static function defaultShutdownFunction() {
    if($error = error_get_last()) {
      call_user_func(static::$exceptionHandler, new \ErrorException(
        $error['message'], $error['type'], 1, $error['file'], $error['line']
      ));
    }
  }
}
