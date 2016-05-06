<?php
  include 'pythonServerInterface.php';
  if (isset($_GET["userID"]))
  {
    $userID = $_GET["userID"];
    $result = makePythonModuleCall('drawTaskGetImage', [$userID]);
    // TODO Delete this
    if (!$result)
    {
      $result = '1101';
    }
    echo $result;
  }
  else
  {
    echo 'failure';
  }
?>
