<?php
error_reporting(0);
require_once("date.func.php");

//判断是否有数据传入，分别给年月日赋值
if(!$_REQUEST['year']){
$year=date("Y");
}else{$year=$_REQUEST['year'];
}
if(!$_REQUEST['month']){
	$month=date("n");
}else $month=$_REQUEST['month'];
if(!$_REQUEST['day']){
	$day=date("d");
}else $day="1";
$oneday=strtotime($year."-".$month."-".$day);
?>


<!DOCTYPE html>
<html>
<head>
	<title>calendar</title>
<meta content="text/html;charset=utf-8" />
<style type="text/css">
td{width:30px;text-align: center;height: 20px;}
a{text-decoration: none;}
table{border-collapse: collapse;}
#today{background-color: red;}
#table{text-align: center;background-color: lightgreen; }
</style>
<script type="text/javascript">
</script>	
</head>
<body>
<div>
	<h1 style="text-align:center;">简单的日历</h1>
</div>
<div >
<center><!--居中显示-->
<table border="1" cellspacing="0" cellpadding="5" id="table">

<!--表头两行-->
<tr >
<td><a href="calendar.php?year=<?php echo (($year-1)<=1940)?1940:($year-1);?>&month=<?php echo $month;?>" ><<</a></td>
<td><a href="calendar.php?month=<?php echo (($month-1)<=0)?1:($month-1);?>&year=<?php echo $year;?>" ><</a></td>
<td><a href="calendar.php"> today</a></td>
<td><?php echo $year;?></td>
<td><?php echo $month;?></td>
<td><a href="calendar.php?month=<?php echo (($month+1)>=12)?12:($month+1);?>&year=<?php echo $year;?>" >></a></td>
<td><a href="calendar.php?year=<?php echo (($year+1)>=2050)?2050:($year+1);?>&month=<?php echo $month;?>" >>></a></td>
</tr>
<tr ><td>mon</td><td>tur</td><td>wed</td><td>thu</td><td>fri</td><td>sat</td><td>sun</td></tr>

<!--输出日期-->
<?php
$arrmonth=arrmonth($year);
$days=$arrmonth[$month-1];
if($day>$days)$day=$days;
$week=first_day_week($oneday,$day);
$week1=first_day_week($oneday,$day);
if($week1==0){
	$week=7;
	$week1=7;
}

//循环输出日期
for ($i=0; $i < $days+$week-1; $i++) { 
	if(--$week1>0){
		echo "<td></td>";
	}else{
		if($year==date("Y")&&$month==date("n") &&$day==($i+2-$week)){
			echo "<td id='today'>".($i+2-$week)."</td>";
	 	}else echo "<td>".($i+2-$week)."</td>";
	}
	if(($i+1)%7==0)echo"</tr><tr>";
}
echo "</tr>";
?>


</table>
</center>
</div>
</body>
</html>
