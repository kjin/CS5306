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
        <div class="literally backgrounds">
            
            
        </div>
    </div>
    <div align="center">
    
    </div>
    <div id="messageBox"></div>

    <!-- you really ought to include react-dom, but for react 0.14 you don't strictly have to. -->
    <script src="static/react-0.14.3.js"></script>
    <script src="static/jquery-1.8.2.js"></script>
    <script src="literallycanvas/js/literallycanvas.js"></script>

  <?php
     
        if (isset($_GET["id"])) {
          $id = $_GET["id"];
          
        }
        else {
          $id = 0;
          
        }
        echo "<button onclick=\"save($id)\">Save Me</button>";
        echo "<button onclick=\"load($id)\">Load Me</button>";
        echo "\n<br />\n<br />";
        if($id[0]=='1'){
          echo "Draw Completely";
        }
        else{
          echo "Do not draw Completely";
        }
        
      ?>
    <script type="text/javascript">
     
  $(document).ready(function() {
    var backgroundImage = new Image()
    backgroundImage.src = 'files/4102.png';

    var lc = LC.init(
        document.getElementsByClassName('literally backgrounds')[0],
        {
          backgroundShapes: [
            LC.createShape(
              'Image', {x: 0, y: 0, image: backgroundImage}),
            
          ]
        });
    // the background image is not included in the shape list that is
    // saved/loaded here
   
  });
      
      

      function save(id) {
        var image = lc.getImage({ rect: imageBounds }).toDataURL();
        window.open(image);
        jQuery.post(serverAddress + "/drawTaskFinishImage.php?userID=" + id, image, onSaveSuccess);
      }

      function onSaveSuccess() {
        document.getElementById("messageBox").innerHTML += "Save success.<br>"+$id ;
      }

      function load(id) {
        jQuery.get(serverAddress + "/drawTaskGetImage.php?userID=" + id, "", onLoadSuccess);
      }

      function onLoadSuccess(result) {
        document.getElementById("messageBox").innerHTML += "Loaded image number : "+result;
        lc.loadSnapshot(JSON.parse(result));
      }
    </script>
  </body>
</html>
