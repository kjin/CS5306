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
    $result = makePythonModuleCall('evalTaskGetImages', [$userID,$numTimes]);
    // TODO: Delete this
    if (!$result)
    {
      $result = "";
      for ($i = 0; $i < $numTimes; $i++)
      {
        if ($i > 0)
        {
          $result .= ",";
        }
        $result .= "1001,1002";
      }
    }
    // replace every other comma with a semicolon
    $exploded = explode(',',$result);
    $result = "";
    for ($i = 0; $i < count($exploded); $i++)
    {
      if ($i > 0)
      {
        if ($i % 2 == 0)
        {
          $result .= ";";
        }
        else
        {
          $result .= ",";
        }
      }
      $result .= $exploded[$i];
    }
    echo $result;
  }
  else
  {
    echo 'failure';
  }
?>
