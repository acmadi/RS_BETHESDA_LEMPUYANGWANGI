<?php

$PID = "input_resep";

require_once("../lib/dbconn.php");
require_once("../lib/querybuilder.php");

$qb = New InsertQuery();
$qb->TableName = "rl100012b";
$qb->HttpAction = "POST";
$qb->VarPrefix = "f_";

/*$qb->VarTypeIsDate = Array("tgl_lahir");*/

$qb->addFieldValue("id", "nextval('rl100012b_seq')");
$SQL = $qb->build();

pg_query($con, $SQL);
header("Location: ../index2.php?p=$PID");

exit;

?>
