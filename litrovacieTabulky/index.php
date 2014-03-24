<?php
$radius = 0;
$length = 0;
$step = 1;
$heigth = 0;
$depth = 0;

//lezaty valec
if(isset($_POST['submit'])) {
	if(isset($_POST['diameter']) && is_numeric($_POST['diameter']) && $_POST['diameter'] % 2 == 0)	{
		if($_POST['diameter'] > 0)
			$radius = $_POST['diameter'] / 2;
		else
			$error[] = 'Neplatný polomer.';
	} else
		$error[] = 'Neplatný polomer.';


	if(isset($_POST['length']) && is_numeric($_POST['length'])) {
		if($_POST['length'] > 0)
			$length = $_POST['length'];
		else
			$error[] = 'Neplatná dĺžka.';
	} else
		$error[] = 'Neplatná dĺžka.';

	if(isset($_POST['step']) && is_numeric($_POST['step']))
		$step = $_POST['step'];
	else
		$error[] = 'Neplatný krok.';

	if(isset($_POST['output']) && is_numeric($_POST['output']))
		$output = $_POST['output'];
	else
		$error[] = 'Neplatný typ výstupnej jednotky.';
}

// kvader
if(isset($_POST['submit_cube'])) {
	if(isset($_POST['heigth']) 
			&& is_numeric($_POST['heigth']) 
			&& $_POST['heigth'] % 2 == 0)	{
		if($_POST['heigth'] > 0)
			$heigth = $_POST['heigth'];
		else
			$error[] = 'Neplatná výška.';
	} else
		$error[] = 'Neplatná výška.';


	if(isset($_POST['length']) && is_numeric($_POST['length'])) {
		if($_POST['length'] > 0)
			$length = $_POST['length'];
		else
			$error[] = 'Neplatná dĺžka.';
	} else
		$error[] = 'Neplatná dĺžka.';

	if(isset($_POST['depth']) && is_numeric($_POST['depth'])) {
		if($_POST['depth'] > 0)
			$depth = $_POST['depth'];
		else
			$error[] = 'Neplatná hĺbka.';
	} else
		$error[] = 'Neplatná hĺbka.';

	if(isset($_POST['step']) && is_numeric($_POST['step']))
		$step = $_POST['step'];
	else
		$error[] = 'Neplatný krok.';

	if(isset($_POST['output']) && is_numeric($_POST['output']))
		$output = $_POST['output'];
	else
		$error[] = 'Neplatný typ výstupnej jednotky.';
}

// kvader a kruh
if(isset($_POST['submit_cubeAndCircle'])) {
	if(isset($_POST['height']) 
			&& is_numeric($_POST['height']))	{
		if($_POST['height'] > 0)
			$height = $_POST['height'];
		else
			$error[] = 'Neplatná výška.';
	} else
		$error[] = 'Neplatná výška.';


	if(isset($_POST['width']) && is_numeric($_POST['width'])) {
		if($_POST['width'] > 0)
			$width = $_POST['width'];
		else
			$error[] = 'Neplatná šírka.';
	} else
		$error[] = 'Neplatná šírka.';

	if(isset($_POST['depth']) && is_numeric($_POST['depth'])) {
		if($_POST['depth'] > 0)
			$depth = $_POST['depth'];
		else
			$error[] = 'Neplatná hĺbka.';
	} else
		$error[] = 'Neplatná hĺbka.';
	
	if(isset($_POST['arcHeight']) && is_numeric($_POST['arcHeight'])) {
		if($_POST['arcHeight'] > 0)
			$arcHeight = $_POST['arcHeight'];
		else
			$error[] = 'Neplatná hĺbka.';
	} else
		$error[] = 'Neplatná hĺbka.';

	if(isset($_POST['step']) && is_numeric($_POST['step']))
		$step = $_POST['step'];
	else
		$error[] = 'Neplatný krok.';

	if(isset($_POST['output']) && is_numeric($_POST['output']))
		$output = $_POST['output'];
	else
		$error[] = 'Neplatný typ výstupnej jednotky.';
}

