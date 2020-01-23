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
    $error = $exception instanceof Exception ? $exception->getResponseData() : Exception::defaultResponseData($exception);
    switch($accept[0]) {
    case 'text/html':
      if(!headers_sent()) {
        header('Content-Type: text/html');
      }
      echo '<!DOCTYPE HTML>';
      echo '<html><body>';
      echo '<h1>Error in application</h1>';
      echo '<p>' . htmlentities(json_encode($error)) . '</p>';
      echo '</body></html>';
      break;
    case 'application/json':
      if(!headers_sent()) {
        header('Content-Type: application/json');
      }
      echo json_encode($error);
      break;
    case 'text/plain':
    default:
      if(!headers_sent()) {
        header('Content-Type: text/plain');
      }
      echo json_encode($error);
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
