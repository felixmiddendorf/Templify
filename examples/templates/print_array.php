<h1>Standard Output</h1>
<ul>
<?php
foreach($data as $element){
?>
  <li><?php echo $element; ?></li>
<?php
}
?>
</ul>

<h1>Escaped Output</h1>
<ul>
<?php
foreach($data as $element){?>
  <li><?php echo h($element); ?></li>
<?php
}
?>
</ul>