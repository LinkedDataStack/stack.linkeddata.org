<?php
ob_start();
header("Content-Type: application/rss+xml; charset=UTF-8");

include 'categories.php';

$suite = $_GET["suite"];
switch ($suite)
{
case "nightly":
	$suiteSuffix = "-nightly";
	break;
case "testing":
	$suiteSuffix = "-testing";
	break;
default:
	$suite		 = "stable";
	$suiteSuffix = "";
}
// get only categories
if(isset($_GET["categories-only"]))
        $categories_only = TRUE;
// get feeds from specific category
if(isset($_GET["filter_category"])){
	if (is_numeric($_GET["filter_category"]))
		$category_id = $_GET["filter_category"];
	else 
		$category_id = array_search($_GET["filter_category"], $categories );
}
echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:dc="http://purl.org/dc/elements/1.1/" >
	<channel>
		<atom:link href="http://stack.linkeddata.org/download/repo.php?suite=<?= $suite ?>" rel="self" type="application/rss+xml" />
		<title>Linked Data Stack: <?= ucfirst($suite) ?> Repository</title>
		<description>The <?= $suite ?> debian repository of the Linked Data Stack.</description>
		<link>http://stack.linkeddata.org/download/repo.php?suite=<?= $suite ?></link>

<?php
if(isset($category_id))
	echo "<category>".$categories[$category_id]."</category>";	
else
	foreach($categories as $cat)
		echo "<category>".$cat."</category>";
?>

<?php if(isset($categories_only)) : ?>
	</channel>
</rss>
<?php exit; ?>
<?php endif; ?>
<?php
$handle = fopen("/var/reprepro/linkeddata/dists/ldstack" . $suiteSuffix . "/main/binary-amd64/Packages", "r");
if ($handle)
{
  while (($line = fgets($handle, 1024)) !== false)
  {
    // ignore Homepage and Description content that come as debian instructions
    if(stripos($line, '<insert the upstream URL, if relevant>') !== false ||
       stripos($line, '<insert up to 60 chars description>') !== false ||
       stripos($line, '<insert long description, indented with spaces>') !== false ) 
      continue;

    if(preg_match("/^\n/", $line))
    {
	if (isset($category_id)){
		if( array_key_exists($row["Package"], $categroy_package))
  			if (in_array($category_id, $categroy_package[$row["Package"]]))
 				$rows[] = $row;
	}
	else
		$rows[] = $row;
	unset($row);
      	unset($lastprp);
    }
    elseif (preg_match("/^(Package|Version|Filename|Description|Maintainer|Homepage):\s*(.+)$/", $line, $matches)){

      $lastprp = $matches[1];
      $value = $matches[2];
      $row[$lastprp] = $value;
     }
     elseif (preg_match("/^\s/", $line, $matches))
	$row[$lastprp] .= $line;
     else unset($lastprp);
  }

	fclose($handle);

	function cmp($a, $b)
	{
		return $b["Created"] - $a["Created"];
	}

	usort($rows, cmp);

	foreach ($rows as $row)
	{
?>
		<item>
			<guid isPermaLink="false">http://stack.linkeddata.org/components/<?= $row["Package"]?></guid>
			<title><?= $row["Package"] ?> <?= $row["Version"] ?></title>
<?
      		if (isset($row["Filename"])) 
		   echo "<pubDate>". gmdate("D, d M Y H:i:s \G\M\T",filemtime("/var/reprepro/linkeddata/" . $row["Filename"]))."</pubDate>".PHP_EOL;
      	        if (isset($row["Maintainer"])){ 
		   $authors = explode(",",$row["Maintainer"]);
		//	echo "<author>";
  		foreach ($authors as $a)
  			if(preg_match("/<(.*?)>/", $a, $mat)){
   				echo "<dc:creator>". $mat[1] . " (" . preg_replace('/[^A-Za-z0-9 ]/', '', str_replace(" ".$mat[0],"",$a)) .")</dc:creator>".PHP_EOL;
				// echo "<foaf:email>".$mat[1] . "</foaf:email>".PHP_EOL;
   				//echo "<foaf:name>". str_replace(" ".$mat[0],"",$a) ."</foaf:name>".PHP_EOL;
				}
   		//echo "</author>";
		}	
		if(isset($row["Description"])) echo "<description>".$row["Description"]."</description>".PHP_EOL;
		if(isset($row["Homepage"])) echo "<link>".$row["Homepage"]."</link>".PHP_EOL;

		// add an image if a file image exist with the name of the package, add it to the rss 
		$image = "/var/www/stack.linkeddata.org/wp-content/uploads/".$row["Package"];
		$list = glob($image.".{jpg,png,gif,jpeg}",GLOB_BRACE);
		if (count($list)>0){ 
			$mimetype_info = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
    			$mimetype = finfo_file($mimetype_info, $list[0]);
			finfo_close($mimetype_info);
			echo '<enclosure url="http://stack.linkeddata.org/wp-content/uploads/'. basename($list[0]).'"';
			echo ' length="' . filesize($list[0]).'"' ;	
			echo ' type="'.$mimetype.'" />';
		}
	       // add a category currently hard coded, we need a way to automatize this
		if (isset ($categories)){
			if (array_key_exists($row["Package"], $categroy_package)) {
				foreach ($categroy_package[$row["Package"]] as $value) {
					echo '<category>'. $categories[$value] .'</category>';						
				}
			}
			else // show uncategorised if not in the list
				echo '<category>'. $categories[-1] .'</category>';
		}
?>
		</item>
<?php
	}
}
?>
	</channel>
</rss>
