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
      <div>
        <h1></h1>
      </div>
      <div id="instructions" align="center"></div>
      <div id="skills" align="center">
      <hr>
        <b>How would you characterize your artistic ability?</b><br />
        <input type="radio" name="skillLevel" value="1"> Beginner 
        <input type="radio" name="skillLevel" value="2"> Intermediate 
        <input type="radio" name="skillLevel" value="3"> Advanced<br />
      <hr>
      </div>
      <div class="fs-container">
        <div id="lc"></div>
      </div>
      <div align="center" id="complete">
        <div align="center" id="messages"><br /></div>
        <button onclick="cancel()">Cancel</button>
      </div>
    </div>

    <!-- you really ought to include react-dom, but for react 0.14 you don't strictly have to. -->
    <script src="static/react-0.14.3.js"></script>
    <script src="static/jquery-1.8.2.js"></script>
    <script src="literallycanvas/js/literallycanvas.js"></script>

    <script type="text/javascript">
      var strPreInstructions = "<b>Instructions:</b> A creature is partially finished in the canvas below. ";
      var strComplete = "Please answer the question below and complete the creature.";
      var strDontComplete = "Please answer the question below and add to the creature, but <b>do not complete it.</b>";
      var strSufInstructions = "<br />Then click submit at the bottom of the page. <b>You should take at least two minutes but no more than one hour.</b>";
      var strCode = "Thank you! Please copy the following code into the token box in Mechanical Turk:";
      var strIncomplete = "Please select your skill level.";
      var strCancel = "The task has been canceled.";
    
      var imageSize = {width: 800, height: 600};
      var imageBounds = {
        x: 0, y: 0, width: imageSize.width, height: imageSize.height
      };
      
      var userID = <?php echo "\"" . round(microtime(true) * 1000) . "\""; ?>;
      var imageID;
      
      var lc;
      

      function save() {
        var elements = document.getElementsByName("skillLevel");
        var skillLevel = "";
        for (var i = 0; i < elements.length; i++)
        {
          if (elements[i].checked)
          {
            skillLevel = elements[i].value;
            break;
          }
        }
        if (skillLevel.length == 0)
        {
          document.getElementById("messages").innerHTML = "<font color=\"red\">" + strIncomplete + "</font>";
          return;
        }
        var image = lc.getImage({ rect: imageBounds }).toDataURL();
        jQuery.post("drawTaskFinishImage.php?userID=" + userID + "&imageID=" + imageID + "&skillLevel=" + skillLevel, image, displayUniqueToken);
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
          document.getElementById("instructions").innerHTML = strPreInstructions + strComplete + strSufInstructions;
        }
        else{
          document.getElementById("instructions").innerHTML = strPreInstructions + strDontComplete + strSufInstructions;
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
