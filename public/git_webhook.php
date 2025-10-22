<?php
$command = "git pull origin main";
exec($command, $output);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($output);
?>
