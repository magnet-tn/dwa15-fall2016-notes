<?php
error_reporting(-1); # Report all PHP errors
ini_set('display_errors', 1); # Display errors to page
?>

<!DOCTYPE html>
<html>
<head>

	<title>PHPiggy Bank</title>

	<?php
	require('logic.php');
	?>

</head>

<body>

	<img alt='PHPiggy Bank Logo' src='http://thewc.co.s3.amazonaws.com/challenges/php-phpiggy-bank.png'>

	<p>
	    You have $<?php echo $total ?> in your piggy bank.
	</p>

	<img src='http://thewc.co.s3.amazonaws.com/challenges/<?php echo $image?>' alt='Goal Status'>

</body>
</html>
