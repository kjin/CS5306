<!doctype html>
<html>
  <head>
    <title>Literally Canvas</title>
    <link href="/literallycanvas/css/literallycanvas.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no" />

    <style type="text/css">
      .fs-container {
        width: 840px;
        margin: auto;
      }

      .literally {
        width: 100%;
        height: 100%;
        position: relative;
      }
    </style>
  </head>

  <body onload="load()">
    <div id="whole">
      <div id="instructions" align="center"></div>
      <div class="fs-container">
        <div id="lc"></div>
      </div>
      <div align="center" id="complete">
        <button onclick="cancel()">Cancel</button>
      </div>
    </div>

    <!-- you really ought to include react-dom, but for react 0.14 you don't strictly have to. -->
    <script src="static/react-0.14.3.js"></script>
    <script src="static/jquery-1.8.2.js"></script>
    <script src="literallycanvas/js/literallycanvas.js"></script>

    <script type="text/javascript">
      var strComplete = "A creature is partially finished in the canvas below. Complete the creature.";
      var strDontComplete = "A creature is partially finished in the canvas below. Add to the creature, but do not complete it.";
      var strCode = "Thank you! Please copy the following ID into the token box in Mechanical Turk:";
      var strCancel = "The task has been canceled.";
    
      var imageSize = {width: 800, height: 600};
      var imageBounds = {
        x: 0, y: 0, width: imageSize.width, height: imageSize.height
      };
      
      var userID = <?php $result = $_GET["workerID"]; echo (isset($result) ? "\"$result\"" : "\"00000\""); ?>;
      var imageID;
      
      var lc;
      

      function save() {
        var image = lc.getImage({ rect: imageBounds }).toDataURL();
        jQuery.post("drawTaskFinishImage.php?userID=" + userID + "&imageID=" + imageID, image, displayUniqueToken);
      }

      function displayUniqueToken(result) {
        document.getElementById("whole").innerHTML = strCode + " <b>" + result + "</b>";
      }

      function load() {
        jQuery.get("drawTaskGetImage.php?userID=" + userID, "", onLoadSuccess);
      }

      function onLoadSuccess(result) {
        // This is the image we upload, NOT the image that is being drawn on
        imageID = parseInt(result);
        if(result[0]=='1'){
          document.getElementById("instructions").innerHTML = "<b>Instructions: </b>" + strComplete;
        }
        else{
          document.getElementById("instructions").innerHTML = "<b>Instructions: </b>" + strDontComplete;
        }
        var backgroundImage = new Image();
        // Subtract 100 from the image ID to get the image we should be drawing on.
        backgroundImage.src = 'files/'+(imageID - 100)+'.png';

        lc = LC.init(document.getElementById("lc"), {
          imageURLPrefix: 'literallycanvas/img',
          defaultStrokeWidth: 2,
          backgroundShapes: [
            LC.createShape(
              'Image', {x: 0, y: 0, image: backgroundImage}),
            
          ]
        });
        
        document.getElementById("complete").innerHTML += "<button onclick=\"save()\">Submit</button>";
      }
      
      function cancel()
      {
        jQuery.get("drawTaskCancelImage.php?userID=" + userID, "", onCancelSuccess);
      }
      
      function onCancelSuccess()
      {
        document.getElementById("whole").innerHTML = strCancel;
      }
    </script>
  </body>
</html>
