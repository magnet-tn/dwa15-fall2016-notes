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
Create a variable called $winningNumber and set it to be
a winning number between 0 and 3 (0,1,2,3 = 4 contestants total)
You could pick a number yourself, or have PHP pick one for you using the rand() function
*/
$winningNumber = rand(0,3);

/*
This variable will be used for our bonus features to determine if there's no winner,
or if there's a tie (i.e., more than one winner)
*/
$winner_count = 0;


# Use a foreach loop to loop through the contestants array
foreach($contestants as $key => $value) {

	/*
	Create a variable called $roll which will represent what dice number this contestant "rolled"
	Set $roll to be a random number using PHP's rand() function. Just like your $winningNumber,
	limit this number between 0 and 3.
	*/
	$roll = rand(0,3);


	# If the user's roll matches the winning number...
	if($roll == $winningNumber) {

		# This contestant (i.e., $contestant[$key]) is set to be a "Winner"
		$contestants[$key] = 'Winner';

		# Increment the winner count
		$winner_count++;

	}
	else {
	# Otherwise...

		# This contestant (i.e., $contestant[$key]) is set to be a "Loser"
		$contestants[$key] = 'Loser';
	}

} # End of loop
