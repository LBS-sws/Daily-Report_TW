<?php

class QcController extends Controller 
{
	public $function_id='A06';

	public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
/*		
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','new','edit','delete','save'),
				'users'=>array('@'),
			),
*/
			array('allow', 
				'actions'=>array('new','edit','delete','save','down','downs','remove',"templates",'fileupload','fileremove','filedownload'),
				'expression'=>array('QcController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','filedownload'),
				'expression'=>array('QcController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new QcList;
		if (isset($_POST['QcList'])) {
			$model->attributes = $_POST['QcList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_a06']) && !empty($session['criteria_a06'])) {
				$criteria = $session['criteria_a06'];
				$model->setCriteria($criteria);
			}
		}

		$model->determinePageNum($pageNum);

		$model->retrieveDataByPage($model->pageNum);

		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{

		if (isset($_POST['QcForm'])) {
			$model = new QcForm($_POST['QcForm']['scenario']);
			$model->attributes = $_POST['QcForm'];
			if ($model->validate()) {
				$model->saveData();
//				$model->scenario = 'edit';

				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('qc/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				switch ($model->service_type) {
					case 'IA': $formfile = $model->new_form ? 'formia' : 'form'; break;
					case 'IB' : $formfile = $model->new_form ? 'formib' : 'form'; break;
					default : $formfile = 'form';
				}
				$this->render($formfile,array('model'=>$model,));
			}
		}
	}

