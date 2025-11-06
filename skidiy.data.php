<?php
require('includes/sdk.php');

$ko = new ko();
$parkInfo = $ko->getParkInfo();//_j($parkInfo);
echo "var parkInfo = JSON.parse('".json_encode($parkInfo)."');";

$instructorInfo = $ko->getInstructorInfo();
echo "var instructorInfo = JSON.parse('".json_encode($instructorInfo)."');";

$exchangeRateInfo = $ko->getExchageRate();
echo "var exchangeRate = JSON.parse('".json_encode($exchangeRateInfo)."');";
?>