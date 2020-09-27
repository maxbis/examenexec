<?php
use yii\helpers\Html;
?>
<br>
<div class="form-group">
      <?= Html::a('Cancel', ['index'], ['class'=>'btn btn-primary']) ?>
      &nbsp;&nbsp;&nbsp;
      <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>