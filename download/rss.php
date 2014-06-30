<?php
ob_start();
header("Content-Type: application/rss+xml; charset=UTF-8");

include "categories.php";

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

echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<rss version="2.0">
	<channel>
		<title>Linked Data Stack: <?= ucfirst($suite) ?> Repository</title>
		<description>The <?= $suite ?> debian repository of the Linked Data Stack.</description>
		<link>http://stack.linkeddata.org/download/repo.php?suite=<?= $suite ?></link>
<?php
// $handle = fopen("/var/reprepro/linkeddata/dists/ldstack" . $suiteSuffix . "/main/binary-amd64/Packages", "r");
$handle = fopen("Packages", "r");
if ($handle)
{
	while (($line = fgets($handle, 1024)) !== false)
  {
    if(preg_match("/^\n/", $line))
    {
      $rows[] = $row;
      unset($row);
      unset($lastprp);
    }
    elseif (preg_match("/^(Package|Version|Filename|Description|Maintainer|Homepage):\s*(.+)$/", $line, $matches)){

      $lastprp = $matches[1];
      $value = $matches[2];
      // ignore Homepage and Description content that come as debian instructions
			if(stripos($line, '<insert the upstream URL, if relevant>') !== false ||
				 stripos($line, '<insert up to 60 chars description>') !== false ||
				 stripos($line, '<insert long description, indented with spaces>') !== false )
				continue;

      if (strcmp($lastprp,"Filename")){
        $created = filemtime("/var/reprepro/linkeddata/" . $value);
        $row["Created"] = $created;
      }
      elseif(strcmp($lastprp,"Maintainer")){
        if(preg_match("/<(.*?)>/", $value, $mat)) {
          $row["Author"] = $mat[1] . " " . str_replace(" ".$mat[0],"",$value);
        }
      }
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
			<guid><?= $row["Package"]?></guid>
			<title><?= $row["Package"] ?> <?= $row["Version"] ?></title>
			<pubDate><?= gmdate("D, d M Y H:i:s \G\M\T", $row["Created"]) ?></pubDate>
<?
			if(isset($row["Description"])) echo "<description>".$row["Description"]."</description>".PHP_EOL;
			if(isset($row["Author"])) echo "<author>".$row["Author"]."</author>".PHP_EOL;
			if(isset($row["dc:creator"])) echo "<dc:creator>".$row["dc:creator"]."</dc:creator>".PHP_EOL;
			if(isset($row["Homepage"])) echo "<link>".$row["Homepage"]."</link>".PHP_EOL;

			// add an image if a file image exist with the name of the package, add it to the rss 
			$image = "/var/www/stack.linkeddata.org/wp-content/uploads/".$row["Package"];
			$list = glob($image.".{jpg,png,gif,jpeg}",GLOB_BRACE);
			if (count($list)>0){ 
				$mimetype_info = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
    		$mimetype = finfo_file($mimetype_info, $list[0]);
				finfo_close($mimetype_info);

				echo '<enclosure url="\http://stack.linkeddata.org/wp-content/uploads/"'. basename($list[0]);
				echo ' length=' . filesize($list[0]) ;	
				echo ' type="'.$mimetype.'" />';
			}
      // add a category currently hard coded, we need a way to automatize this
      include 'categories.php';
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
