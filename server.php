#!/usr/bin/php

<?php 
  set_time_limit(0);
  $address = '127.0.0.1';
  $port = 5080;
  $socket = socket_create(AF_INET, SOCK_STREAM, 0);
  socket_bind($socket, $address, $port) or die('Error in binding to address');
  socket_listen($socket);
  
  register_shutdown_function(function(){
    socket_close($socket);
  });

  while(true){
    $client = socket_accept($socket);
    if(!$client)
      break;
    while(true){
      $input = @socket_read($client, 1024);
      if(!$input)
        break;
      $pos = strpos($input,"\r\n\r\n");
      $headers = explode("\r\n",substr($input,0,$pos));
      $text = "<html><head><title>Hilmers 0.1</title></head><body><pre>";
      $text .= "Your browser sent the following requests: \n";
      foreach($headers as $i=>$header){
        $text .= "$header\n";
      }
      $text .= "</pre></body></html>";

      $resheaders = "Content-Length: ".strlen($text)."\r\nContent-Type: text/html\r\nContent-Language: en\r\nServer: hilmers 0.1\r\nStatus: 200 Ok\r\nX-Powered-By: PHP\r\n";
      
      socket_write($client,"HTTP/1.1 200 OK\r\n");
      socket_write($client, $resheaders);
      socket_write($client,"\r\n");
      socket_write($client, $text);
      socket_close($client);
    }
  }

?>