<div class="row">
<table class="centered">
  <thead>
    <th>日</th>
    <th>ㄧ</th>
    <th>二</th>
    <th>三</th>
    <th>四</th>
    <th>五</th>
    <th>六</th>
  </thead>
  <tbody>

<?php 
$cnt = 1;
foreach ($calendar as $date => $s) {
  $d = date('d', strtotime($date));
  $today = ($date==$in['date']) ? 'today' : null;
  if($cnt%7==1) echo "<tr>\n";

  echo "\t<td>\n\t\t<div class=\"date {$today}\">\n";
  echo "\t\t\t<div class=\"day\">{$d}</div>\n";
    foreach ($s as $slot => $lesson) {
      foreach ($lesson as $park => $instructors) {
        foreach ($instructors as $instructor => $extraInfo) {
          echo "\t\t\t
                <div oidx=\"{$extraInfo['oidx']}\"
                  schedule=\"x={$extraInfo['sidx']},d={$date},s={$slot},p={$park},i={$instructor},e={$extraInfo['expertise']}{$extraInfo['rule']}\">
                  </span>{$park}
                </div>\n";
        }//foreach instructor
      }//foreach park
    }//foreach slot
  echo "\t\t</div>\n\t</td>\n";

  $cnt++;
  if($cnt%7==1) echo "</tr>\n\n";
}//foreach date
?>

  </tbody>
</table>
</div>