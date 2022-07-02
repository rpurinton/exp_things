<?php
require_once("../common.php");
$sql = mysqli_connect("127.0.0.1","exp","exp","exp");
if(isset($_POST["submit"]))
{
	extract($_POST);
	mysqli_query($sql,"INSERT INTO `explog` (`laozilvl`,`laozipct`,`laozips`,`laozitotal`,`laotzulvl`,`laotzupct`,`laotzups`,`laotzutotal`) VALUES ('$laozilvl','$laozipct','$laozips','".calcTotal($laozilvl,$laozipct)."','$laotzulvl','$laotzupct','$laotzups','".calcTotal($laotzulvl,$laotzupct)."')");
}
$row = mysqli_fetch_assoc(mysqli_query($sql,"SELECT * FROM `explog` ORDER BY `id` DESC LIMIT 0,1"));
$last = $row;
extract($row);
$last["diff"] = $laozitotal - $laotzutotal;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>EXP Tracker</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="EXP Tracker">
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
		<th rowspan="2">Date</th>
		<th colspan="6">Laozi</th>
		<th colspan="6">Lao Tzu</th>
		<th rowspan="3" colspan="2"><input type="submit" name="submit" value="submit" /></th>
	</tr>
	<tr>
		<th colspan="2">Level</th>
		<th colspan="2">EXP Percent</th>
		<th colspan="2">Power Score</th>
		<th colspan="2">Level</th>
		<th colspan="2">EXP Percent</th>
		<th colspan="2">Power Score</th>
	</tr>
	<tr>
		<td><?= date("m-d") ?></td>
		<td colspan="2"><select name="laozilvl">
<?php
	for($i=1;$i<192;$i++)
	{
		if($i == $laozilvl) echo("\t\t\t<option value=\"$i\" selected=\"selected\">$i</option>\n");
		else echo("\t\t\t<option value=\"$i\">$i</option>\n");
	}
?>
			</select></td>
		<td colspan="2"><input type="text" name="laozipct" placeholder="<?= $laozipct ?>%" size="10" /></td>
		<td colspan="2"><input type="text" name="laozips" placeholder="<?= $laozips ?>" size="10" /></td>
		<td colspan="2"><select name="laotzulvl">
<?php
	for($i=1;$i<192;$i++)
	{
		if($i == $laotzulvl) echo("\t\t\t<option value=\"$i\" selected=\"selected\">$i</option>\n");
		else echo("\t\t\t<option value=\"$i\">$i</option>\n");
	}
?>
			</select></td>
		<td colspan="2"><input type="text" name="laotzupct" placeholder="<?= $laotzupct ?>%" size="10" /></td>
		<td colspan="2"><input type="text" name="laotzups" placeholder="<?= $laotzups ?>" size="10" /></td>
	</tr>
</form>
<tr>
	<th></th>
	<th>Level</th>
	<th>Total EXP</th>
	<th>EXP Growth</th>
	<th>To Level</th>
	<th>Power Score</th>
	<th>PS Growth</th>
	<th>Level</th>
	<th>Total EXP</th>
	<th>EXP Growth</th>
	<th>To Level</th>
	<th>Power Score</th>
	<th>PS Growth</th>
	<th>Diff</th>
	<th>Diff Diff</th>
</tr>
<?php
$result = mysqli_query($sql,"SELECT * FROM `explog` ORDER BY `id` DESC");
while($row = mysqli_fetch_assoc($result))
{
	extract($row);
	$dt = date("m-d",strtotime($stamp));
	echo("<tr class=\"".getClass()."\">\n");
	echo("\t<td>$dt</td>\n");
	echo("\t<td>$laozilvl</td>\n");
	echo("\t<td>".myFormat($laozitotal)."</td>\n");
	echo("\t<td>".myFormat($last["laozitotal"] - $laozitotal)."</td>\n");
	echo("\t<td>".myFormat(toLevel($laozilvl,$laozipct))."</td>\n");
	echo("\t<td>".myFormat($laozips)."</td>\n");
	echo("\t<td>".myFormat($last["laozips"] - $laozips)."</td>\n");
	echo("\t<td>$laotzulvl</td>\n");
	echo("\t<td>".myFormat($laotzutotal)."</td>\n");
	echo("\t<td>".myFormat($last["laotzutotal"] - $laotzutotal)."</td>\n");
	echo("\t<td>".myFormat(toLevel($laotzulvl,$laotzupct))."</td>\n");
	echo("\t<td>".myFormat($laotzups)."</td>\n");
	echo("\t<td>".myFormat($last["laotzups"] - $laotzups)."</td>\n");
	echo("\t<td>".myFormat($laozitotal - $laotzutotal)."</td>\n");
	echo("\t<td>".myFormat($last["diff"] - ($laozitotal - $laotzutotal))."</td>\n");
	echo("</tr>\n");
	$last = $row;
	$last["diff"] = $laozitotal - $laotzutotal;
}
?>
        <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    </body>
</html>
