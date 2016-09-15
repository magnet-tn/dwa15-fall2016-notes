## User defined

[Functions](http://us1.php.net/manual/en/language.functions.php) are a mechanism for encapsulating statements of code together; they're useful because they allow you repeat tasks without repeating code.

For example:

~~~php
function calculateTotal($subtotal, $discount, $shippingMethod) {

    if($shippingMethod == 'priority') {
        $shippingRate = 5;
    }
    elseif($shippingMethod == 'express') {
        $shippingRate = 15;
    }

    $tax = .09 * $subtotal;

    $total = $subtotal + $shippingRate - $discount;

    return $total;

}
~~~

You would then invoke the above function like this:

~~~php
Your total is <?php echo calculateTotal(10,3,'priority'); ?>
~~~

If you imagine an online store, there are many actions that might necessitate a calculation re-total (for example, adding an item to the cart, changing your shipping method, etc.). Each of these actions could rely on the above function to re-calculate a new total.

## Arguments
Functions may accept arguments which provide extra information regarding how the function should operate.

In the above example the arguments were subtotal (10), discount (3) and shipping method ('priority').

If a function doesn't need arguments, then the parentheses would be left empty. Example:

~~~php
function getCurrentSale() {

    # Read and return the the contents of currentSale.txt
    return file("currentSale.txt");

}

$sale = getCurrentSale();
~~~


## Return
A function can return values upon completion. For example, in the previous challenge, we could turn our *"Is the savings goal met?"* logic into a function called `getGoalImage` that would return the appropriate image name.

Try it...what would a `getGoalImage()` function look like?


## Built-in functions
In addition to creating your own functions, PHP has many useful [built-in (internal)](http://us2.php.net/manual/en/functions.internal.php) functions.

~~~php
string date ( string $format [, int $timestamp = time() ] )
~~~

Use the [PHP date function](http://us1.php.net/manual/en/function.date.php) to produce the following time formats:

* 4:30pm
* 16 hundred hours
* April 14, 2014 4:00pm
