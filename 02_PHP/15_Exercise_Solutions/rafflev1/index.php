<?php
error_reporting(-1); # Report all PHP errors
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'>
	<title>Raffle v1</title>
	<?php require 'coin-flip-approach.php'; ?>
</head>
<body>

	<h1>Raffle v1</h1>


	<!-- Print the contestants array -->
	<?php foreach($contestants as $key => $value) { ?>
		<?php echo $key; ?> is a <?php echo $value; ?><br>
	<?php } ?>


	<!-- Bonus features! -->
	<br>
	<?php if($winner_count == 0) { ?>

		No winners this round :(

	<?php } elseif($winner_count > 1) { ?>

		It's a tie!

	<?php } ?>

	<p>
		<a href='/raffle'>Play again...</a>
	</p>

</body>
</html>
