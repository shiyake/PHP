<?php

//判断瑞年返回每月天数
function arrmonth($onedayy){
if(($onedayy%4)==0){
	if ((($onedayy%100)==0)&&(($onedayy%400)==0) ){
		$arrmonth = array(31,29,31,30,31,30,31,31,30,31,30,31 );
		}
	else $arrmonth = array(31,28,31,30,31,30,31,31,30,31,30,31 );
}
$arrmonth = array(31,28,31,30,31,30,31,31,30,31,30,31 );
return $arrmonth;
}

//取得当月1日的星期
function first_day_week($oneday,$day){
$timestring=$oneday-($day-1)*24*60*60;
$first_day_to_week=date("w",$timestring);
return $first_day_to_week;
}
