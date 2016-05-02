<?php
  if (isset($_GET["id"]) and file_put_contents("files/outputfile" . $_GET["id"] . ".txt", file_get_contents("php://input"))) {
    echo 'received';
  }
  else {
    echo 'failure';
  }
?>
