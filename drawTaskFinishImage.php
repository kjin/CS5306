<?php
  include 'pythonServerInterface.php';
  $inputImage = file_get_contents("php://input");
  if (isset($_GET["userID"]) and imagepng($inputImage, "files/" . $_GET["id"] . ".png"))
  {
    $userID = $_GET["userID"]);
    echo makePythonModuleCall('drawTaskFinishImage', $userID);
  }
  else
  {
    echo 'failure';
  }
?>
