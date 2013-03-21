<?php
class bar{
	function foo( $var1, $closure ){
		print $var1."\n";
		print $closure()."\n";
	}
}

$bar = new bar();
$bar->foo( 'string1', function(){
	return 'string2';
});

$bar->foo( 'string3', 'string4');