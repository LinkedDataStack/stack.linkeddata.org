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
		</item>
<?php
	}
}
?>
	</channel>
</rss>
