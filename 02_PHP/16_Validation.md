Using the PHP features we've covered so far, we could incorporate basic server-side validation into our raffle exercise.

[To see this in action, see Raffle v3...](https://github.com/susanBuck/dwa15-php-practice/tree/master/rafflev3)

Summary:
+ The [`ctype_alpha`](http://php.net/manual/en/function.ctype-alpha.php) function is used to see if any of the contestant names include numbers or symbols.
+ A simple boolean expression is used to see if any of the contestant names are empty (`if(trim($value == '') == ''...`)
+ If either of the above conditions are met, a variable `$error` is set to a String describing the error, and the foreach loop is exited with a `return statement`
+ In the display page, it looks to see if `$error` is set, and if it is, displays it.

You could also use a function like [`preg_match`](http://php.net/manual/en/function.preg-match.php) in combination with a regular expression to validate what characters you want.