	public function actionDownss()
    {
        $model = new QcForm($_POST['QcForm']['scenario']);
        $model->attributes = $_POST['QcForm'];
        Yii::$enableIncludePath = false;
        Yii::import('application.extensions.PHPExcel.PHPExcel', 1);
        $objPHPExcel = new PHPExcel;
        $objReader  = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load("templates/source/miechong.xls");
//        echo $model['info']['qc_score'];
//        print_r('<pre/>');
//        print_r($model);
        $objPHPExcel->getActiveSheet()->setCellValue('C4', $model['company_name'])
            ->setCellValue('K4', $model['job_staff'])
            ->setCellValue('C6', $model['info']['service_dt'])
            ->setCellValue('K6', $model['entry_dt'])
            ->setCellValue('C9', $model['env_grade'])
            ->setCellValue('f11', $model['info']['qc_score'])
            ->setCellValue('f33', $model['service_score'])
            ->setCellValue('L44', $model['qc_result'])
            ->setCellValue('D46', $model['cust_comment'])
            ->setCellValue('D50', $model['remarks']);
        $objPHPExcel->getActiveSheet()->mergeCells('C4:D4')
            ->mergeCells('K4:L4')
            ->mergeCells('C6:D6')
            ->mergeCells('K6:L6')
            ->mergeCells('C9:D9')
            ->mergeCells('D46:F46')
            ->mergeCells('D50:F50');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $time=time();
//        $str= mb_convert_encoding("out/bill_".$model['party'].'_'.$model['game'].'_'.$model['month'].".xls","gb2312","UTF-8");
        $str="templates/bill_".$time.".xls";
        $objWriter->save($str);
//        //让访问浏览器直接下载文件流
//        $url=$_SERVER['HTTP_HOST']."/dr/templates/bill_".$time.".xls";
//        Header('location:http://'.$url);

        $filetype = array(".docx",".doc",".xlsx",".xls",".pptx",".ppt",".jpg",".png",".pdf"); //文件类型
       // $tempFile = "templates/bill_".$time;                 //$log["attachment"]为文件地址;
        $url = str_replace($filetype,"",$str).".pdf";             //替换文件后缀
//header('Location: '.$url);
//die();
        $tempFile = "templates/new_".basename($str);             //临时文件地址
        copy($str,$tempFile);             //移动文件
        exec("unoconv -f pdf ".$tempFile);                           //文件转pdf
        $pdf = str_replace($filetype,"",$tempFile).".pdf";
//        header("Content-type:application/pdf");
// 文件将被称为 downloaded.pdf
//header("Content-Disposition:attachment;filename=downloaded.pdf");
// PDF 源在 original.pdf 中
       readfile($pdf);
//$url = "/uploads/".$log["attachment"];
        Header('location:http://'.$pdf);
    }
//IB
    public function actionDown()
    {
        $model = new QcForm($_POST['QcForm']['scenario']);
        $model->attributes = $_POST['QcForm'];
//        print_r('<pre>');
//        print_r($model);
//        require_once('protected/extensions/tcpdf/tcpdf.php');
//        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf = new MyPDF2('P', 'mm', 'A4', true, 'UTF-8', false);
        // = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF8', false)
//        $pdf->SetCreator(PDF_CREATOR);
//        $pdf->SetAuthor('yueguangguang');
//        $pdf->SetSubject('TCPDF Tutorial');
//        $pdf->SetKeywords('TCPDF, PDF, PHP');
        $pdf->SetTitle($model['company_name']);
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetFont('stsongstdlight', '', 10);
        $t_margin= $pdf->getHeaderHeight()+7;
        $r_margin=5;
        $l_margin=5;
        $pdf->SetMargins($l_margin, $t_margin, $r_margin);
        $h_margin=15;
        $pdf->SetHeaderMargin($h_margin);
        $f_margin=5;
        $pdf->SetFooterMargin($f_margin);
        // set auto page breaks
        $b_margin=5;
        $pdf->SetAutoPageBreak(TRUE, $b_margin);
        // add a page
        $pdf->AddPage();
        $arr=array();
        $arr['service_dt']=$model->info['service_dt'];
        $arr['qc_score']=$model->info['qc_score'];
        $arr['score_ratcheck']=$model->info['score_ratcheck'];
        $arr['score_ratdispose']=$model->info['score_ratdispose'];
        $arr['score_ratboard']=$model->info['score_ratboard'];
        $arr['score_rathole']=$model->info['score_rathole'];
        $arr['score_ratwarn']=$model->info['score_ratwarn'];
        $arr['score_ratdrug']=$model->info['score_ratdrug'];
        $arr['score_roachcheck']=$model->info['score_roachcheck'];
        $arr['score_roachdrug']=$model->info['score_roachdrug'];
        $arr['score_roachexdrug']=$model->info['score_roachexdrug'];
        $arr['score_roachtoxin']=$model->info['score_roachtoxin'];
        $arr['score_flycup']=$model->info['score_flycup'];
        $arr['score_flylamp']=$model->info['score_flylamp'];
        $arr['score_flycntl']=$model->info['score_flycntl'];
        $arr['score_flyspray']=$model->info['score_flyspray'];
        $arr['score_uniform']=$model->info['score_uniform'];
        $arr['score_tools']=$model->info['score_tools'];
        $arr['score_greet']=$model->info['score_greet'];
        $arr['score_comm']=$model->info['score_comm'];
        $arr['score_safety']=$model->info['score_safety'];
        $arr['score_afterwork']=$model->info['score_afterwork'];
        $arr['sign_cust']=$model->info['sign_cust'];
        $arr['sign_tech']=$model->info['sign_tech'];
        $arr['sign_qc']=$model->info['sign_qc'];
        $arr = (object)$arr;
        $image=array();
        $image['sign_cust']=TbHtml::image($model->info['sign_cust'],'QcForm_info_sign_cust_img',array('id'=>'QcForm_info_sign_cust_img','width'=>150,'height'=>30,));
        $image['sign_tech']=TbHtml::image($model->info['sign_tech'],'QcForm_info_sign_cust_img',array('id'=>'QcForm_info_sign_cust_img','width'=>150,'height'=>30,));
        $image['sign_qc']=TbHtml::image($model->info['sign_qc'],'QcForm_info_sign_cust_img',array('id'=>'QcForm_info_sign_cust_img','width'=>150,'height'=>30,));
        $image = (object)$image;
//        print_r(TbHtml::image($model->info['sign_cust'],'QcForm_info_sign_cust_img',array('id'=>'QcForm_info_sign_cust_img','width'=>200,'height'=>100,)));
//     print_r('<pre>');
//        print_r($arr);
        $tbl=<<<EOD
        
      <!--<img width=92 height=60 src="images/image000.gng"/>-->
     
        <div style="text-align: center;font-size: 17px;margin: auto;line-height: 50px;" ><img width="90" height="60" src="images/image000.png" >史伟莎灭虫服务质检报告</div>
        <div style="margin-left: 20px;  ">
<table align="left" border="0" cellpadding="1" cellspacing="1" width="600" >
  
         <tr>
             <td width="100px">客户名称 :</td>
             <td width="200px"> $model->company_name</td>
             <td width="100px">
                 外勤名称 :
             </td>
             <td> $model->job_staff</td>
         </tr>
        <tr>
            <td>服务日期 :</td>
            <td>$arr->service_dt</td>
            <td>
                品检日期 :	
            </td>
            <td>$model->entry_dt</td>
        </tr>
        <tr>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td width="150px"><b>质检评分（占总分60%）</b></td>
            <td colspan="3">分数：$arr->qc_score</td>
        </tr>
         <tr>
            <td colspan="4"></td>
        </tr>
      
        <tr>
            <td><b>1、鼠防治(满分100分)</b></td>
          
            <td colspan="2">	</td>
            <td><b>得分</b></td>
        </tr>
        <tr>
            <td>现场检查</td>
            <td width="238px">现场的检查按照程序和顺序,无遗漏</td>
            <td width="68px">	25分</td>
            <td >$arr->score_ratcheck</td>
        </tr>
        <tr>
            <td>死鼠清理</td>
            <td width="238px">死鼠是否及时发现并清理</td>
            <td width="68px">	15分</td>
            <td >$arr->score_ratdispose</td>
        </tr>
        <tr>
            <td>粘板布放</td>
            <td width="238px">粘鼠板布放是否合理到位，及时更换失效粘板</td>
            <td width="68px">15分</td>
            <td >	$arr->score_ratboard</td>
        </tr>
        <tr>
            <td>鼠洞封堵</td>
            <td width="238px">对能够封堵的鼠洞进行全面封堵，无遗漏 </td>
            <td width="68px">	15分</td>
            <td >	$arr->score_rathole</td>
        </tr>
        <tr>
            <td>警示标签</td>
            <td width="238px">老鼠控制的场所是否贴有警示标签 </td>
            <td width="68px">	15分</td>
            <td >	$arr->score_ratwarn</td>
        </tr>
        <tr>
            <td>鼠药投放</td>
            <td width="238px">鼠药的摆放是否全面，及时添加</td>
            <td width="68px">	15分</td>
            <td >	$arr->score_ratdrug</td>
        </tr>
        <tr>
            <td><b>2、蟑螂防治（满分100分）</b></td>           
            <td colspan="2" width="300px">	</td>
            <td><b>得分</b></td>
        </tr>
        <tr>
            <td>现场检查</td>
            <td width="238px">现场的检查按照程序和顺序,无遗漏  </td>
            <td width="68px">	25分</td>
            <td >	$arr->score_roachcheck</td>
        </tr>
        <tr>
            <td>胶饵点施</td>
            <td width="238px">胶饵的点施全面、合理 </td>
            <td width="68px">	25分</td>
            <td >	$arr->score_roachdrug</td>
        </tr>
        <tr>
            <td>失效胶饵的处理</td>
            <td width="238px">更换陈旧、失效的胶饵 </td>
            <td width="68px">	25分</td>
            <td >	$arr->score_roachexdrug</td>
        </tr>
        <tr>
            <td>撒施毒饵</td>
            <td width="238px">毒饵的撒施合理、全面 </td>
            <td width="68px">	25分</td>
            <td >	$arr->score_roachtoxin</td>
        </tr>
        <tr>
            <td><b>3、飞虫防治（满分100分）</b></td>           
            <td colspan="2" width="300px">	 </td>
            <td><b>得分</b></td>
        </tr>
        <tr>
            <td>蚊滋杯</td>
            <td width="238px">蚊滋杯的制作是否规范,是否贴警示标签,并定期更换 </td>
            <td width="68px">	25分</td>
            <td >	$arr->score_flycup</td>
        </tr>
        <tr>
            <td>灭蚊蝇灯</td>
            <td width="238px">是否及时更换粘纸，粘蝇纸布放合理 </td>
            <td width="68px">	25分</td>
            <td >	$arr->score_flylamp</td>
        </tr>
        <tr>
            <td>滋生地处理</td>
            <td width="238px">对滋生地的控制是否合理 </td>
            <td width="68px">	25分</td>
            <td >	$arr->score_flycntl</td>
        </tr>
         <tr>
            <td>化学剂喷洒</td>
            <td width="238px">做滞留喷洒时是否做到标准流程 </td>
            <td width="68px">	25分</td>
            <td >	$arr->score_flyspray</td>
        </tr>
           <tr>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td><b>服务评分（占总分40%）</b></td>
            <td colspan="3">分数：$model->service_score</td>
        </tr>
           <tr>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td ><b>1.个人形象</b></td>
            <td colspan="2" width="300px"></td>
            <td><b>得分</b></td>
        </tr>
        <tr>
            <td>着装规范</td>
            <td width="238px">仪容整洁合理（穿着整洁的工作服）</td>
            <td width="68px">10分</td>
            <td>$arr->score_uniform</td>
        </tr>
        <tr>
            <td>装备齐全</td>
            <td width="238px">携带的工具，药物等齐全</td>
            <td width="68px">10分</td>
            <td>$arr->score_tools</td>
        </tr>
        <tr>
            <td ><b>2、沟通技巧</b></td>
            <td colspan="2" width="300px"></td>
            <td><b>得分</b></td>
        </tr>
        <tr>
            <td>进门打招呼</td>
            <td width="238px">技术员到达现场后需要到现场负责人处报到</td>
            <td width="68px">20分</td>
            <td>$arr->score_greet</td>
        </tr>
        <tr>
            <td>现场沟通</td>
            <td width="238px">技术员在服务时和现场人员的询问与沟通</td>
            <td width="68px">20分</td>
            <td>$arr->score_comm</td>
        </tr>
        <tr>
            <td ><b>3、安全评分</b></td>
            <td colspan="2" width="300px">总分10分（违反此项全部扣除）</td>
            <td><b>得分</b></td>
        </tr>
        <tr>
            <td>安全</td>
            <td width="238px">药物投放是否安全（使用鼠饵盒药物不可接触食物）</td>
            <td width="68px">10分</td>
            <td>$arr->score_safety</td>
        </tr>
        <tr>
            <td>工作后效果</td>
            <td width="238px"></td>
            <td width="68px">30分</td>
            <td>$arr->score_afterwork</td>
        </tr>
                <tr>          
          <td colspan="4">
        </td>
        </tr>
        <tr>          
            <td style="font-size: 15px;"><b>总分 : </b>$model->qc_result</td>
           <td colspan="3">
           </td>
        </tr>
        <tr>          
          <td colspan="4">
        </td>
        </tr>
        <tr>
            <td  height="25px" width="75px" style="font-size: 12px"><b>客户意见：</b></td>
            <td>$model->cust_comment</td>
            <td  style="font-size: 12px" width="90px"><b>客户签名:</b></td>
             <td>$image->sign_cust</td>
        </tr>
        <tr>
            <td  height="25px" width="75px" style="font-size: 12px"><b>质检员意见：</b> </td>
           <td>$model->remarks</td>
            <td style="font-size: 12px"><b>质检员签名:</b> </td>
            <td>$image->sign_qc</td>
        </tr>
    
    </table>
    </div>
EOD;
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $outstring =$pdf->Output('IB.pdf', 'D');
        return $outstring;
    }
//IA
    public function actionDowns()
    {
        $model = new QcForm($_POST['QcForm']['scenario']);
        $model->attributes = $_POST['QcForm'];
//        print_r('<pre>');
//        print_r($model);
        $pdf = new MyPDF2('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle($model['company_name']);
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetFont('stsongstdlight', '', 10);
        $t_margin= $pdf->getHeaderHeight()+7;
        $r_margin=5;
        $l_margin=5;
        $pdf->SetMargins($l_margin, $t_margin, $r_margin);
        $h_margin=15;
        $pdf->SetHeaderMargin($h_margin);
        $f_margin=5;
        $pdf->SetFooterMargin($f_margin);
        // set auto page breaks
        $b_margin=5;
        $pdf->SetAutoPageBreak(TRUE, $b_margin);
        // add a page
        if($model->info['sticker_cltype']==1||$model->info['sticker_cltype']=="欠"){
            $model->info['sticker_cltype']="欠";
        }else{
            $model->info['sticker_cltype']="残";
        }
        if($model->info['sticker_matype']==1||$model->info['sticker_matype']=="欠"){
            $model->info['sticker_matype']="欠";
        }else{
            $model->info['sticker_matype']="残";
        }
        if($model->info['sticker_bgtype']==1||$model->info['sticker_bgtype']=="欠"){
            $model->info['sticker_bgtype']="欠";
        }else{
            $model->info['sticker_bgtype']="残";
        }
        $arr=array();
        $arr['sign_cust']=$model->info['sign_cust'];
        $arr['sign_tech']=$model->info['sign_tech'] ;
        $arr['sign_qc']=$model->info['sign_qc'];
        $arr['service_dt']=$model->info['service_dt'] ;
        $arr['score_machine']=$model->info['score_machine'] ;
        $arr['score_sink']=$model->info['score_sink'] ;
        $arr['score_toilet']=$model->info['score_toilet'];
        $arr['score_sticker']=$model->info['score_sticker'] ;
        $arr['sticker_cltype']=$model->info['sticker_cltype'] ;
        $arr['sticker_clno']=$model->info['sticker_clno'] ;
        $arr['sticker_matype']=$model->info['sticker_matype'] ;
        $arr['sticker_mano']=$model->info['sticker_mano'] ;
        $arr['sticker_bgtype']=$model->info['sticker_bgtype'];
        $arr['sticker_bgno']=$model->info['sticker_bgno'] ;
        $arr['sticker_reqno']=$model->info['sticker_reqno'];
        $arr['sticker_actno']=$model->info['sticker_actno'];
        $arr['score_enzyme']=$model->info['score_enzyme'] ;
        $arr['score_bluecard']=$model->info['score_bluecard'];
        $arr['improve']=$model->info['improve'] ;
        $arr['praise']=$model->info['praise'];
//        echo $arr['sign_qc'];
//        exit();
        $arr = (object)$arr;
        $image=array();
        $image['sign_cust']=TbHtml::image($model->info['sign_cust'],'QcForm_info_sign_cust_img',array('id'=>'QcForm_info_sign_cust_img','width'=>200,'height'=>40,));
        $image['sign_tech']=TbHtml::image($model->info['sign_tech'],'QcForm_info_sign_cust_img',array('id'=>'QcForm_info_sign_cust_img','width'=>200,'height'=>40,));
        $image['sign_qc']=TbHtml::image($model->info['sign_qc'],'QcForm_info_sign_cust_img',array('id'=>'QcForm_info_sign_cust_img','width'=>200,'height'=>40,));
        $image = (object)$image;
        $pdf->AddPage();
        $tbl=<<<EOD
        
	<div style="text-align: center;font-size:15px;margin: auto;line-height: 50px;" ><img width="90" height="55" src="images/image000.png" >史伟莎清洁服务质检报告</div>
       
<table align="left" border="0" cellpadding="4" cellspacing="1" width="600" >
				  <tr>
             <td width="100px" style="font-size: 12px">客户名称 :</td>
             <td width="200px" style="font-size: 12px"> $model->company_name</td>
             <td width="100px" style="font-size: 12px">
                 外勤名称 :
             </td > 
             <td style="font-size: 12px" width="180px"> $model->job_staff</td>
         </tr>
        <tr>
            <td style="font-size: 12px">服务日期 :</td>
            <td style="font-size: 12px">$arr->service_dt</td>
            <td style="font-size: 12px">
                品检日期 :	
            </td>
            <td style="font-size: 12px">$model->entry_dt</td>
        </tr>
        <tr>
        <td colspan="4"></td>
        </tr>
				<tr class="tit1">
					<td height="30px" colspan='4' width="200px" style="font-size: 14px"><b>质检评分（总分100分）</b></td>
				</tr>
				<tr>
					<td width="110px" style="font-size: 12px">机器评分（14分）</td>
					<td style="font-size: 12px" width="130px"><span>得分：$arr->score_machine</span></td>
					<td width="150px" style="font-size: 12px">除菌液、酵素评（5分）</td>
					<td style="font-size: 12px"><span>得分：$arr->score_enzyme</span></td>
				</tr>
				<tr>
					<td style="font-size: 12px">洗手盆评分（6分）</td>
					<td style="font-size: 12px"><span>得分：$arr->score_sink</span></td>
					<td style="font-size: 12px">常用瓶、蓝卡评分（5分）</td>
					<td style="font-size: 12px"><span>得分：$arr->score_bluecard</span></td>
				</tr>
				<tr>
					<td style="font-size: 12px" height="30px">贴标评分（10分）</td>
					<td style="font-size: 12px"><span>得分：$arr->score_sticker</span></td>
					<td style="font-size: 12px">洁具评分（50分）</td>
					<td style="font-size: 12px"><span>得分：$arr->score_toilet</span></td>
				</tr>
				
				<tr>
					<td width="90px" style="font-size: 12px" >洁具贴</td>
					<td width="20px"></td>
					<td width="34px" style="font-size: 12px"><input type="text" value="">$arr->sticker_cltype</td>
					<td colspan='2' style="font-size: 12px">$arr->sticker_clno</td>
				</tr>
				<tr>
					<td style="font-size: 12px">机器贴</td>
					<td></td>
					<td style="font-size: 12px">$arr->sticker_matype</td>
					<td colspan='2' style="font-size: 12px">$arr->sticker_mano</td>
				</tr>
				<tr>
					<td style="font-size: 12px">大标贴</td>
					<td></td>
					<td style="font-size: 12px">$arr->sticker_bgtype</td>
					<td colspan='2' style="font-size: 12px">$arr->sticker_bgno</td>
				</tr>
				<tr>
					<td style="font-size: 12px">洗手贴/冲厕贴（应有/只有）</td>
					<td></td>
					<td style="font-size: 12px">$arr->sticker_reqno</td>
					<td colspan='2' style="font-size: 12px">$arr->sticker_actno</td>
				</tr>
				<tr>
				<td colspan="4" height=""></td>
                </tr>
				<tr class="tit1">
					<td style="font-size: 14px" width="120px"><b>服务评分（共计）</b></td>
					<td colspan='1'  style="font-size: 14px" width="120">得分  :  $model->service_score</td>
					<td style="font-size: 14px" width="120px"><b>客户评分（10分）</b></td>
					<td colspan='1'  style="font-size: 14px">得分  :  $model->cust_score</td>
				</tr>
				   
				<tr>
				<td colspan="4"></td>
                </tr>
				<tr class="tit1">
					<td style="font-size: 16px"><b>质检成绩  &nbsp;: </b></td>
					<td colspan='2' style="font-size: 16px">$model->qc_result</td>
				</tr>
				<tr>
				<td colspan="4"></td>
                </tr>
				<tr >
					<td height="50px" style="font-size: 12px">客户意见</td>
					<td colspan="3" style="font-size: 12px">$model->cust_comment</td>
				</tr>
				<tr >
					<td height="50px" style="font-size: 12px">质检员意见</td>
						<td colspan="3" style="font-size: 12px">$model->remarks</td>
				</tr>
				<tr>
					<td height="50px" style="font-size: 12px">需改善的地方</td>
						<td colspan="3" style="font-size: 12px">$arr->improve</td>
				</tr>
				<tr>
					<td height="50px" style="font-size: 12px">有赞扬的地方</td>
						<td colspan="3" style="font-size: 12px">$arr->praise</td>
				</tr>
				<tr>

					<td width="60px" style="font-size: 12px">客户签名:</td>
					<td width="100px" style="font-size: 12px">$image->sign_cust</td>
					<td width="150px"></td>
					<td width="72px" style="font-size: 12px">质检员签名:</td>
					<td width="100px" style="font-size: 12px">$image->sign_qc</td>
				</tr>
			</table>
EOD;
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $outstring =$pdf->Output('IA.pdf', 'D');
        return $outstring;
    }

	public function actionView($index)
	{
		$model = new QcForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			switch ($model->service_type) {
				case 'IA': $formfile = $model->new_form ? 'formia' : 'form'; break;
				case 'IB' : $formfile = $model->new_form ? 'formib' : 'form'; break;
				default : $formfile = 'form';
			}
			$this->render($formfile,array('model'=>$model,));
		}
	}
	
