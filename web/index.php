<html>
<head>
  <title>Vertretungsplan</title>
  <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="w3.css">
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="/favicon.ico">
</head>
<body>
<?php
class TableRows extends RecursiveIteratorIterator {
  function __construct($it) {
    parent::__construct($it, self::LEAVES_ONLY);
  }

  function current() {
    return "<td>" . parent::current(). "</td>";
  }

  function beginChildren() {
    echo "<tr>";
  }

  function endChildren() {
    echo "</tr>" . "\n";
  }
}
echo "<span class='w3-xlarge'>Vertretungsplan für die Q12</span><br><br>";
$config = parse_ini_file('../../priv/config.ini');
$servername = "localhost";
$dbname = "scraper";
try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $config['username'], $config['password']);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $conn->prepare("SELECT `datum_plan_heute` FROM `pläne_daten` WHERE `index`=0");
  $stmt->execute();
  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $datum_plan_heute = $stmt->fetch();
  echo "<span class='w3-large'>Vertretungsplan für <span style='color: red;'>";
  echo $datum_plan_heute["datum_plan_heute"];
  echo "</span>:</span><br><br>";

  $stmt = $conn->prepare("SELECT `keine_v_heute` FROM `pläne_daten` WHERE `index`=0");
  $stmt->execute();
  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $keine_v_heute = $stmt->fetch();
  if($keine_v_heute["keine_v_heute"] == 0) {

    echo "<div><table class='w3-table-all w3-hoverable w3-small' style='width: 60%;'>";
    echo "<tr class='w3-black'><th>Stunde</th><th>Betrifft</th><th>Vertretung</th><th>Fach</th><th>Raum</th><th>Info</th><th>updated_at</th></tr>";

    $stmt = $conn->prepare("SELECT `Std.`, `Betrifft`, `Vertretung`, `Fach`, `Raum`, `Info`, `updated_at` FROM `plan_heute`");
  	$stmt->execute();

  	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  	foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
     	 echo $v;
   	}
    echo "</table></div><br><br>";
  }
  else {echo "<span class='w3-large'> Keine Vertretungen für die Q12</span><br<br><br><br>";}

  $stmt = $conn->prepare("SELECT `datum_plan_morgen` FROM `pläne_daten` WHERE `index`=0");
  $stmt->execute();
  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $datum_plan_morgen = $stmt->fetch();
  echo "<span class='w3-large'>Vertretungsplan für <span style='color: red;'>";
  echo $datum_plan_morgen["datum_plan_morgen"];
  echo "</span>:</span><br><br>";

  $stmt = $conn->prepare("SELECT `keine_v_morgen` FROM `pläne_daten` WHERE `index`=0");
  $stmt->execute();
  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $keine_v_morgen = $stmt->fetch();
  if($keine_v_morgen["keine_v_morgen"] == 0) {

  	echo "<div><table class='w3-table-all w3-hoverable w3-small' style='width: 60%;'>";
  	echo "<tr class='w3-black'><th>Stunde</th><th>Betrifft</th><th>Vertretung</th><th>Fach</th><th>Raum</th><th>Info</th><th>updated_at</th></tr>";

  	$stmt = $conn->prepare("SELECT `Std.`, `Betrifft`, `Vertretung`, `Fach`, `Raum`, `Info`, `updated_at` FROM `plan_morgen`");
          $stmt->execute();

          $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
          foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
           echo $v;
          }
          echo "</table></div><br><br><br>";
      }
      else {echo "<span class='w3-large'> Keine Vertretungen für die Q12</span><br<br><br><br><br>";}
} catch(PDOException $e) {
	echo "Error: " . $e->getMessage();
}

$conn = null;
?>
<spans class="w3-small">Alle Daten ohne Gewähr.</span>
</body>
</html>
