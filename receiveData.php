<?php
  file_put_contents("outputfile.txt", file_get_contents("php://input"));
  echo 'received';
?>