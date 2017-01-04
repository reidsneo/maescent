<?php 
$file = fopen("file.txt", "r");
while(!feof($file)){
    $line = fgets($file);
   if (strpos($line, 'HOST;ADD;') !== false) {
    echo $line."<br>";
	}
}
fclose($file);

 ?>