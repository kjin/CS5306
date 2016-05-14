<?php
    $min = getOrDefault("min", 0);
    $max = getOrDefault("max", 0);
    
    function getOrDefault($tag, $default)
    {
        return isset($_GET[$tag]) ? $_GET[$tag] : $default;
    }
    
    $file = file_get_contents('./imgdata.txt');
    $lines = explode("\n", $file);
    $map = array();
    for ($i = 0; $i < count($lines); $i++)
    {
        $components = explode(" ", $lines[$i]);
        $map[$components[0]] = $components;
    }
    
    $prefixes = [ "10", "11", "21", "22", "31", "32", "33", "41", "42", "43", "44" ];
    $myTable = "";
    for ($i = 0; $i < count($prefixes); $i++)
    {
        $myTable .= "<tr>";
        for ($j = $min; $j <= $max; $j++)
        {
            $imageID = $prefixes[$i] . str_pad($j, 2, '0', STR_PAD_LEFT);
            $myTable .= "<td>";
            $myTable .= "<img src=\"files/" . $imageID . ".png\" alt=\"Not available\" width=\"200\"/>";
            $myTable .= "<br />";
            $myTable .= $imageID;
            $myTable .= "<br />";
            $myTable .= $map[$imageID][3];
            $myTable .= "</td>";
        }
        $myTable .= "</tr>";
    }
?>
<html>
    <head>
        <title>Approval Dashboard</title>
    </head>
    <body>
        <table id="the_table" border=1 style="width:100%">
            <?php echo $myTable; ?>
        </table>
    </body>
</html>