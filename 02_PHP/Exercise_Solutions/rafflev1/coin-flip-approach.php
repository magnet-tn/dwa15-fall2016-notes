<?php
/*
Create an array with the four contestants
The keys of the array should be Sam, Eliot, Liz, and Max
We don't know if they're winners or losers yet, so we leave
the values blank by using two empty quotes (i.e., '')
*/
$contestants = [
	'Sam'   => '',
	'Eliot' => '',
	'Liz'   => '',
	'Max'   => ''
];

/*
This variable will be used for our bonus features to determine if there's no winner,
or if there's a tie (i.e., more than one winner)
*/
$winnerCount = 0;


# Use a foreach loop to loop through the contestants array
foreach($contestants as $key => $value) {

	/*
	Create a variable $coinFlip; set this variable to be either 0 or 1 (i.e., heads or tails)
	Use PHP's rand() function to randomly pick the 0/1
	*/
	$coinFlip = rand(0,1);

	# If the $coinFlip was 1...
	if($coinFlip == 1) {

		# This contestant (i.e., $contestant[$key]) is set to be a "Winner"
		$contestants[$key] = 'Winner';

		# Increment the winner count
    		$winnerCount++;

	}
	# Otherwise...
	else {

		# This contestant (i.e., $contestant[$key]) is set to be a "Loser"
		$contestants[$key] = 'Loser';
	}

} # End of loop
