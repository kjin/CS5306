<?php
  if (isset($_GET["id"]))
  {
    echo file_get_contents("files/outputfile" . $_GET["id"] . ".txt");
  }
?>
