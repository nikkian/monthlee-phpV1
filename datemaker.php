<?php
include("includes/mydb-functions.php");
connect_to_db();

$sql = "SELECT * FROM homework";
$result = query($sql);
$num = mysqli_num_rows($result);
$i = 0;
$a = 0;
while($row = mysqli_fetch_array($result)){
    $i++;
    if($num == $i){
        $a = $row['due'];
    }
}

$a = empty($a)? date('U') : strtotime($a);
$string="INSERT INTO homework (due) VALUES ";
$days = [31,28,31,30,31,30,31,31,30,31,30,31];
    $b = intval(date('j',$a))+1;
    $c = intval(date('n',$a));
for($j=$c; $j<=12; $j++){
    for($z=1; $z<=$days[intval($j-1)]; $z++){
        $string .= "('2013-".$j."-".$z."'), ";
    }
}
$string .= "('2014-1-1');";
echo $string;

if(query($string)){
    echo "OK";
}



?>
