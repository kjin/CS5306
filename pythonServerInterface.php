<?php
  function makePythonModuleCall($function, $args)
  {
    $data = $function;
    foreach ($args as $element)
    {
      $data .= "," . $element;
    }
    $mySocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if (@socket_connect($mySocket, '127.0.0.1', 9876))
    {
      $buffer = "";
      if (strlen($data) <= 1000)
      {
        socket_send($mySocket, $data, strlen($data), MSG_EOF);
        if (socket_recv($mySocket, $buffer, 1024, 0) > 0)
        {
          return $buffer;
        }
        else
        {
          return false;
        }
      }
    }
    error_log("ERROR: Python server is not running.");
  }
?>