	public function actionNew($type='')
	{
		$model = new QcForm('new');

		switch ($type) {
			case 'IA': $formfile = 'formia'; $model->new_form = true; break;
			case 'IB' : $formfile = 'formib'; $model->new_form = true; break;
			default : $formfile = 'form';
		}
		$model->service_type = $type;
		$model->entry_dt = date('Y/m/d');
		$model->qc_dt = date('Y/m/d');
		$model->initData();
		$this->render($formfile,array('model'=>$model,));
	}
	
	public function actionEdit($index)
	{
		$model = new QcForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			switch ($model->service_type) {
				case 'IA': $formfile = $model->new_form ? 'formia' : 'form'; break;
				case 'IB' : $formfile = $model->new_form ? 'formib' : 'form'; break;
				default : $formfile = 'form';
			}

			$this->render($formfile,array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new QcForm('delete');
		if (isset($_POST['QcForm'])) {
			$model->attributes = $_POST['QcForm'];
			$model->saveData();
			Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
		}
		$this->redirect(Yii::app()->createUrl('qc/index'));
	}

    public function actionRemove()
    {
        $model = new QcForm('remove');
        if (isset($_POST['QcForm'])) {
            $model->attributes = $_POST['QcForm'];

        }
        $model->remove($model);
        echo "<script>location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
//        if (! $model->remove($model)) {
//            throw new CHttpException(404,'The requested page does not exist.');
//        } else {
//            switch ($model->service_type) {
//                case 'IA': $formfile = $model->new_form ? 'formia' : 'form'; break;
//                case 'IB' : $formfile = $model->new_form ? 'formib' : 'form'; break;
//                default : $formfile = 'form';
//            }
//            $this->render($formfile,array('model'=>$model,));
//        }
//        $this->redirect(Yii::app()->createUrl('qc/index'));
   }
	
//	public function actionFileupload() {
//		$model = new QcForm();
//		if (isset($_POST['QcForm'])) {
//			$model->attributes = $_POST['QcForm'];
//			
//			$docman = new DocMan($model->docType,$model->id);
//			if (isset($_FILES['attachment'])) $docman->files = $_FILES['attachment'];
//			$docman->fileUpload();
//			echo $docman->genTableFileList(false);
//		} else {
//			echo "NIL";
//		}
//	}
	
	public function actionFileupload($doctype) {
		$model = new QcForm();
		if (isset($_POST['QcForm'])) {
			$model->attributes = $_POST['QcForm'];
			
			$id = ($_POST['QcForm']['scenario']=='new') ? 0 : $model->id;
			$docman = new DocMan($doctype,$id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
			$docman->fileUpload();
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
	
//	public function actionFileRemove() {
//		$model = new QcForm();
//		if (isset($_POST['QcForm'])) {
//			$model->attributes = $_POST['QcForm'];
//			
//			$docman = new DocMan($model->docType,$model->id);
//			$docman->fileRemove($model->removeFileId);
//			echo $docman->genTableFileList(false);
//		} else {
//			echo "NIL";
//		}
//	}
	
	public function actionFileRemove($doctype) {
		$model = new QcForm();
		if (isset($_POST['QcForm'])) {
			$model->attributes = $_POST['QcForm'];
			$docman = new DocMan($doctype,$model->id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			$docman->fileRemove($model->removeFileId[strtolower($doctype)]);
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
	
//	public function actionFileDownload($docId, $fileId) {
//		$sql = "select city from swo_qc where id = $docId";
//		$row = Yii::app()->db->createCommand($sql)->queryRow();
//		if ($row!==false) {
//			$citylist = Yii::app()->user->city_allow();
//			if (strpos($citylist, $row['city']) !== false) {
//				$docman = new DocMan('QC', $docId);
//				$docman->fileDownload($fileId);
//			} else {
//				throw new CHttpException(404,'Access right not match.');
//			}
//		} else {
//			throw new CHttpException(404,'Record not found.');
//		}
//	}

	public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
		$sql = "select city from swo_qc where id = $docId";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$citylist = Yii::app()->user->city_allow();
			if (strpos($citylist, $row['city']) !== false) {
				$docman = new DocMan($doctype,$docId,'QcForm');
				$docman->masterId = $mastId;
				$docman->fileDownload($fileId);
			} else {
				throw new CHttpException(404,'Access right not match.');
			}
		} else {
				throw new CHttpException(404,'Record not found.');
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='qc-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('A06');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('A06');
	}
}
