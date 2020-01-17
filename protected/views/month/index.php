<?php
$this->pageTitle=Yii::app()->name . ' - Month Report';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'month-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('monthly','Monthly Report Data'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>

<section class="content">

            <div class="btn-group" role="group">
                <?php
                echo TbHtml::button('dummyButton',array('style'=>'display:none','disabled'=>true,'submit'=>'#',));
                ?>
            </div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('monthly','Monthly Report Data List'),
			'model'=>$model,
				'viewhdr'=>'//month/_listhdr',
				'viewdtl'=>'//month/_listdtl',
				'gridsize'=>'24',
				'height'=>'600',
				'search'=>array(
							'year_no',
							'month_no',
						),
                'city'=>'city',
		));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

