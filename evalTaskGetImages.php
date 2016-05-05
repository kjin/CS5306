<?php
  include 'pythonServerInterface.php';
  if (isset($_GET["userID"]))
  {
    $userID = $_GET["userID"];
    echo makePythonModuleCall('evalTaskGetImages', [$userID]);
  }
  else
  {
    echo 'failure';
  }
?>
