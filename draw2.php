<!doctype html>
<html>
  <head>
    <title>Literally Canvas</title>
    <link href="literallycanvas/css/literallycanvas.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no" />

    <style type="text/css">
      .fs-container {
        width: 880px;
        height: 640px;
        margin: auto;
      }

      .literally {
        width: 100%;
        height: 100%;
        position: relative;
      }
    </style>
  </head>

  <body>
    <div class="fs-container">
      <div id="lc"></div>
    </div>
    <div align="center">
      <?php
        if (isset($_GET["id"])) {
          $id = $_GET["id"];
          load($id)
        }
        else {
          $id = 0;
        }
        echo "<button onclick=\"save($id)\">Save Me</button>";
        
        echo "\n<br />\n<br />";
        if($id[0]=='1'){
          
          echo "Draw Completely";
        }else{
          echo"Do not draw completely";
        }
        
      ?>
    </div>
    <div id="messageBox"></div>

    <!-- you really ought to include react-dom, but for react 0.14 you don't strictly have to. -->
    <script src="static/react-0.14.3.js"></script>
    <script src="static/jquery-1.8.2.js"></script>
    <script src="literallycanvas/js/literallycanvas.js"></script>

    <script type="text/javascript">
      var imageSize = {width: 800, height: 600};
      var imageBounds = {
        x: 0, y: 0, width: imageSize.width, height: imageSize.height
      };
    
      var serverAddress = "https://cs-5306-kelvinjin.c9users.io";
      // var serverAddress = "http://52.24.144.42";

      var lc = LC.init(document.getElementById("lc"), {
        imageURLPrefix: 'literallycanvas/img',
        toolbarPosition: 'bottom',
        defaultStrokeWidth: 2,
        imageSize: imageSize,
        strokeWidths: [1, 2, 3, 5, 30]
      });

      var blank = lc.getSnapshot();

      function save(id) {
        var image = lc.getImage({ rect: imageBounds }).toDataURL();
        window.open(image);
        jQuery.post(serverAddress + "/drawTaskFinishImage.php?userID=" + id, image, onSaveSuccess);
      }

      function onSaveSuccess() {
        document.getElementById("messageBox").innerHTML += "Save success.<br>";
      }

      function load(id) {
        jQuery.get(serverAddress + "/drawTaskGetImage.php?userID=" + id, "", onLoadSuccess);
      }

      function onLoadSuccess(result) {
        document.getElementById("messageBox").innerHTML += "Load success.<br>";
        lc.loadSnapshot(JSON.parse(result));
      }
    </script>
  </body>
</html>
