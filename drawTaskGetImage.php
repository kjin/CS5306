<?php
  include 'pythonServerInterface.php';
      

  if (isset($_GET["userID"]))
  {
    $userID = $_GET["userID"];
    echo $userID;
    echo makePythonModuleCall('drawTaskGetImage', [$userID]);
  }
  else
  {
    echo 'failure';
  }
?>
