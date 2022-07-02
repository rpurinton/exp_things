<?php
require_once("../common.php");
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
if($lang !== "es" && $lang !== "pt") $lang = "en";
require_once("lang/$lang.php");
if(isset($_COOKIE["session_id"]))
{
	session_id($_COOKIE["session_id"]);
	session_start();
}
else
{
	session_start();
	setcookie("session_id",session_id(), time()+(365*24*3600),"/timer/","mir4.gq",1);
}
if(isset($_POST["reset"]))
{
	$_SESSION["started"] = false;
	unset($_SESSION["records"]);
}
if(isset($_POST["submit"]))
{
	if($_POST["pct"] == "") $_POST["pct"] = 0;
	$record["time"] = time();
	if($_POST["submit"] == $text["start"])
	{
		$_SESSION["started"] = true;
		$_SESSION["start_time"] = time();
		$_SESSION["start_pct"] = $_POST["pct"];
		$record["pct"] = $_POST["pct"];
		$record["level"] = $_POST["level"];
	}
	else
	{
		$_POST["submit"] = str_replace("%","",$_POST["submit"]);
		$_POST["submit"] = str_replace($text["click"]." EXP ","",$_POST["submit"]);
		if($_POST["submit"] == 100)
		{
			$_SESSION["level"]++;
			$_POST["submit"] = 0;
		}
		$record["pct"] = $_POST["submit"];
		$record["level"] = $_SESSION["level"];
	}
	$record["total"] = calcTotal($record["level"],$record["pct"]);
	$_SESSION["level"] = $record["level"];
	$_SESSION["pct"] = $record["pct"];
	$_SESSION["records"][] = $record;
}
?>
<!DOCTYPE html>
<html lang="<?php echo($lang); ?>">
<head>
    <meta charset="utf-8">
    <title>Mir4 <?php echo($text["clock"]); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mir4 <?php echo($text["clock"]); ?>">
    <meta name="author" content="Laozi">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        html,body{
            background-color:#111;
            color: #ddd;
            width: 100%;
            min-height: 100%;
        }
        table,tr,td,th,input,select {
            background-color:#111;
            color: #eee;
            padding: 10px;
	    min-width:128px;
            border-spacing: 10px;
	    border: 1px solid #444;
            border-collapse: seperate;
            vertical-align: middle;
        }
        input, select {
            background-color: #111;
            background-image: linear-gradient(to bottom, #333, transparent); 
        }
        table {
            table-layout: fixed;
            overflow-wrap: break-word;
        }
        th {
            text-align: right;
            background-color:#000;
            color:#fff;
        }
        .rowlight {
            background-color:#222;
        }
        .rowDark {
            background-color:#111;
        }
        .exp {
            color: cyan;
            text-align: center;
            width: 100%;
        }
        .timer {
        }
        .header {
            vertical-align: text-bottom;
            font-size: 275%;
        }
        a:link, a:visited, a:hover, a:active
        {
            color:#ddd;
            text-decoration: none;
        }
    </style>
</head>
<body>
<br />
<center>
<div class="header"><img src="images/mir4.png" /><?php echo($text["clock"]); ?></div>
<?php
if(!isset($_SESSION["started"]) || (isset($_SESSION["started"]) && !$_SESSION["started"]))
{
	if(isset($_SESSION["pct"])) $pct_default = $_SESSION["pct"];
	else $pct_default = "";
?>
    <form method="POST">
        <table>
            <tr>
		<th><?php echo($text["currentlevel"]); ?></th>
                <td>
                    <select name="level">
                    <?php for($i=20;$i<192;$i++)
                          {
                              if(isset($_SESSION["level"]) && $_SESSION["level"] == $i) echo("<option value=\"$i\" selected=\"selected\">$i</option>\n"); 
                              else echo("<option value=\"$i\">$i</option>\n"); 
                          }
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php echo($text["startingexp"]); ?></th>
                <td>
                    <input id="pct" type="number" min="0" max="100" step="0.0001" accuracy="4" name="pct" value="<?php echo($pct_default); ?>" autofocus onfocus="let tmp_val = this.value; this.value = ''; this.value = tmp_val;" onchange="this.value = parseFloat(this.value).toFixed(4);" /> %
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" style="width: 100%;" name="submit" value="<?php echo($text["start"]); ?>" />
                </td>
            </tr>
        </table>
    </form>
<?php
}
else
{
    $level = $_SESSION["level"];
    $pct = $_SESSION["pct"];
    $start_time = $_SESSION["start_time"];
    $start_pct = $_SESSION["start_pct"];
    ?>
    <form method="POST">
        <table>
            <tr><th colspan="2"><input class="exp" type="submit" name="submit" value="<?php echo($text["click"]); ?> EXP <?php echo(number_format(round_up($pct+0.0001,0.01),4)); ?>%" /> </th></tr>
            <tr><th><?php echo($text["elapsed"]); ?></th><td><div id="timer" class="timer"><?php echo(minsec(time()-$start_time)); ?> s</div></td></tr>
            <tr><th>EXP <?php echo($text["until"]); ?> <?php echo($level+1); ?></th><td><?php echo(myFormat(toLevel($level,$pct))); ?></td></tr>
    <?php
    foreach($_SESSION["records"] as $key => $value)
    {
        if($key === 0)
        {
            $start_time = $value["time"];
            $start_total = $value["total"];
        }
        else
        {
            $timediff = $value["time"] - $_SESSION["records"][$key-1]["time"];
            $totaldiff = $value["total"] - $_SESSION["records"][$key-1]["total"];
            $expperhour = ($totaldiff / $timediff)*3600;
            $expperhour_formated = myFormat($expperhour);
            $toLevel = toLevel($value["level"],$value["pct"]);
        }
    }
    if($key > 0)
    {
        $overall_time = $value["time"] - $start_time;
        $overall_total = $value["total"] - $start_total;
        $overall_exppermin = ($overall_total / $overall_time)*60;
        $overall_expperhour = ($overall_total / $overall_time)*3600;
        $timeToLevel = timeHuman($toLevel/$overall_expperhour);
        echo("<tr><th>Time ".$text["until"]." ".($level+1)."</th><td>$timeToLevel</td></tr>");
        echo("<tr><th>EXP ".$text["gained"]."</th><td>".myFormat($overall_total)."</td></tr>");
        echo("<tr><th>EXP ".$text["perhour"]."</th><td>".myFormat($overall_expperhour)."</td></tr>");
    }
    echo("<tr><th colspan=\"2\"><input style=\" width: 100%;\" type=\"submit\" name=\"reset\" value=\"".$text["reset"]."\" /></th></tr>\n");
    echo("        </table>\n    </form>\n");
}
?>
        </table>
    <h6>&copy;2022 <?php echo($text["by"]); ?> <a href="https://discordapp.com/users/363853952749404162">Laozi 老子</a></h6>
    <br />
    <?php echo($text["chatwith"]); ?><br />
    <a href="https://discord.gg/zRjqXY5gDv" target="_blank"><img style="width: 128px;" src="images/discord.png" /></a>
    </center>
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script type="text/javascript">
    elapsed = <?php if(isset($start_time)) echo(time()-$start_time); else echo(0); ?>;
    setInterval(updateTimer,1000);
    $(document).ready(function () {
        $(".pct").change(function() {
            $(this).val(parseFloat($(this).val()).toFixed(2));
        });
    });
    function updateTimer()
    {
        elapsed++;
	<?php if(isset($start_time)) echo("    $(\".timer\").text(minsec(elapsed)+\" s\");\n"); ?>
    }
    function minsec(seconds)
    {
         minutes = Math.floor(seconds/60);
         seconds = seconds % 60;
         if(minutes) return minutes + " m " + seconds;
         else return seconds;
    }
    </script>
</body>
</html>