require_once('./class.circleSegment.php');
require_once('./class.cubeSegment.php');
require_once('./class.cubeAndCircleSegment.php');

if(isset($_POST['submit']))
	$circle = new circleSegment($radius, $length, $step, $output);

if(isset($_POST['submit_cube'])) {
	$cube = new cubeSegment($heigth, $length, $depth, $step, $output);
}

if(isset($_POST['submit_cubeAndCircle'])) {
	$cubeAndCircle = new cubeAndCircleSegment($height, $width, $depth, $arcHeight, $step, $output);
}

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Výpočet litrovacej tabuľky</title>
</head>
<body>
<h3>Ked zadavate desatinne cislo, zadavajte ho pomocou "." (bodky) a nie ciarky.</h3>
<form action="index.php" method="POST">
Lezaty valec
<table border="0">
	<tr>
		<th>Priemer:</th>
		<td><input type="text" name="diameter" />cm</td>
	</tr>
	<tr>
		<th>Dĺžka:</th>
		<td><input type="text" name="length" />cm</td>
	</tr>
	<tr>
		<th>Krok:</th>
		<td><input type="text" name="step" />cm</td>
	</tr>
	<tr>
		<th>Výstupné jednotky:</th>
		<td>
			<select name="output">
				<option value="0.000000000001">km3</option>
				<option value="0.000000001">hm3</option>
				<option value="0.000001">m3</option>
				<option value="0.001" selected="selected">dm3 / l</option>
				<option value="1">cm3</option>
				<option value="1000">mm3</option>
			</select>
		</td>
	</tr>
	<tr align="center">
		<td colspan="2">
			<input type="submit" name="submit" value="Vypočítaj" />
		</td>
	</tr>
</table>
</form>

<form action="index.php" method="POST">
Kvader a valec
<table border="0">
	<tr>
		<th>Výška:</th>
		<td><input type="text" name="height" />cm</td>
	</tr>
	<tr>
		<th>Šírka:</th>
		<td><input type="text" name="width" />cm</td>
	</tr>
	<tr>
		<th>Hĺbka:</th>
		<td><input type="text" name="depth" />cm</td>
	</tr>
	<tr>
		<th>Výška zahnutia:</th>
		<td><input type="text" name="arcHeight" />cm</td>
	</tr>
	<tr>
		<th>Krok:</th>
		<td><input type="text" name="step" />cm</td>
	</tr>
	<tr>
		<th>Výstupné jednotky:</th>
		<td>
			<select name="output">
				<option value="0.000000000001">km3</option>
				<option value="0.000000001">hm3</option>
				<option value="0.000001">m3</option>
				<option value="0.001" selected="selected">dm3 / l</option>
				<option value="1">cm3</option>
				<option value="1000">mm3</option>
			</select>
		</td>
	</tr>
	<tr align="center">
		<td colspan="2">
			<input type="submit" name="submit_cubeAndCircle" value="Vypočítaj" />
		</td>
	</tr>
</table>
</form>

<form action="index.php" method="POST">
Kvader
<table border="0">
	<tr>
		<th>Výška:</th>
		<td><input type="text" name="heigth" />cm</td>
	</tr>
	<tr>
		<th>Šírka:</th>
		<td><input type="text" name="length" />cm</td>
	</tr>
	<tr>
		<th>Hĺbka:</th>
		<td><input type="text" name="depth" />cm</td>
	</tr>
	<tr>
		<th>Krok:</th>
		<td><input type="text" name="step" />cm</td>
	</tr>
	<tr>
		<th>Výstupné jednotky:</th>
		<td>
			<select name="output">
				<option value="0.000000000001">km3</option>
				<option value="0.000000001">hm3</option>
				<option value="0.000001">m3</option>
				<option value="0.001" selected="selected">dm3 / l</option>
				<option value="1">cm3</option>
				<option value="1000">mm3</option>
			</select>
		</td>
	</tr>
	<tr align="center">
		<td colspan="2">
			<input type="submit" name="submit_cube" value="Vypočítaj" />
		</td>
	</tr>
</table>
</form>
</body>
</html>