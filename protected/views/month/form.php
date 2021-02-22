<?php
$this->pageTitle=Yii::app()->name . ' - Month Report Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'monthly-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('monthly','Monthly Report Data Form'); ?></strong>
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
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('month/index')));
		?>

			<?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('misc','xiazai'), array(
				'submit'=>Yii::app()->createUrl('month/xiazai')));
			?>
        <?php echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','ZongJie'), array(
            'submit'=>Yii::app()->createUrl('month/summarize',array('index'=> $model->id,'city'=>$_GET['city']))));
        ?>
	</div>
	</div></div>
    <div class="btn-group">
        <button id="hide" type="button" class="btn btn-default" style="width: 555px;font-size: 16px">月报数据</button>
        <button id="show" type="button" class="btn btn-default" style="width: 555px;font-size: 16px"">部门明细</button>
    </div>

	<div class="box box-info" id="p">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'year_no',array('class'=>"col-sm-1 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'year_no', 
						array('size'=>10,'readonly'=>true)
					); ?>
				</div>
				<?php echo $form->labelEx($model,'month_no',array('class'=>"col-sm-1 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'month_no', 
						array('size'=>10,'readonly'=>true)
					); ?>
				</div>
			</div>
	
			<legend>&nbsp;</legend>

<?php
	$modelName = get_class($model);
	$cnt=0;
	foreach ($model->record as $key=>$data) {
		$cnt++;
		$id_prefix = $modelName.'_record_'.$key;
		$name_prefix = $modelName.'[record]['.$key.']';
		echo '<div class="form-group">';
		echo '<div class="col-sm-4">';
		echo  TbHtml::label($cnt.'. '.$data['name'].($data['updtype']!='M' ? ' *' : ''),$id_prefix.'_datavalue');
		echo '</div>';
		echo '<div class="col-sm-3">';
		echo TbHtml::textField($name_prefix.'[datavalue]',$data['datavalue'],
				array('size'=>40,'maxlength'=>100,'class'=>($data['updtype']!='M' ? 'bg-gray' : ''),'readonly'=>($model->scenario=='view'||$data['updtype']!='M'))
			);		
		echo TbHtml::hiddenField($name_prefix.'[id]',$data['id']);
		echo TbHtml::hiddenField($name_prefix.'[code]',$data['code']);
		echo TbHtml::hiddenField($name_prefix.'[name]',$data['name']);
		echo TbHtml::hiddenField($name_prefix.'[datavalueold]',$data['datavalueold']);
		echo TbHtml::hiddenField($name_prefix.'[updtype]',$data['updtype']);
		echo TbHtml::hiddenField($name_prefix.'[fieldtype]',$data['fieldtype']);
		echo TbHtml::hiddenField($name_prefix.'[manualinput]',$data['manualinput']);
        echo TbHtml::hiddenField($name_prefix.'[excel_row]',$data['excel_row']);
		echo '</div>';
		echo '</div>';
	}
?>
		</div>
	</div>
    <div id="s" class="box box-info" style="display: none">
        <style type="text/css">
            .tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
            .tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
            .tftable tr {background-color:#d4e3e5;}
            .tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
        </style>

        <table class="tftable" border="1">
            <tr><th colspan="2" >管理项目</th><th style="width: 10%">成绩</th><th style="width: 10%">评分标准</th><th style="width: 10%">得分</th><th style="width: 12%">部门得分/总得分</th></tr>
            <tr><td colspan="5"></td><td>总分(100分）：<?php echo $model->excel['f74'];?></td></tr>
            <tr><td style="width: 10%">銷售部</td><td colspan="4"></td><td><?php echo $model->excel['f75'];?></td></tr>
            <tr><td rowspan="8">新生意情况</td><td>傳統服務(IA,IB)新生意年金額增長 （(當月-上月)/上月)</td><td><?php echo $model->excel['c76'];?></td><td> -15% - -30%    :  1
                    <br/><0% - -14%   :  2
                    <br/>>=0% - 8%   :  3
                    <br/>9% - 14%   :  4
                    <br/>>= 15% :  5
                    <br/></td><td><?php echo $model->excel['e76'];?></td><td></td></tr>
            <tr><td>傳統服務(IA,IB)新生意年金額橫比增長 （(當月-去年當月）/去年當月)</td><td><?php echo $model->excel['c77'];?></td><td> -15% - -30%    :  1
                    <br/><0% - -14%   :  2
                    <br/>>=0% - 8%   :  3
                    <br/>9% - 14%   :  4
                    <br/>> 15% :  5 </td><td><?php echo $model->excel['e77'];?></td><td></td></tr>
            <tr><td>傳統服務(IA,IB)新生意合同數量增長（(當月-上月)/上月)</td><td><?php echo $model->excel['c78'];?></td><td>-10% ~ -20% : 1
                    <br/>4% ~ -9% : 2
                    <br/>5% ~ 19% : 3
                    <br/>20% ~ 29% : 4
                    <br/>> =30% : 5</td><td><?php echo $model->excel['e78'];?></td><td></td></tr>
            <tr><td>傳統服務(IA,IB)新生意合同數量橫比增長（(當月-去年當月)/去年當月)</td><td><?php echo $model->excel['c79'];?></td><td>-10% ~ -20% : 1
                    <br/>0% ~ -9% : 2
                    <br/>10% ~ 19% : 3
                    <br/>20% ~ 29% : 4
                    <br/>> =30% : 5</td><td><?php echo $model->excel['e79'];?></td><td></td></tr>
            <tr><td>新興業務(空氣淨化機, 飄盈香，甲醛，廚房或其他)新生意年金額增長 （(當月-上月)/上月)</td><td><?php echo $model->excel['c80'];?></td><td>-100% ~ -200% : 1
                    <br/><0% ~ -99% : 2
                    <br/>>=0% ~ 99% : 3
                    <br/> 100% ~ 149% : 4
                    <br/>>= 150% : 5</td><td><?php echo $model->excel['e80'];?></td><td></td></tr>
            <tr><td>新興業務(空氣淨化機,飄盈香，甲醛，廚房或其他)新生意年金額橫比增長 （(當月-去年當月)/去年當月)</td><td><?php echo $model->excel['c81'];?></td><td>-100% ~ -200% : 1
                    <br/> <0% ~ -99% : 2
                    <br/>  >=0% ~ 99% : 3
                    <br/> 100% ~ 149% : 4
                    <br/> >= 150% : 5</td><td><?php echo $model->excel['e81'];?></td><td></td></tr>
            <tr><td>公司整體生意年金額淨增長比例（（當月-上月）/上月）</td><td><?php echo $model->excel['c82'];?></td><td>-10% ~ -20% : 1
                    <br/> <0% ~ -9% : 2
                    <br/>  >=0% ~ 8% : 3
                    <br/> 9% ~ 14% : 4
                    <br/> >= 15% : 5</td><td><?php echo $model->excel['e82'];?></td><td></td></tr>
            <tr><td>公司整體生意淨增長年金額橫比比例（（當月-去年當月）/去年當月）</td><td><?php echo $model->excel['c83'];?></td><td>-16% ~ -30% : 1
                    <br/> <0% ~ -15% : 2
                    <br/>  >=0% ~ 8% : 3
                    <br/>  9% ~ 14% : 4
                    <br/>  >= 15% : 5</td><td><?php echo $model->excel['e83'];?></td><td></td></tr>
            <tr><td rowspan="2">生意結構比例</td><td>餐飲非餐飲年生意額比例</td><td><?php echo $model->excel['c84'];?></td><td>20% ~ 39% : 1
                    <br/>  40% ~ 69% : 2
                    <br/>  70% ~ 99% : 4
                    <br/>  100% ~ 149% : 5
                    <br/> 150% ~ 229% : 3
                    <br/> >= 230% : 1</td><td><?php echo $model->excel['e84'];?></td><td></td></tr>
            <tr><td>IA, IB年生意額比例</td><td><?php echo $model->excel['c85'];?></td><td>5% ~ 19% : 1
                    <br/>  20% ~ 39% : 2
                    <br/>   40% ~ 59% : 4
                    <br/>  60% ~ 79% : 5
                    <br/> 80% ~ 99% : 3
                    <br/>>= 100% : 1</td><td><?php echo $model->excel['e85'];?></td><td></td></tr>
            <tr><td>停單情況</td><td>停單金額占生意比例% （當月停單總月金額/當月生意額）</td><td><?php echo $model->excel['c86'];?></td><td>0% ~ 0.8% : 5
                    <br/> 0.9% ~ 1.9% : 4
                    <br/>  2.0% ~ 2.8% : 3
                    <br/>  2.9% ~ 3.8% : 2
                    <br/>  >= 3.9% : 1</td><td><?php echo $model->excel['e86'];?></td><td></td></tr>
        </table>
        <style type="text/css">
            .tftable1 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #9dcc7a;border-collapse: collapse;}
            .tftable1 tr {background-color:#bedda7;}
            .tftable1 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #9dcc7a;}
        </style>
        <table class="tftable1" border="1">
            <tr><td style="width: 10%">外勤部</td><td colspan="4"></td><td><?php echo $model->excel['f87'];?></td></tr>
            <tr><td rowspan="3">技術員生產力</td><td>上月技術員平均生意額超出標準門欄比例 （標準：130000/月， 當地平均技術員生意額 - 標準生意額 / 標準生意額 ），主管/主任級別以下技術員</td><td style="width: 10%"><?php echo  $model->excel['c88'];?></td><td style="width: 10%">>=20% : 5
                    <br/>    0% ~ 19% : 4
                    <br/>   -9% ~ -1% : 3
                    <br/>   -19% ~ -10% : 2
                    <br/>   -29% ~ -20% : 1
                    <br/>    < -30% : 0</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e88'];?></td><td style="width: 12%"></td></tr>
            <tr><td>上月技術員最高生意額技術員金額跟標準比較  （標準：130000/月)</td><td><?php echo  $model->excel['c89'];?></td><td>>=70% : 5
                    <br/>  30% - 69% : 4
                    <br/>  10% - 29%：3</td><td><?php echo $model->excel['e89'];?></td><td></td></tr>
            <tr><td>上月技術員最低生意額技術員金額</td><td><?php echo  $model->excel['c90'];?></td><td>仅供参考，不计算分数</td><td><?php echo $model->excel['e90'];?></td><td></td></tr>
            <tr><td rowspan="2">技術員成本</td><td>技術員用料比例 清潔（技術員領貨金額/當月生意額）</td><td><?php echo  $model->excel['c91'];?></td><td><=10% : 5
                    <br/>    11% ~ 15% : 4
                    <br/>    16% ~ 20% : 3
                    <br/>    21% ~ 25% : 2
                    <br/>    26% ~ 30% : 1
                    <br/>    >31% : 0</td><td><?php echo $model->excel['e91'];?></td><td></td></tr>
            <tr><td>技術員用料比例 滅蟲（技術員領貨金額/當月生意額）</td><td><?php echo  $model->excel['c92'];?></td><td><=5% : 5
                    <br/>  6% ~ 10% : 4
                    <br/>  11% ~ 15% : 3
                    <br/> 16% ~ 20% : 2
                    <br/>  21% ~ 25% : 1
                    <br/>   >26% : 0</td><td><?php echo $model->excel['e92'];?></td><td></td></tr>
            <tr><td rowspan="2">獲獎情況</td><td>當月錦旗獲獎數目占整體技術員比例 （錦旗數目/整體技術員數目）</td><td><?php echo  $model->excel['c93'];?></td><td>>20% : 5
                    <br/>  10% ~ 19% : 3
                    <br/> 1% ~ 9% : 1
                    <br/> <=0% : 0</td><td><?php echo $model->excel['e93'];?></td><td></td></tr>
            <tr><td>當月襟章頒發明細 （P:N) P為受頒技術員數目，N為襟章發放數目</td><td><?php echo  $model->excel['c94'];?></td><td>仅供参考，不计算分数</td><td><?php echo $model->excel['e94'];?></td><td></td></tr>
        </table>
        <style type="text/css">
            .tftable2 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #a9a9a9;border-collapse: collapse;}
            .tftable2 tr {background-color:#b8b8b8;}
            .tftable2 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #a9a9a9;}
        </style>

        <table class="tftable2" border="1">
            <tr><td style="width: 10%">財務部</td><td colspan="4"></td><td><?php echo $model->excel['f95'];?></td></tr>
            <tr><td rowspan="2">財政狀況</td><td>毛利率 （當月生意額 - 材料訂購 - 技術員工資）/當月生意額</td><td style="width: 10%"><?php echo $model->excel['c96'];?></td><td style="width: 10%">>=55% : 5
                    <br/>  50% ~ 54% : 4
                    <br/> 45% ~ 49% : 3
                    <br/> 40% ~ 44% : 2
                    <br/>  36% ~ 39% : 1
                    <br/>  <=35% : 0</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e96'];?></td><td style="width: 12%"></td></tr>
            <tr><td>工資占生意額比例</td><td style="width: 10%"><?php echo $model->excel['c97'];?></td><td style="width: 10%">20% ~ 24% : 5
                    <br/>  25% ~ 34% : 4
                    <br/>  35% ~ 39% : 3
                    <br/>  40% ~ 49% : 2
                    <br/> >50% : 1</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e97'];?></td><td style="width: 12%"></td></tr>
           <?php if(!empty($model->excel['bc102'])){?>
            <tr><td rowspan="3">利潤狀況</td><td>纯利率</td><td style="width: 10%"><?php echo $model->excel['bc102'];?></td><td style="width: 10%"><4% : 1
                    <br/> 5% ~ 9% : 2
                    <br/> 10%~14% : 3
                    <br/> 15%~19% : 4
                    <br/>  >=20% : 5</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['be102'];?></td><td style="width: 12%"></td></tr>
            <tr><td>纯利跟上月横比增长</td><td style="width: 10%"><?php echo $model->excel['bc103'];?></td><td style="width: 10%">>=1% : 1
                    <br/> >=1.5% : 2
                    <br/> >=2% : 3
                    <br/>  >=2.5% : 4
                    <br/>  >=3% : 5</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['be103'];?></td><td style="width: 12%"></td></tr>
            <tr><td>纯利跟去年同比增长</td><td style="width: 10%"><?php echo $model->excel['bc104'];?></td><td style="width: 10%">0~7% : 1
                    <br/> 8%~16% : 2
                    <br/>  17%~25% : 3
                    <br/>  26%~34% : 4
                    <br/> >35% : 5</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['be104'];?></td><td style="width: 12%"></td></tr>
          <?php }?>
            <tr><td  rowspan="2">收款情況</td><td>收款效率（当月收款额/上月生意额） </td><td style="width: 10%"><?php echo $model->excel['c98'];?></td><td style="width: 10%">> =100% : 5
                    <br/> 95% ~ 99% : 4
                    <br/>  90% ~ 94% : 3
                    <br/>  85% ~ 89% : 2
                    <br/>  80% ~ 84% : 1</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e98'];?></td><td style="width: 12%"></td></tr>
            <tr><td>公司累積結餘（到每月最後一天止）</td><td style="width: 10%"><?php echo $model->excel['c99'];?></td><td style="width: 10%">仅供参考，不计算分数</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e99'];?></td><td style="width: 12%"></td></tr>
            <tr><td>應收未收帳情況</td><td>問題客人（超過90天沒有結款）比例
                    (問題客戶總月費金額/當月生意額）</td><td style="width: 10%"><?php echo $model->excel['c100'];?></td><td style="width: 10%"><= 30% : 5
                    <br/>  31% ~ 40% : 4
                    <br/>  41% ~ 50% :3
                    <br/>  51% ~ 60% : 2
                    <br/>  61% ~ 70% : 1</td style="width: 10%"><td style="width: 10%"><?php echo $model->excel['e100'];?></td><td style="width: 12%"></td></tr>
        </table>
        <style type="text/css">
            .tftable3 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #ebab3a;border-collapse: collapse;}
            .tftable3 tr {background-color:#f0c169;}
            .tftable3 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #ebab3a;}
        </style>

        <table class="tftable3" border="1">
            <tr><td style="width: 10%">營運部</td><td colspan="4"></td><td><?php echo $model->excel['f101'];?></td></tr>
            <tr><td>整體情況</td><td>新合同7天內安排首次比例 （成功7天首次客戶數目/整體當月新合同數目）</td><td style="width: 10%"><?php echo $model->excel['c102'];?></td><td style="width: 10%">95% ~ 100% ： 5
                    <br/>  90% ~ 94% ： 4
                    <br/>  85% ~ 89% ： 3
                    <br/>  80% ~ 84% ： 2
                    <br/>  75% ~ 79% ： 1
                    <br/>  <75% : 0</td><td style="width: 10%"><?php echo $model->excel['e102'];?></td><td style="width: 12%"></td></tr>
            <tr><td rowspan="3">物流情況</td><td>運送皂液準確度 （實際送皂液/應送皂液）</td><td><?php echo $model->excel['c103'];?></td><td>95% ~ 100% ： 5
                    <br/> 90% ~ 94% ： 4
                    <br/> 85% ~ 89% ： 3
                    <br/> 80% ~ 84% ： 2
                    <br/> 75% ~ 79% ： 1
                    <br/> <75% : 0</td><td><?php echo $model->excel['e103'];?></td><td></td></tr>
            <tr><td>運送銷售貨品準確度 （實際送銷售貨品/應送銷售貨品）</td><td><?php echo $model->excel['c104'];?></td><td>95% ~ 100% ： 5
                    <br/>  90% ~ 94% ： 4
                    <br/>   85% ~ 89% ： 3
                    <br/>   80% ~ 84% ： 2
                    <br/>  75% ~ 79% ： 1
                    <br/>  <75% : 0</td><td><?php echo $model->excel['e104'];?></td><td></td></tr>
            <tr><td>汽車支出平均 （C:M)
                    C : 車輛數目，M：車的平均用油量</td><td><?php echo $model->excel['c105'];?></td><td>仅供参考，不计算分数</td><td><?php echo $model->excel['e105'];?></td><td></td></tr>
            <tr><td rowspan="2">倉庫情況</td><td>每月盤點準確度</td><td><?php echo $model->excel['c106'];?></td><td>>=108% : 0
                    <br/>  104% ~ 107% ： 1
                    <br/>  101% ~103% ： 3
                    <br/>  96% ~ 100% ： 5
                    <br/>   92% ~ 95% ： 3
                    <br/>  88% ~ 91% ： 1</td><td><?php echo $model->excel['e106'];?></td><td></td></tr>
            <tr><td>新合同5天內安排安裝比例 （成功5天安裝客戶數目/整體當月新合同數目）</td><td><?php echo $model->excel['c107'];?></td><td>96% ~ 100% ： 5
                    <br/>  91% ~ 95% ： 4
                    <br/>  86% ~ 90% ： 3
                    <br/>  81% ~ 85% ： 2
                    <br/>  76% ~ 80% ： 1
                    <br/> <= 75% : 0</td><td><?php echo $model->excel['e107'];?></td><td></td></tr>
            <tr><td rowspan="3">質檢情況</td><td>當月質檢客戶數量效率 （跟標準每月客戶拜訪數目比較）
                    <br/> （估計客戶每個約4000/月，當地服務客戶金額/客戶金額估值=客戶約數量，客戶約數量除6（希望每12個月拜訪客戶一次），等於標準每月客戶拜訪數目）</td><td><?php echo $model->excel['c108'];?></td><td>>=90% : 5
                    <br/>  70% ~ 89% :４
                    <br/> 50% ~ 69% : 3
                    <br/> 30% ~ 49% : 2
                    <br/> 10% ~ 29% : 1
                    <br/> <9% : 0</td><td><?php echo $model->excel['e108'];?></td><td></td></tr>
            <tr><td>質檢問題客戶數量比例
                    <br/> （問題客戶 ： 質檢拜訪客戶分數低於70分。問題客戶/當月質檢拜訪客戶 = 質檢問題客戶數量比例）</td><td><?php echo $model->excel['c109'];?></td><td>>20% : 3
                    <br/> 10% ~ 19% : 5
                    <br/>  0% ~ 9% : 1</td><td><?php echo $model->excel['e109'];?></td><td></td></tr>
            <tr><td>表現滿意技術員 (質檢拜訪表平均分數最高同事）</td><td><?php echo $model->excel['c110'];?></td><td>仅供参考，不计算分数</td><td><?php echo $model->excel['e110'];?></td><td></td></tr>
            <tr><td rowspan="5">客訴處理</td><td>當月客訴數目比較（當月客訴數目 - 上月客訴數目 / 上月客訴數目）</td><td><?php echo $model->excel['c111'];?></td><td>< -15% : 5
                    <br/>  -10% ~ -14% : 4
                    <br/>  -5% ~ -9% : 3
                    <br/>  -1% ~ -4% : 2
                    <br/>  5% ~ 0% : 1
                    <br/>  >=6% : 0</td><td><?php echo $model->excel['e111'];?></td><td></td></tr>
            <tr><td>客訴解決效率（高效客訴數目 = 2工作天內處理完成客訴數量）
                    <br/>（客訴解決效率 = 高效客訴解決數目/當月客訴數目）</td><td><?php echo $model->excel['c112'];?></td><td>95% ~ 100% ： 5
                    <br/>  90% ~ 94% ： 4
                    <br/>  85% ~ 89% ： 3
                    <br/>  80% ~ 84% ： 2
                    <br/>  75% ~ 79% ： 1
                    <br/> <74% : 0</td><td><?php echo $model->excel['e112'];?></td><td></td></tr>
            <tr><td>主任跟投訴技術員面談比例
                    <br/>  （主任/組長面談客訴技術員數目/客訴數目）</td><td><?php echo $model->excel['c113'];?></td><td>8% ~10% ： 5
                    <br/>  6% ~ 7% ： 3
                    <br/>  4% ~ 5% ： 1
                    <br/>  <3% : 0</td><td><?php echo $model->excel['e113'];?></td><td></td></tr>
            <tr><td>高效回訪率 （高效回訪 = 客訴後7天內電話客戶回訪數目）
                    <br/> （高效回訪率=高效回訪/當月解決客訴數目）</td><td><?php echo $model->excel['c114'];?></td><td>95% ~ 100% ： 5
                    <br/> 90% ~ 94% ： 4
                    <br/>  85% ~ 89% ： 3
                    <br/> 80% ~ 84% ： 2
                    <br/>  75% ~ 79% ： 1
                    <br/> <74% : 0</td><td><?php echo $model->excel['e114'];?></td><td></td></tr>
            <tr><td>問題客戶需要主任組長跟進數目</td><td><?php echo $model->excel['c115'];?></td><td>仅供参考，不计算分数</td><td><?php echo $model->excel['e115'];?></td><td></td></tr>
        </table>

        <style type="text/css">
            .tftable4 {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #bcaf91;border-collapse: collapse;}
            .tftable4 tr {background-color:#e9dbbb;}
            .tftable4 td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #bcaf91;}
        </style>
        <table class="tftable4" border="1">
            <tr><td style="width: 10%">人事部</td><td colspan="4"></td><td><?php echo $model->excel['f116'];?></td></tr>
            <tr><td>整體情況</td><td>所有同事勞動合同進展 (超過一個月沒有簽署勞動合同同事數目（張））</td><td style="width: 10%"><?php echo $model->excel['c117'];?></td><td style="width: 10%">0 : 5
                    <br/>  1 ~ 3 : 4
                    <br/>   4 ~ 5 : 3
                    <br/>   >6 : 0</td><td style="width: 10%"><?php echo $model->excel['e117'];?></td><td style="width: 12%"></td></tr>
            <tr><td rowspan="2">銷售人員情況</td><td>銷售人員流失率 （工作滿一個月的）（離職銷售人員/當月所有銷售人員）</td><td><?php echo $model->excel['c118'];?></td><td>0% ~ 10% : 5
                    <br/> 11% ~ 20% : 3
                    <br/>  21% ~ 30% : 1
                    <br/> >31% : 0</td><td><?php echo $model->excel['e118'];?></td><td></td></tr>
            <tr><td>銷售區域空置率（公共區域/銷售劃分區域）</td><td><?php echo $model->excel['c119'];?></td><td>0% ~ 20%     :  5
                    <br/>   21% ~ 60%   :  3
                    <br/> 61%  ~ 100% :  1</td><td><?php echo $model->excel['e119'];?></td><td></td></tr>
            <tr><td rowspan="3">外勤人員情況</td><td>離職技術員（工作滿一個月的）人數% （當月離職技術人員/整體外勤技術人員）</td><td><?php echo $model->excel['c120'];?></td><td>0% ~ 5% : 5
                    <br/> 6% ~ 10% : 3
                    <br/> 11% ~ 15% : 1
                    <br/>>16% : 0</td><td><?php echo $model->excel['e120'];?></td><td></td></tr>
            <tr><td>組長數目跟標準比例 （最多每10個技術員，就要有一個組長的設置)
                    <br/> (技術員數目/10=標準組長數目， 比例 = 主管數目/標準主任長數目）</td><td><?php echo $model->excel['c121'];?></td><td>>=100% : 5
                    <br/>  81% ~ 99% : 3
                    <br/>  <= 80% : 1</td><td><?php echo $model->excel['e121'];?></td><td></td></tr>
            <tr><td>主任數目跟標準比例 （最多每20個技術員，就要有一個主任的設置)
                    <br/>  (技術員數目/20=標準組長數目， 比例 = 組長數目/標準數目）</td><td><?php echo $model->excel['c122'];?></td><td>>=100% : 5
                    <br/>  81% ~ 100% : 3
                    <br/>  <= 80% : 1</td><td><?php echo $model->excel['e122'];?></td><td></td></tr>
            <tr><td>辦公室人員情況</td><td>離職辦公室（工作滿一個月的）人數% （當月離職辦公室人員/整體辦公室人員）</td><td><?php echo $model->excel['c124'];?></td><td>0% ~ 10% : 5
                    <br/> 11% ~ 20% : 3
                    <br/>  21% ~ 30% : 1
                    <br/>  >31% : 0</td><td><?php echo $model->excel['e124'];?></td><td></td></tr>
        </table>
    </div>

</section>

<?php
$js = <<<EOF
$(document).ready(function(){
  $("#hide").click(function(){
 document.getElementById('p').style.display = 'block';
 document.getElementById('s').style.display = 'none';
  });
  $("#show").click(function(){
 document.getElementById('p').style.display = 'none';
 document.getElementById('s').style.display = 'block';
  });
});

EOF;
?>
<?php
Yii::app()->clientScript->registerScript('calculate',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


