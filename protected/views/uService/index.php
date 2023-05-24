<?php
$this->pageTitle=Yii::app()->name . ' - Task Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'uService-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','U Service Amount'); ?></strong>
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
        <?php echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t("summary",'Enquiry'), array(
            'submit'=>Yii::app()->createUrl('uService/view')));
        ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'search_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-10">
                    <?php echo $form->inlineRadioButtonList($model, 'search_type',UServiceForm::getSelectType(),
                        array('readonly'=>false,'id'=>'search_type')
                    ); ?>
                </div>
            </div>
            <div id="search_div">
                <div data-id="1" <?php if ($model->search_type!=1){ echo "style='display:none'"; } ?>>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'search_year',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-2">
                            <?php echo $form->dropDownList($model, 'search_year',SummarySetList::getSelectYear(),
                                array('readonly'=>false,'id'=>'year_one')
                            ); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'search_quarter',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-2">
                            <?php echo $form->dropDownList($model, 'search_quarter',SummarySetList::getSummaryMonthList(),
                                array('readonly'=>false)
                            ); ?>
                        </div>
                    </div>
                </div>
                <div data-id="2" <?php if ($model->search_type!=2){ echo "style='display:none'"; } ?>>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'search_year',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-2">
                            <?php echo $form->dropDownList($model, 'search_year',SummarySetList::getSelectYear(),
                                array('readonly'=>false,'id'=>'year_two')
                            ); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'search_month',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-2">
                            <?php echo $form->dropDownList($model, 'search_month',SummarySetList::getSelectMonth(),
                                array('readonly'=>false)
                            ); ?>
                        </div>
                    </div>
                </div>
                <div data-id="3" <?php if ($model->search_type!=3){ echo "style='display:none'"; } ?>>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'search_start_date',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-2">
                            <?php echo $form->textField($model, 'search_start_date',
                                array('readonly'=>false,'prepend'=>"<span class='fa fa-calendar'></span>")
                            ); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'search_end_date',array('class'=>"col-sm-2 control-label")); ?>
                        <div class="col-sm-2">
                            <?php echo $form->textField($model, 'search_end_date',
                                array('readonly'=>false,'prepend'=>"<span class='fa fa-calendar'></span>")
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->dropDownList($model, 'city',UServiceForm::getCityList(),
                        array('readonly'=>false)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'condition',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->dropDownList($model, 'condition',UServiceForm::getConditionList(),
                        array('readonly'=>false)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'seniority_min',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->numberField($model, 'seniority_min',
                        array('readonly'=>false,'min'=>0)
                    ); ?>
                </div>
                <div class="pull-left text-center">
                    <p class="form-control-static"> 至 </p>
                </div>
                <div class="col-sm-2">
                    <?php echo $form->numberField($model, 'seniority_max',
                        array('readonly'=>false,'min'=>0)
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>


<?php
$js="
    $('#year_one,#year_two').change(function(){
        var year = $(this).val();
        $('#year_one,#year_two').val(year);
    });
    $('input[type=radio]').change(function(){
        var id = $(this).val();
        console.log(id);
        $('#search_div').children('div').hide();
        $('#search_div').children('div[data-id='+id+']').show();
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDatePicker(array(
    'UServiceForm_search_start_date',
    'UServiceForm_search_end_date'
));
Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


