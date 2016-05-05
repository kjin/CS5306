<?php
  include 'pythonServerInterface.php';
  $data = file_get_contents("php://input");
  $comparisons = explode(';', $data);
  if (isset($_GET["userID"]))
  {
    $userID = $_GET["userID"];
    foreach ($comparisons as $comparison)
    {
      $preferences = explode(',',$comparison);
      makePythonModuleCall('evalTaskCompare', [$userID, $preferences[0], $preferences[1]]);
    }
    echo makePythonModuleCall('evalTaskFinish', [$userID]);
  }
  else
  {
    echo 'failure';
  }
?>
