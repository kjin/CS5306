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
    $result = makePythonModuleCall('evalTaskFinish', [$userID]);
    echo $result;
    if (!$result)
    {
      echo 'approveme2';
    }
  }
  else
  {
    echo 'approveme1';
  }
?>
