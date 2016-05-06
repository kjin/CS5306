<!doctype html>
<html>
  <head>
    <title>Literally Canvas</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no" />
  </head>

  <body onload="requestComparisons()">
    <div id="whole">
      <div align="center">
        <b>Instructions:</b> For each pair of images, choose the one that is more visually appealing to you.
        You must choose one option for each pair. When you are finished, click the "Submit" button at the bottom of this page.
      </div>
      <div align="center">
        <table style="width:100%" border="1" id="comparisons">
        </table>
        <div align="center" id="messages"><br /></div>
        <button onclick="next()" id="continue"></button>
        <br /><br /><br /><br />
      </div>
    </div>

    <script src="static/jquery-1.8.2.js"></script>
    <script type="text/javascript">
      // string constants
      var strNoImages = "Sorry! Currently there are no images to compare. Please check back later. (Do NOT submit the HIT because it will be rejected.)";
      var strIncomplete = "Please choose an image.";
      var strCode = "Thank you! Please copy the following code into the token box in Mechanical Turk:";
    
      var userID = <?php echo "\"" . round(microtime(true) * 1000) . "\""; ?>;
      var maxNumComparisons = 10;
      
      var allComparisons = "";
      var numComparisonsSoFar = 0;
      var preferencesSoFar = "";
      
      function next() {
        var elements = document.getElementsByName("comparison");
        if (elements[0].checked || elements[1].checked)
        {
          if (preferencesSoFar.length > 0)
          {
            preferencesSoFar += ";";
          }
          if (elements[0].checked)
          {
            preferencesSoFar += elements[0].value + "," + elements[1].value;
          }
          else
          {
            preferencesSoFar += elements[1].value + "," + elements[0].value;
          }
        }
        else
        {
          document.getElementById("messages").innerHTML = "<font color=\"red\">" + strIncomplete + "</font>";
          return;
        }
        numComparisonsSoFar++;
        if (numComparisonsSoFar == allComparisons.length)
        {
          submitPreferences();
        }
        else
        {
          loadComparison();
        }
      }

      // Returns two imageIDs to compare pictures against
      function requestComparisons() {
        jQuery.get("evalTaskGetImages.php?userID=" + userID + "&numTimes= " + maxNumComparisons, receiveComparisons);
      }

      function receiveComparisons(result) {
        allComparisons = result.split(';');
        if (allComparisons[0].length == 0)
        {
          document.getElementById("whole").innerHTML = strNoImages;
          return;
        }
        loadComparison();
      }
      
      function loadComparison() {
        if (numComparisonsSoFar == allComparisons.length)
        {
          document.getElementById("continue").innerHTML = "Submit";
        }
        else
        {
          var values = allComparisons[numComparisonsSoFar].split(',');
          document.getElementById("comparisons").innerHTML = setupTableRow(values[0], values[1]);
          document.getElementById("continue").innerHTML = "Continue >>";
        }
      }
      
      function submitPreferences() {
        jQuery.post("evalTaskFinish.php?userID=" + userID, preferencesSoFar, displayUniqueToken);
      }
      
      function displayUniqueToken(result) {
        document.getElementById("whole").innerHTML = strCode + " <b>" + result + "</b>";
      }
      
      function setupTableRow(imageID1, imageID2)
      {
        var result = "<tr>";
        result += "" + (numComparisonsSoFar + 1) + " out of " + allComparisons.length;
        result += "</tr><tr>";
        result += setupTableCell(imageID1);
        result += setupTableCell(imageID2);
        result += "</tr>";
        return result;
      }
      
      // Returns the inner HTML for a table cell
      function setupTableCell(imageID) {
        var imageURL = "files/" + imageID + ".png";
        var result = "<td><div align=\"center\">";
        result += "<img src=\"" + imageURL + "\" width=\"100%\"/><br />";
        result += "I prefer this <input type=\"radio\" name=\"comparison\" value=\"" + imageID + "\">";
        result += "</div></td>";
        return result;
      }
    </script>
  </body>
</html>
