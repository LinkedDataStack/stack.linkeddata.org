<?php include 'categories.php';?>
<?php
header("Content-Type: application/rss+xml; charset=UTF-8");

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
$handle = fopen("/var/reprepro/linkeddata/dists/ldstack" . $suiteSuffix . "/main/binary-amd64/Packages", "r");
if ($handle)
{
	while (($line = fgets($handle, 1024)) !== false)
	{
		if (strlen($line) <= 2) // lines include EOL
		{
			if (count($row) == 3)
			{
				$created = filemtime("/var/reprepro/linkeddata/" . $row["Filename"]);
				unset($row["Filename"]);
				$row["Created"] = $created;

				$rows[] = $row;
			}

			unset($row);
		}
		elseif (preg_match("/^(Package|Version|Filename):\s*(.+)$/", $line, $matches))
			$row[$matches[1]] = $matches[2];
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
			<title><?= $row["Package"] ?> <?= $row["Version"] ?></title>
			<pubDate><?= gmdate("D, d M Y H:i:s \G\M\T", $row["Created"]) ?></pubDate>
			<description><?= $row["Description"]?></description>
			<author><?= $row["Maintainer"]?></author>
			<link><?= $row["Homepage"]?></link>
			<?
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
			if (isset ($categories)){
				if (array_key_exists($row["Package"], $categroy_package){
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
