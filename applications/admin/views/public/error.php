<?php
$this->pageTitle=Yii::app()->name . ' - Error';
?>

<h2>Error <?php echo $code; ?></h2>

<div class="error">
<?php echo CHtml::encode($message); ?>
</div>
<div class="error">
<pre><?php echo CHtml::encode($trace); ?></pre>
</div>