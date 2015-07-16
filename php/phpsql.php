<?php

function parseToXml($htmlStr)
{
    $xmlStr=str_replace('<','&lt;',$htmlStr);
    $xmlStr=str_replace('>','&gt;',$xmlStr);
    $xmlStr=str_replace('"','&quot;',$xmlStr);
    $xmlStr=str_replace("'",'&#39;',$xmlStr);
    $xmlStr=str_replace("&",'&amp;',$xmlStr);
    return $xmlStr;
}

function connect() {

	// Connect to the database
	$host = "insert_host_here";
	$user = "user";
	$pass = "password";
	$db = "database";

	$connection = pg_connect("host=$host dbname=$db user=$user password=$pass")
	    or die ("Could not connect to server\n");
}

function outputXML($time) {

	// Connect to the database
	connect();

	// Select all the rows in the table
	$query = "SELECT map, lat, long AS lng, COUNT(macid)
	FROM tsp.data NATURAL JOIN tsp.coordinates
	WHERE timestart < $time AND (timestart + duration) > $time
	GROUP BY map, lat, lng;";

	// query is executed
	$result = pg_query($connection, $query) or die("Cannot execute query: $query\n");

	// Create the XML file
	header("Content-type: text/xml");

	// Start XML file, echo parent node
	echo '<markers>';
	// Iterate through the rows, printing XML nodes for each
	while($row = pg_fetch_assoc($result)){
	    echo '<marker ';
	    echo 'map="' . parseToXML($row['map']) . '" ';
	    echo 'lat="' .parseToXML($row['lat']) . '" ';
	    echo 'lng="' .parseToXML($row['lng']) . '" ';
	    echo 'count="' .parseToXML($row['count']) . '" ';
	    echo '/>';
	}
	// end XML file
	echo '</markers>';
}

//$time = 1395010865;
//$time = 1395058771; max 12 points

if (isset($_GET['time'])) {
	//$time = 1395010865;
	$time = $_GET['time'];
} else {
	$time = 1395010865;
}

outputXML($time);

?>
