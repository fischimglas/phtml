# Phtml 
Easy to use html wrapper 

Code:
```php
<?php

// Create a simple HTML wrapper element
$div = Phtml::div(array('class' => 'foo'), 'hello ');

// Modify on the fly
$div->add('world');
$div->addClass('bar');

// Add another element
$div->add(Html::div(array(),'subdiv'));

// and just echo when done.
echo $div;
?>
```

Result:
```html
<div class="foo bar">hello world!<div>subdiv</div></div>
```
