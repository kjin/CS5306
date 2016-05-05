<!doctype html>
<html>
  <head>
    <title>Literally Canvas</title>
    <link href="/static/css/literallycanvas.css" rel="stylesheet">
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
        }
        else {
          $id = 0;
        }
        echo "<button onclick=\"save($id)\">Save Me</button>";
        echo "<button onclick=\"load($id)\">Load Me</button>";
      ?>
    </div>
    <div id="messageBox"></div>

    <!-- you really ought to include react-dom, but for react 0.14 you don't strictly have to. -->
    <script src="static/js/react-0.14.3.js"></script>
    <script src="static/js/literallycanvas.js"></script>
    <script src="static/js/jquery-1.8.2.js"></script>

    <script type="text/javascript">
      var serverAddress = "https://cs-5306-kelvinjin.c9users.io";
      // var serverAddress = "http://52.24.144.42";

      var lc = LC.init(document.getElementById("lc"), {
        imageURLPrefix: 'static/img',
        toolbarPosition: 'bottom',
        defaultStrokeWidth: 2,
        strokeWidths: [1, 2, 3, 5, 30]
      });

      var blank = lc.getSnapshot();

      function save(id) {
        var snapshot = lc.getSnapshot();
        jQuery.post(serverAddress + "/receiveData.php?id=" + id, JSON.stringify(snapshot), onSaveSuccess);
      }

      function onSaveSuccess() {
        document.getElementById("messageBox").innerHTML += "Save success.<br>";
      }

      function load(id) {
        jQuery.get(serverAddress + "/sendData.php?id=" + id, "", onLoadSuccess);
      }

      function onLoadSuccess(result) {
        document.getElementById("messageBox").innerHTML += "Load success.<br>";
        lc.loadSnapshot(JSON.parse(result));
      }
    </script>
  </body>
</html>
