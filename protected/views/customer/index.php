<?php
$this->pageTitle=Yii::app()->name . ' - Customer';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'customer-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('customer','Customer'); ?></strong>
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
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php 
			echo TbHtml::button('dummyButton', array('style'=>'display:none','disabled'=>true,'submit'=>'#',));
			if (Yii::app()->user->validRWFunction('A01'))
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Record'), array(
					'submit'=>Yii::app()->createUrl('customer/new'), 
				)); 
		?>
	</div>
	</div></div>
	<?php 
		$search = array(
						'code',
						'name',
						'full_name',
						'cont_name',
						'cont_phone',
					);
		$this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('customer','Customer List'),
			'model'=>$model,
				'viewhdr'=>'//customer/_listhdr',
				'viewdtl'=>'//customer/_listdtl',
				'gridsize'=>'24',
				'height'=>'600',
				'search'=>$search,
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

