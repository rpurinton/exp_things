<?php
$ticker = false;
$levels = json_decode(file_get_contents(__DIR__."/levels.json"),true);

function getClass()
{
	global $ticker;
	if($ticker)
	{
		$ticker = false;
		return "rowDark";
	}
	$ticker = true;
	return "rowLight";
}

function toLevel($level,$pct)
{
	global $levels;
	$remain = 100 - $pct;
	$remain = $levels[$level+1] * ($remain/100);
	return($remain);
}

function calcTotal($level,$pct)
{
	global $levels;
	$total = 0;
	for($i=1;$i<=$level;$i++)
	{
		$total += $levels[$i];
	}
	$total += floor($levels[$level+1] * ($pct/100));
	return $total;
}

function myFormat($num)
{
	if($num < 0)
	{
		$neg = -1;
		$num *= $neg;
	}
	else $neg = 1;
	if($num == 0) return "---";
	if($num < 1000) return $num*$neg;
	$num = $num / 1000;
	if($num < 1000) return(number_format($num*$neg,1)."K");
	$num = $num / 1000;
	if($num < 1000) return(number_format($num*$neg,1)."M");
	return(number_format($num*$neg/1000,3)."B");
}

function timeHuman($input)
{
	$hours = floor($input);
	$remainder = $input - $hours;
	$mins = round($remainder*60,0);
	return("$hours h $mins m");
}

function round_up($int, $n) {
    return ceil($int / $n) * $n;
}

function minsec($seconds)
{
	$minutes = floor($seconds/60);
	$seconds = $seconds % 60;
	if($minutes) return ("$minutes m $seconds");
	return ($seconds);
}
