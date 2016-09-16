<?php
/*
Initiate a blank contestants array
We'll be filling this up with data from the $_POST array
*/
$contestants = [];

/*
Here's an example of what the $_POST Array looks like:
Array (
	[contestant0] => Alisha
	[contestant1] => Ben
	[contestant2] => Jill
	[contestant3] => Francis )
*/

# This variable will be used for our bonus features
$winnerCount = 0;

# Use a foreach loop to loop through the contestants array
foreach($_POST as $key => $value) {

	/*
	Create a variable $coinFlip; set this variable to be either 0 or 1 (i.e. heads or tails)
	Use PHP's rand() function to randomly pick the 0/1
	*/
	$coinFlip = rand(0,1);

	# If the $coinFlip was 1...
	if($coinFlip == 1) {

		# This contestant (i.e. $contestant[$value]) is set to be a "Winner"
		$contestants[$value] = 'Winner';

		# Increment the winner count
		$winnerCount++;

	}
	# Otherwise...
	else {

		# This contestant (i.e $contestant[$value]) is set to be a "Loser"
		$contestants[$value] = 'Loser';
	}

} # End of loop
