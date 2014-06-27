<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8" />
<meta name="author" content="Vadim Zaslawski" />
<?php
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
?>
<title><?= ucfirst($suite) ?> Repository – Linked Data Stack</title>
<link rel="stylesheet" href="repo.css" />

</head>
<body>

<a href="rss.php?suite=<?= $suite ?>" style="float: right;">RSS</a>

<a href="index.html">Repositories</a>:
<a href="repo.php">Stable</a>,
<a href="repo.php?suite=testing">Testing</a>,
<a href="repo.php?suite=nightly">Nightly</a>

<table>
	<caption>Linked Data Stack: <strong><?= ucfirst($suite) ?></strong> Repository</caption>
	<tr>
		<th><a href="?suite=<?= $suite ?>">Package</a></th>
		<th>Version</th>
		<th><a href="?suite=<?= $suite ?>&sort=created">Created</a></th>
	</tr>
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

	if ($_GET["sort"] == "created")
	{
		function cmp($a, $b)
		{
			return $b["Created"] - $a["Created"];
		}

		usort($rows, cmp);
	}

	foreach ($rows as $row)
	{
?>
	<tr>
		<td><?= $row["Package"] ?></td>
		<td><?= $row["Version"] ?></td>
		<td><?= date("Y-M-d H:i", $row["Created"]) ?></td>
	</tr>
<?php
	}
}
?>
</table>

</body>
</html>
