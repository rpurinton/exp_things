<?php
session_start();
$ticker = false;
$levels = json_decode(file_get_contents("levels.json"),true);
if(isset($_POST["reset"])) unset($_SESSION["records"]);
if(isset($_POST["submit"]))
{
	$record["time"] = time();
	$record["pct"] = $_POST["pct"];
	$record["level"] = $_POST["level"];
	$record["total"] = calcTotal($_POST["level"],$_POST["pct"]);
	$_SESSION["level"] = $_POST["level"];
	$_SESSION["pct"] = $_POST["pct"];
	$_SESSION["records"][] = $record;
}
if(isset($_SESSION["level"])) $level = $_SESSION["level"];
else $level = 1;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>EXP Stop Watch</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="EXP Stop Watch">
        <meta name="author" content="Laozi">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <style>
            html,body{
                background-color:#222;
                color: #ddd;
                width: 100%;
            }
            table,tr,td,th,input,select {
                border: 1px solid;
                padding: 5px;
		text-align: center;
		vertical-align: middle;
            }
            table {
                width: 100%;
                table-layout: fixed;
                overflow-wrap: break-word;
            }
	    th {
		background-color:#000;
		color:#fff;
		}
            .tdmin {
                width: 120px;
            }
	    .rowlight {
                background-color:#222;
		}
	    .rowDark {
		background-color:#111;
		}
        </style>
    </head>
    <body>
        <form method="POST">
            <table>
                <tr>
                    <td>
                        <select name="level">
<?php
for($i=1;$i<192;$i++)
{
	if($i == $level) echo("                        <option value=\"$i\" selected=\"selected\">$i</option>\n");
	else echo("                        <option value=\"$i\">$i</option>\n");
}
if(isset($_SESSION["pct"])) $placeholder = $_SESSION["pct"];
else $placeholder = "0.0000";
?>
			</select>
                    </td>
                    <td>
                        <input type="text" name="pct" placeholder="<?php echo($placeholder); ?>" autofocus />
                    </td>
                    <td>
                        <input type="submit" name="submit" value="Submit" />
                        <input type="submit" name="reset" value="Reset" />
                    </td>
                </tr>
<?php
foreach($_SESSION["records"] as $key => $value)
{
	echo("                    <tr class=\"".getClass()."\">\n");
	echo("                        <td>".date("H:i:s",$value["time"])."</td>\n");
	echo("                        <td>Lap ".($key+1)."</td>\n");
	if($key === 0)
	{
		$start_time = $value["time"];
		$start_total = $value["total"];
		echo("                        <td>&nbsp;</td>\n");
	}
	else
	{
		$timediff = $value["time"] - $_SESSION["records"][$key-1]["time"];
		$totaldiff = $value["total"] - $_SESSION["records"][$key-1]["total"];
		$expperhour = ($totaldiff / $timediff)*3600;
		echo("                        <td>".myFormat($expperhour)." per Hour</td>\n");
	}
	echo("                    </tr>\n");
}
if($key > 0)
{
	$overall_time = $value["time"] - $start_time;
	$overall_total = $value["total"] - $start_total;
	$overall_expperhour = ($overall_total / $overall_time)*3600;
	echo("                    <tr class=\"".getClass()."\">\n");
	echo("                        <td>&nbsp;</td>\n");
	echo("                        <td>Overall</td>\n");
	echo("                        <td>".myFormat($overall_expperhour)." per Hour</td>\n");
	echo("                    </tr>\n");
}


?>
            </table>
        </form>
        <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    </body>
</html>

<?php

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
