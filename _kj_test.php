<!doctype html>
<html>
  <head>
    <title>Literally Canvas</title>
    <link href="/literallycanvas/css/literallycanvas.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no" />

    <style type="text/css">
      .fs-container {
        width: 880px;
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
      <div id="instructions"></div>
      <div class="fs-container">
        <div id="lc"></div>
      </div>
      <div align="center">
        <br />
        <button onclick="save()">Submit</button>
      </div>
    </div>

    <!-- you really ought to include react-dom, but for react 0.14 you don't strictly have to. -->
    <script src="static/react-0.14.3.js"></script>
    <script src="static/jquery-1.8.2.js"></script>
    <script src="literallycanvas/js/literallycanvas.js"></script>

    <script type="text/javascript">
      var imageSize = {width: 800, height: 600};
      var imageBounds = {
        x: 0, y: 0, width: imageSize.width, height: imageSize.height
      };
      
      var userID = <?php $result = $_GET["workerID"]; echo (isset($result) ? "\"$result\"" : "\"00000\""); ?>;
      var imageID;
      
      var lc;
      

      function save() {
        var image = lc.getImage({ rect: imageBounds }).toDataURL();
        var newImageID = imageID + 100;
        jQuery.post("drawTaskFinishImage.php?userID=" + userID + "&imageID=" + newImageID, image, onSaveSuccess);
      }

      function onSaveSuccess() {
        
      }

      function load() {
        jQuery.get("drawTaskGetImage.php?userID=" + userID, "", onLoadSuccess);
      }

      function onLoadSuccess(result) {
        result=4102;
        imageID = parseInt(result);
        if(result[0]=='1'){
          document.getElementById("instructions").innerHTML = "On the canvas shown below, you see a random part of a creature<br> Complete the creature by drawing in whatever you like!<br>";
        }
        else{
          document.getElementById("instructions").innerHTML = "On the canvas shown below, you see an incomplete drawing of a creature<br> Contribute to the drawing but do not complete it!<br>";
        }
        var backgroundImage = new Image();
        backgroundImage.src = 'files/'+result+'.png';

        lc = LC.init(document.getElementById("lc"), {
          imageURLPrefix: 'literallycanvas/img',
          defaultStrokeWidth: 2,
          backgroundShapes: [
            LC.createShape(
              'Image', {x: 0, y: 0, image: backgroundImage}),
            
          ]
        });
        
      }
    </script>
  </body>
</html>
