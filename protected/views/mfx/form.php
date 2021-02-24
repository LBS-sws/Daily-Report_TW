<?php
$this->pageTitle=Yii::app()->name . ' - Month Report';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'monthly-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('monthly','月报表数据分析'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>
<div class="box"><div class="box-body">
        <div class="btn-group" role="group">

            <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                'submit'=>Yii::app()->createUrl('mfx/index')));
            ?>

        </div>
        <div class="btn-group pull-right" role="group">
            <?php echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('misc','xiazai'), array(
                'submit'=>Yii::app()->createUrl('mfx/downs')));
            ?>
        </div>
    </div></div>
<?php if(!empty($model->five)){ ?>
<section class="content" >
    <div style="width: 100%;">
        <div class="acc" style=" overflow-x:auto; overflow-y:auto;">
<!--	--><?php //$this->widget('ext.layout.ListPageWidget', array(
//			'title'=>Yii::t('monthly','Monthly Report Data List'),
//			'model'=>$model,
//				'viewhdr'=>'//month/_listhdr',
//				'viewdtl'=>'//month/_listdtl',
//				'gridsize'=>'24',
//				'height'=>'600',
//				'search'=>array(
//							'year_no',
//							'month_no',
//						),
//		));
//	?>
    <style type="text/css">
        .tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
        .tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
        .tftable tr {background-color:#d4e3e5;}
        .tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
        .tftable tr:hover {background-color:#ffffff;}
    </style>
    <input name="ReportH02Form[city]" value="<?php echo $model->scenario['city'];?>" style="display: none">
    <input name="ReportH02Form[start_dt]" value="<?php echo $model->scenario['start_dt'];?>" style="display: none">
    <input name="ReportH02Form[start_dt1]" value="<?php echo $model->scenario['start_dt1'];?>" style="display: none">
    <input name="ReportH02Form[end_dt]" value="<?php echo $model->scenario['end_dt'];?>" style="display: none">
    <input name="ReportH02Form[end_dt1]" value="<?php echo $model->scenario['end_dt1'];?>" style="display: none">
    <table class="tftable" border="1">
        <tr><th colspan="2">管理项目</th><?php $i=0; for($i=0;$i<$model->ccuser;$i++){?><th><?php echo $model->year[$i]."/".$model->month[$i]?></th><?php }?><th>定义</th></tr>
        <?php for ($a=0;$a<count($model->five[0]);$a++){?>
        <tr><td  style="width: 13%;"></td><td  style="width: 20%;"><?php echo $model->five[0][$a]['name'];?></td><?php $i=0; for($i=0;$i<$model->ccuser;$i++){?><td><?php echo $model->five[$i][$a]['data_value'];?></td><?php }?><td style="width: 15%;"></td></tr>
        <?php }?>

        <tr><td style="width: 13%;">总分(100分）：</td><td style="width: 20%;"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f74']."</td>";}?><td style="width: 15%;"></td></tr>
        <tr><td style="width: 10%">銷售部</td><td></td> <?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f75']."</td>";}?><td></td>  </tr>
        <tr><td rowspan="8">新生意情况</td><td>傳統服務(IA,IB)新生意年金額增長 （(當月-上月)/上月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c76']."(".$model->excel[$a]['e76'].")</td>";}?><td> -15% - -30%    :  1
                <br/><0% - -14%   :  2
                <br/>>=0% - 8%   :  3
                <br/>9% - 14%   :  4
                <br/>>= 15% :  5
                <br/></td> </tr>
        <tr><td>傳統服務(IA,IB)新生意年金額橫比增長 （(當月-去年當月）/去年當月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c77']."(".$model->excel[$a]['e77'].")</td>";}?><td> -15% - -30%    :  1
                <br/><0% - -14%   :  2
                <br/>>=0% - 8%   :  3
                <br/>9% - 14%   :  4
                <br/>> =15% :  5 </td> </tr>
        <tr><td>傳統服務(IA,IB)新生意合同數量增長（(當月-上月)/上月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c78']."(".$model->excel[$a]['e78'].")</td>";}?><td>-10% ~ -20% : 1
                <br/>4% ~ -9% : 2
                <br/>5% ~ 19% : 3
                <br/>20% ~ 29% : 4
                <br/>> =30% : 5</td> </tr>
        <tr><td>傳統服務(IA,IB)新生意合同數量橫比增長（(當月-去年當月)/去年當月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c79']."(".$model->excel[$a]['e79'].")</td>";}?><td>-10% ~ -20% : 1
                <br/>0% ~ -9% : 2
                <br/>10% ~ 19% : 3
                <br/>20% ~ 29% : 4
                <br/>> =30% : 5</td> </tr>
        <tr><td>新興業務(空氣淨化機, 飄盈香，甲醛，廚房或其他)新生意年金額增長 （(當月-上月)/上月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c80']."(".$model->excel[$a]['e80'].")</td>";}?><td>-100% ~ -200% : 1
                <br/><0% ~ -99% : 2
                <br/>>=0% ~ 99% : 3
                <br/> 100% ~ 149% : 4
                <br/>>= 150% : 5</td> </tr>
        <tr><td>新興業務(空氣淨化機,飄盈香，甲醛，廚房或其他)新生意年金額橫比增長 （(當月-去年當月)/去年當月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c81']."(".$model->excel[$a]['e81'].")</td>";}?><td>-100% ~ -200% : 1
                <br/> <0% ~ -99% : 2
                <br/>  >=0% ~ 99% : 3
                <br/> 100% ~ 149% : 4
                <br/> >= 150% : 5</td> </tr>
        <tr><td>公司整體生意年金額淨增長比例（（當月-上月）/上月）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c82']."(".$model->excel[$a]['e82'].")</td>";}?><td>-10% ~ -20% : 1
                <br/> <0% ~ -9% : 2
                <br/>  >=0% ~ 8% : 3
                <br/> 9% ~ 14% : 4
                <br/> >= 15% : 5</td> </tr>
        <tr><td>公司整體生意淨增長年金額橫比比例（（當月-去年當月）/去年當月）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c83']."(".$model->excel[$a]['e83'].")</td>";}?><td>-16% ~ -30% : 1
                <br/> <0% ~ -15% : 2
                <br/>  >=0% ~ 8% : 3
                <br/>  9% ~ 14% : 4
                <br/>  >= 15% : 5</td> </tr>
        <tr><td rowspan="2">生意結構比例</td><td>餐飲非餐飲年生意額比例</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c84']."(".$model->excel[$a]['e84'].")</td>";}?><td>20% ~ 39% : 1
                <br/>  40% ~ 69% : 2
                <br/>  70% ~ 99% : 4
                <br/>  100% ~ 149% : 5
                <br/> 150% ~ 229% : 3
                <br/> >= 230% : 1</td> </tr>
        <tr><td>IA, IB年生意額比例</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c85']."(".$model->excel[$a]['e85'].")</td>";}?><td>5% ~ 19% : 1
                <br/>  20% ~ 39% : 2
                <br/>   40% ~ 59% : 4
                <br/>  60% ~ 79% : 5
                <br/> 80% ~ 99% : 3
                <br/>>= 100% : 1</td> </tr>
        <tr><td>停單情況</td><td>停單金額占生意比例% （當月停單總月金額/當月生意額）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c86']."(".$model->excel[$a]['e86'].")</td>";}?><td>0% ~ 0.8% : 5
                <br/> 0.9% ~ 1.9% : 4
                <br/>  2.0% ~ 2.8% : 3
                <br/>  2.9% ~ 3.8% : 2
                <br/>  >= 3.9% : 1</td> </tr>


        <tr><td style="width: 13%;">外勤部</td><td style="width: 20%;"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f87']."</td>";}?><td style="width: 15%;"></td></tr>
        <tr><td rowspan="3">技術員生產力</td><td>上月技術員平均生意額超出標準門欄比例 （標準：130000/月， 當地平均技術員生意額 - 標準生意額 / 標準生意額 ），主管/主任級別以下技術員</td><<?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c88']."(".$model->excel[$a]['e88'].")</td>";}?><td style="width: 10%">>=20% : 5
                <br/>    0% ~ 19% : 4
                <br/>   -9% ~ -1% : 3
                <br/>   -19% ~ -10% : 2
                <br/>   -29% ~ -20% : 1
                <br/>    < -30% : 0</td style="width: 10%"></tr>
        <tr><td>上月技術員最高生意額技術員金額跟標準比較  （標準：130000/月)</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c89']."(".$model->excel[$a]['e89'].")</td>";}?><td>>=70% : 5
                <br/>  30% - 69% : 4
                <br/>  10% - 29%：3</td>td></td></tr>
        <tr><td>上月技術員最低生意額技術員金額</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c90']."(".$model->excel[$a]['e90'].")</td>";}?><td>仅供参考，不计算分数</td> </tr>
        <tr><td rowspan="2">技術員成本</td><td>技術員用料比例 清潔（技術員領貨金額/當月生意額）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c91']."(".$model->excel[$a]['e91'].")</td>";}?><td><=10% : 5
                <br/>    11% ~ 15% : 4
                <br/>    16% ~ 20% : 3
                <br/>    21% ~ 25% : 2
                <br/>    26% ~ 30% : 1
                <br/>    >31% : 0</td> </tr>
        <tr><td>技術員用料比例 滅蟲（技術員領貨金額/當月生意額）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c92']."(".$model->excel[$a]['e92'].")</td>";}?><td><=5% : 5
                <br/>  6% ~ 10% : 4
                <br/>  11% ~ 15% : 3
                <br/> 16% ~ 20% : 2
                <br/>  21% ~ 25% : 1
                <br/>   >26% : 0</td> </tr>
        <tr><td rowspan="2">獲獎情況</td><td>當月錦旗獲獎數目占整體技術員比例 （錦旗數目/整體技術員數目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c93']."(".$model->excel[$a]['e93'].")</td>";}?><td>>20% : 5
                <br/>  10% ~ 19% : 3
                <br/> 1% ~ 9% : 1
                <br/> <=0% : 0</td> </tr>
        <tr><td>當月襟章頒發明細 （P:N) P為受頒技術員數目，N為襟章發放數目</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c94']."(".$model->excel[$a]['e94'].")</td>";}?><td>仅供参考，不计算分数</td> </tr>


        <tr><td style="width: 13%">財務部</td><td style="width: 20%"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f95']."</td>";}?><td style="width: 15%"> </td></tr>
        <tr><td rowspan="2">財政狀況</td><td>毛利率 （當月生意額 - 材料訂購 - 技術員工資）/當月生意額</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c96']."(".$model->excel[$a]['e96'].")</td>";}?><td style="width: 10%">>=55% : 5
                <br/>  50% ~ 54% : 4
                <br/> 45% ~ 49% : 3
                <br/> 40% ~ 44% : 2
                <br/>  36% ~ 39% : 1
                <br/>  <=35% : 0</td style="width: 10%"></tr>
        <tr><td>工資占生意額比例</td><<?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c97']."(".$model->excel[$a]['e97'].")</td>";}?><td style="width: 10%">20% ~ 24% : 5
                <br/>  25% ~ 34% : 4
                <br/>  35% ~ 39% : 3
                <br/>  40% ~ 49% : 2
                <br/> >50% : 1</td style="width: 10%"></tr>
        <?php if(!empty($model->excel['bc102'])){?>
            <tr><td rowspan="3">利潤狀況</td><td>纯利率</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c102']."(".$model->excel[$a]['e102'].")</td>";}?><td style="width: 10%"><4% : 1
                    <br/> 5% ~ 9% : 2
                    <br/> 10%~14% : 3
                    <br/> 15%~19% : 4
                    <br/>  >=20% : 5</td style="width: 10%"></tr>
            <tr><td>纯利跟上月横比增长</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c103']."(".$model->excel[$a]['e103'].")</td>";}?><td style="width: 10%">>=1% : 1
                    <br/> >=1.5% : 2
                    <br/> >=2% : 3
                    <br/>  >=2.5% : 4
                    <br/>  >=3% : 5</td style="width: 10%"></tr>
            <tr><td>纯利跟去年同比增长</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c104']."(".$model->excel[$a]['e104'].")</td>";}?><td style="width: 10%">0~7% : 1
                    <br/> 8%~16% : 2
                    <br/>  17%~25% : 3
                    <br/>  26%~34% : 4
                    <br/> >35% : 5</td style="width: 10%"></tr>
        <?php }?>
        <tr><td  rowspan="2">收款情況</td><td>收款效率（当月收款额/上月生意额） </td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c98']."(".$model->excel[$a]['e98'].")</td>";}?><td style="width: 10%">> =100% : 5
                <br/> 95% ~ 99% : 4
                <br/>  90% ~ 94% : 3
                <br/>  85% ~ 89% : 2
                <br/>  80% ~ 84% : 1</td style="width: 10%"></tr>
        <tr><td>公司累積結餘（到每月最後一天止）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c99']."(".$model->excel[$a]['e99'].")</td>";}?><td style="width: 10%">仅供参考，不计算分数</td style="width: 10%"></tr>
        <tr><td>應收未收帳情況</td><td>問題客人（超過90天沒有結款）比例
                (問題客戶總月費金額/當月生意額）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c100']."(".$model->excel[$a]['e100'].")</td>";}?><td style="width: 10%"><= 30% : 5
                <br/>  31% ~ 40% : 4
                <br/>  41% ~ 50% :3
                <br/>  51% ~ 60% : 2
                <br/>  61% ~ 70% : 1</td style="width: 10%"></tr>


        <tr><td style="width: 13%">营运部</td><td style="width: 20%"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f101']."</td>";}?><td style="width: 15%"></td></tr>
        <tr><td>整體情況</td><td>新合同7天內安排首次比例 （成功7天首次客戶數目/整體當月新合同數目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c102']."(".$model->excel[$a]['e102'].")</td>";}?><td style="width: 10%">95% ~ 100% ： 5
                <br/>  90% ~ 94% ： 4
                <br/>  85% ~ 89% ： 3
                <br/>  80% ~ 84% ： 2
                <br/>  75% ~ 79% ： 1
                <br/>  <75% : 0</td> </tr>
        <tr><td rowspan="3">物流情況</td><td>運送皂液準確度 （實際送皂液/應送皂液）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c103']."(".$model->excel[$a]['e103'].")</td>";}?><td>95% ~ 100% ： 5
                <br/> 90% ~ 94% ： 4
                <br/> 85% ~ 89% ： 3
                <br/> 80% ~ 84% ： 2
                <br/> 75% ~ 79% ： 1
                <br/> <75% : 0</td> </tr>
        <tr><td>運送銷售貨品準確度 （實際送銷售貨品/應送銷售貨品）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c104']."(".$model->excel[$a]['e104'].")</td>";}?><td>95% ~ 100% ： 5
                <br/>  90% ~ 94% ： 4
                <br/>   85% ~ 89% ： 3
                <br/>   80% ~ 84% ： 2
                <br/>  75% ~ 79% ： 1
                <br/>  <75% : 0</td> </tr>
        <tr><td>汽車支出平均 （C:M)
                C : 車輛數目，M：車的平均用油量</td><td>仅供参考，不计算分数</td> <td></td></tr>
        <tr><td rowspan="2">倉庫情況</td><td>每月盤點準確度</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c106']."(".$model->excel[$a]['e106'].")</td>";}?><td>>=108% : 0
                <br/>  104% ~ 107% ： 1
                <br/>  101% ~103% ： 3
                <br/>  96% ~ 100% ： 5
                <br/>   92% ~ 95% ： 3
                <br/>  88% ~ 91% ： 1</td> </tr>
        <tr><td>新合同5天內安排安裝比例 （成功5天安裝客戶數目/整體當月新合同數目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c107']."(".$model->excel[$a]['e107'].")</td>";}?><td>96% ~ 100% ： 5
                <br/>  91% ~ 95% ： 4
                <br/>  86% ~ 90% ： 3
                <br/>  81% ~ 85% ： 2
                <br/>  76% ~ 80% ： 1
                <br/> <= 75% : 0</td> </tr>
        <tr><td rowspan="3">質檢情況</td><td>當月質檢客戶數量效率 （跟標準每月客戶拜訪數目比較）
                <br/> （估計客戶每個約4000/月，當地服務客戶金額/客戶金額估值=客戶約數量，客戶約數量除6（希望每12個月拜訪客戶一次），等於標準每月客戶拜訪數目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c108']."(".$model->excel[$a]['e108'].")</td>";}?><td>>=90% : 5
                <br/>  70% ~ 89% :４
                <br/> 50% ~ 69% : 3
                <br/> 30% ~ 49% : 2
                <br/> 10% ~ 29% : 1
                <br/> <9% : 0</td> </tr>
        <tr><td>質檢問題客戶數量比例
                <br/> （問題客戶 ： 質檢拜訪客戶分數低於70分。問題客戶/當月質檢拜訪客戶 = 質檢問題客戶數量比例）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c109']."(".$model->excel[$a]['e109'].")</td>";}?><td>>20% : 3
                <br/> 10% ~ 19% : 5
                <br/>  0% ~ 9% : 1</td> </tr>
        <tr><td>表現滿意技術員 (質檢拜訪表平均分數最高同事）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c110']."(".$model->excel[$a]['e110'].")</td>";}?><td>仅供参考，不计算分数</td> </tr>
        <tr><td rowspan="5">客訴處理</td><td>當月客訴數目比較（當月客訴數目 - 上月客訴數目 / 上月客訴數目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c111']."(".$model->excel[$a]['e111'].")</td>";}?><td>< -15% : 5
                <br/>  -10% ~ -14% : 4
                <br/>  -5% ~ -9% : 3
                <br/>  -1% ~ -4% : 2
                <br/>  5% ~ 0% : 1
                <br/>  >=6% : 0</td> </tr>
        <tr><td>客訴解決效率（高效客訴數目 = 2工作天內處理完成客訴數量）
                <br/>（客訴解決效率 = 高效客訴解決數目/當月客訴數目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c112']."(".$model->excel[$a]['e112'].")</td>";}?><td>95% ~ 100% ： 5
                <br/>  90% ~ 94% ： 4
                <br/>  85% ~ 89% ： 3
                <br/>  80% ~ 84% ： 2
                <br/>  75% ~ 79% ： 1
                <br/> <74% : 0</td> </tr>
        <tr><td>主任跟投訴技術員面談比例
                <br/>  （主任/組長面談客訴技術員數目/客訴數目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c113']."(".$model->excel[$a]['e113'].")</td>";}?><td>8% ~10% ： 5
                <br/>  6% ~ 7% ： 3
                <br/>  4% ~ 5% ： 1
                <br/>  <3% : 0</td> </tr>
        <tr><td>高效回訪率 （高效回訪 = 客訴後7天內電話客戶回訪數目）
                <br/> （高效回訪率=高效回訪/當月解決客訴數目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c114']."(".$model->excel[$a]['e114'].")</td>";}?><td>95% ~ 100% ： 5
                <br/> 90% ~ 94% ： 4
                <br/>  85% ~ 89% ： 3
                <br/> 80% ~ 84% ： 2
                <br/>  75% ~ 79% ： 1
                <br/> <74% : 0</td> </tr>
        <tr><td>問題客戶需要主任組長跟進數目</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c115']."(".$model->excel[$a]['e115'].")</td>";}?><td>仅供参考，不计算分数</td> </tr>
        <tr><td style="width: 13%">人事部</td><td  style="width: 20%"></td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['f116']."</td>";}?><td style="width: 15%"></td></tr>
        <tr><td>整體情況</td><td>所有同事勞動合同進展 (超過一個月沒有簽署勞動合同同事數目（張））</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c117']."(".$model->excel[$a]['e117'].")</td>";}?><td style="width: 10%">0 : 5
                <br/>  1 ~ 3 : 4
                <br/>   4 ~ 5 : 3
                <br/>   >6 : 0</td></tr>
        <tr><td rowspan="2">銷售人員情況</td><td>銷售人員流失率 （工作滿一個月的）（離職銷售人員/當月所有銷售人員）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c118']."(".$model->excel[$a]['e118'].")</td>";}?><td>0% ~ 10% : 5
                <br/> 11% ~ 20% : 3
                <br/>  21% ~ 30% : 1
                <br/> >31% : 0</td> </tr>
        <tr><td>銷售區域空置率（公共區域/銷售劃分區域）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c119']."(".$model->excel[$a]['e119'].")</td>";}?><td>0% ~ 20%     :  5
                <br/>   21% ~ 60%   :  3
                <br/> 61%  ~ 100% :  1</td> </tr>
        <tr><td rowspan="3">外勤人員情況</td><td>離職技術員（工作滿一個月的）人數% （當月離職技術人員/整體外勤技術人員）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c120']."(".$model->excel[$a]['e120'].")</td>";}?><td>0% ~ 5% : 5
                <br/> 6% ~ 10% : 3
                <br/> 11% ~ 15% : 1
                <br/>>16% : 0</td> </tr>
        <tr><td>組長數目跟標準比例 （最多每10個技術員，就要有一個組長的設置)
                <br/> (技術員數目/10=標準組長數目， 比例 = 主管數目/標準主任長數目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c121']."(".$model->excel[$a]['e121'].")</td>";}?><td>>=100% : 5
                <br/>  81% ~ 99% : 3
                <br/>  <= 80% : 1</td> </tr>
        <tr><td>主任數目跟標準比例 （最多每20個技術員，就要有一個主任的設置)
                <br/>  (技術員數目/20=標準組長數目， 比例 = 組長數目/標準數目）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c122']."(".$model->excel[$a]['e122'].")</td>";}?><td>>=100% : 5
                <br/>  81% ~ 100% : 3
                <br/>  <= 80% : 1</td> </tr>
        <tr><td>辦公室人員情況</td><td>離職辦公室（工作滿一個月的）人數% （當月離職辦公室人員/整體辦公室人員）</td><?php for ($a=0;$a<count($model->excel);$a++){ echo "<td>".$model->excel[$a]['c124']."(".$model->excel[$a]['e124'].")</td>";}?><td>0% ~ 10% : 5
                <br/> 11% ~ 20% : 3
                <br/>  21% ~ 30% : 1
                <br/>  >31% : 0</td> </tr>

    </table>
    </div>
    </div>
</section>
<?php } else{echo "<br/><h1>暂无数据</h1>";}?>
<?php
//	echo $form->hiddenField($model,'pageNum');
//	echo $form->hiddenField($model,'totalRow');
//	echo $form->hiddenField($model,'orderField');
//	echo $form->hiddenField($model,'orderType');
//?>
<?php $this->endWidget(); ?>

<?php
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

