<?php
  include 'pythonServerInterface.php';
  if (isset($_GET["userID"]))
  {
    $userID = $_GET["userID"];
    $numTimes = 1;
    if (isset($_GET["numTimes"]))
    {
      $numTimes = intval($_GET["numTimes"]);
    }
    for ($i = 0; $i < $numTimes; $i++)
    {
      if ($i > 0)
      {
        echo ";";
      }
      echo makePythonModuleCall('evalTaskGetImages', [$userID]);
    }
  }
  else
  {
    echo 'failure';
  }
?>
