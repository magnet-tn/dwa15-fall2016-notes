<?php

/*
Create an array with the four contestants
The keys of the array should be Sam, Eliot, Liz, and Max
Default them all to Losers.
*/
$contestants = [
	'Sam'   => 'Loser',
	'Eliot' => 'Loser',
	'Liz'   => 'Loser',
	'Max'   => 'Loser'
];

$winner = array_rand($contestants);
$contestants[$winner] = 'Winner';
