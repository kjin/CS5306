<?php
  include 'pythonServerInterface.php';
  $img = file_get_contents("php://input");
  $img = str_replace('data:image/png;base64,', '', $img);
  $img = str_replace(' ', '+', $img);
  $fileData = base64_decode($img);
  if (isset($_GET["userID"]) and isset($_GET["imageID"]) and file_put_contents("files/" . $_GET["imageID"] . ".png", $fileData))
  {
    $userID = $_GET["userID"];
    echo makePythonModuleCall('drawTaskFinishImage', [$userID]);
  }
  else
  {
    echo 'failure';
  }
?>
