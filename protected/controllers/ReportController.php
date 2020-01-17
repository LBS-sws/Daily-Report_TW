<?php

class ReportController extends Controller 
{
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
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
			array('allow','actions'=>array('all'),'expression'=>array('ReportController','allowAll')),
			array('allow','actions'=>array('custnew'),'expression'=>array('ReportController','allowCustnew')),
			array('allow','actions'=>array('custrenew'),'expression'=>array('ReportController','allowCustrenew')),
			array('allow','actions'=>array('custamend'),'expression'=>array('ReportController','allowCustamend')),
			array('allow','actions'=>array('custsuspend'),'expression'=>array('ReportController','allowCustsuspend')),
			array('allow','actions'=>array('custresume'),'expression'=>array('ReportController','allowCustresume')),
			array('allow','actions'=>array('custterminate'),'expression'=>array('ReportController','allowCustterminate')),
			array('allow','actions'=>array('complaint'),'expression'=>array('ReportController','allowComplaint')),
			array('allow','actions'=>array('qc'),'expression'=>array('ReportController','allowQc')),
			array('allow','actions'=>array('staff'),'expression'=>array('ReportController','allowStaff')),
			array('allow','actions'=>array('enquiry'),'expression'=>array('ReportController','allowEnquiry')),
			array('allow','actions'=>array('logistic'),'expression'=>array('ReportController','allowLogistic')),
			array('allow','actions'=>array('renewal'),'expression'=>array('ReportController','allowRenewal')),
			array('allow','actions'=>array('monthly'),'expression'=>array('ReportController','allowMonthly')),
			array('allow','actions'=>array('feedbackstat'),'expression'=>array('ReportController','allowFeedbackstat')),
			array('allow','actions'=>array('feedback'),'expression'=>array('ReportController','allowFeedback')),
			array('allow', 
				'actions'=>array('generate'),
				'expression'=>array('ReportController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('B');
	}

// Function: Add Item to Queue
	protected function addQueueItem($rpt_id, $criteria, $paper_sz, $rpt_array=array()) {
		$uid = Yii::app()->user->id;
		$bosses = Yii::app()->params['feedbackCcBoss'];
		$now = date("Y-m-d H:i:s");
		if (empty($rpt_array)) $rpt_array = array($rpt_id=>$criteria->name);
//		$criteria->ccuser = (!empty($criteria->ccuser) && is_array($criteria->ccuser)) ? array_merge($criteria->ccuser, $bosses) : $bosses;
		$data = array(
					'RPT_ID'=>$rpt_id,
					'RPT_NAME'=>$criteria->name,
					'CITY'=>(is_array($criteria->city) ? json_encode($criteria->city) : $criteria->city),
					'PAPER_SZ'=>$paper_sz,
					'FIELD_LST'=>$criteria->fields,
					'START_DT'=>General::toMyDate($criteria->start_dt),
					'END_DT'=>General::toMyDate($criteria->end_dt),
					'TARGET_DT'=>General::toMyDate($criteria->target_dt),
					'EMAIL'=>$criteria->email,
					'EMAILCC'=>$criteria->emailcc,
					'TOUSER'=>$criteria->touser,
					'CCUSER'=>json_encode($criteria->ccuser),
					'RPT_ARRAY'=>json_encode($rpt_array),
					'LANGUAGE'=>Yii::app()->language,
					'CITY_NAME'=>Yii::app()->user->city_name(),
					'YEAR'=>$criteria->year,
					'MONTH'=>$criteria->month,
				);
		if (!empty($criteria->type)) $data['TYPE'] = (is_array($criteria->type) ? json_encode($criteria->type) : $criteria->type);
		
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$sql = "insert into swo_queue (rpt_desc, req_dt, username, status, rpt_type)
						values(:rpt_desc, :req_dt, :username, 'P', :rpt_type)
					";
			$command=$connection->createCommand($sql);
			if (strpos($sql,':rpt_desc')!==false)
				$command->bindParam(':rpt_desc',$criteria->name,PDO::PARAM_STR);
			if (strpos($sql,':req_dt')!==false)
				$command->bindParam(':req_dt',$now,PDO::PARAM_STR);
			if (strpos($sql,':username')!==false)
				$command->bindParam(':username',$uid,PDO::PARAM_STR);
			if (strpos($sql,':rpt_type')!==false)
				$command->bindParam(':rpt_type',$criteria->format,PDO::PARAM_STR);
			$command->execute();
			$qid = Yii::app()->db->getLastInsertID();
	
			$sql = "insert into swo_queue_param (queue_id, param_field, param_value)
						values(:queue_id, :param_field, :param_value)
					";
			foreach ($data as $key=>$value) {
				$command=$connection->createCommand($sql);
				if (strpos($sql,':queue_id')!==false)
					$command->bindParam(':queue_id',$qid,PDO::PARAM_INT);
				if (strpos($sql,':param_field')!==false)
					$command->bindParam(':param_field',$key,PDO::PARAM_STR);
				if (strpos($sql,':param_value')!==false)
					$command->bindParam(':param_value',$value,PDO::PARAM_STR);
				$command->execute();
			}

			if ($criteria->format=='FEED') {
				$sql = "insert into swo_queue_user (queue_id, username)
						values(:queue_id, :username)
					";

				$command=$connection->createCommand($sql);
				if (strpos($sql,':queue_id')!==false)
					$command->bindParam(':queue_id',$qid,PDO::PARAM_INT);
				if (strpos($sql,':username')!==false)
					$command->bindParam(':username',$criteria->touser,PDO::PARAM_STR);
				$command->execute();
				
				if (!empty($criteria->ccuser) && is_array($criteria->ccuser)) {
					foreach ($criteria->ccuser as $user) {
						$command=$connection->createCommand($sql);
						if (strpos($sql,':queue_id')!==false)
							$command->bindParam(':queue_id',$qid,PDO::PARAM_INT);
						if (strpos($sql,':username')!==false)
							$command->bindParam(':username',$user,PDO::PARAM_STR);
						$command->execute();
					}
				}
			}
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}
	
// Report: All
	protected static function allowAll() {
		return Yii::app()->user->validFunction('B11');
	}

	public function actionAll() {
		$this->function_id = 'B11';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUIFeedback('all', 'All Daily Reports');
	}

	protected function genAll($criteria) {
		$rptname = array(
				'RptCustnew'=>'Customer Report - New',
				'RptCustrenew'=>'Customer Report - Renewal',
				'RptCustamend'=>'Customer Report - Amendment',
				'RptCustsuspend'=>'Customer Report - Suspended',
				'RptCustresume'=>'Customer Report - Resume',
				'RptCustterminate'=>'Customer Report - Terminate',
				'RptComplaint'=>'Complaint Cases Report',
				'RptEnquiry'=>'Customer Report - Enquiry',
				'RptLogistic'=>'Product Delivery Report',
				'RptQc'=>'Quality Control Report',
				'RptStaff'=>'Staff Report',
			);
		$criteria->name = 'All Daily Reports';
		$this->addQueueItem('RptAll', $criteria, 'A3', $rptname);
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}
	
/*
	protected function genAll($criteria) {
		$criteria->name = 'Customer Report - New';
		$this->addQueueItem('RptCustnew', $criteria, 'A3');
		
		$criteria->name = 'Customer Report - Amendment';
		$this->addQueueItem('RptCustamend', $criteria, 'A3');
		
		$criteria->name = 'Customer Report - Suspended';
		$this->addQueueItem('RptCustsuspend', $criteria, 'A3');

		$criteria->name = 'Customer Report - Resume';
		$this->addQueueItem('RptCustresume', $criteria, 'A3');

		$criteria->name = 'Customer Report - Terminate';
		$this->addQueueItem('RptCustterminate', $criteria, 'A3');

		$criteria->name = 'Complaint Cases Report';
		$this->addQueueItem('RptComplaint', $criteria, 'A3');

		$criteria->name = 'Customer Report - Enquiry';
		$this->addQueueItem('RptEnquiry', $criteria, 'A3');

		$criteria->name = 'Product Delivery Report';
		$this->addQueueItem('RptLogistic', $criteria, 'A3');

		$criteria->name = 'Quality Control Report';
		$this->addQueueItem('RptQc', $criteria, 'A3');

		$criteria->name = 'Staff Report';
		$this->addQueueItem('RptStaff', $criteria, 'A3');

		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}
*/

// Report: Customer - New	
	protected static function allowCustnew() {
		return Yii::app()->user->validFunction('B02');
	}
	
	public function actionCustnew() {
		$this->function_id = 'B02';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('custnew', 'Customer Report - New');
	}

	protected function genCustNew($criteria) {
		$this->addQueueItem('RptCustnew', $criteria, 'A3');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Customer - Renewal	
	protected static function allowCustrenew() {
		return Yii::app()->user->validFunction('B15');
	}
	
	public function actionCustrenew() {
		$this->function_id = 'B15';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('custrenew', 'Customer Report - Renewal');
	}

	protected function genCustRenew($criteria) {
		$this->addQueueItem('RptCustrenew', $criteria, 'A3');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Customer - Amendment	
	protected static function allowCustamend() {
		return Yii::app()->user->validFunction('B05');
	}

	public function actionCustamend() {
		$this->function_id = 'B05';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('custamend', 'Customer Report - Amendment');
	}

	protected function genCustAmend($criteria) {
		$this->addQueueItem('RptCustamend', $criteria, 'A3');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Customer - Suspension
	protected static function allowCustSuspend() {
		return Yii::app()->user->validFunction('B03');
	}

	public function actionCustsuspend() {
		$this->function_id = 'B03';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('custsuspend','Customer Report - Suspended');
	}

	protected function genCustSuspend($criteria) {
		$this->addQueueItem('RptCustsuspend', $criteria, 'A3');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Customer - Resume
	protected static function allowCustresume() {
		return Yii::app()->user->validFunction('B04');
	}
	
	public function actionCustresume() {
		$this->function_id = 'B04';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('custresume','Customer Report - Resume');
	}

	protected function genCustResume($criteria) {
		$this->addQueueItem('RptCustresume', $criteria, 'A3');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Customer - Termination
	protected static function allowCustTerminate() {
		return Yii::app()->user->validFunction('B10');
	}

	public function actionCustterminate() {
		$this->function_id = 'B10';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('custterminate','Customer Report - Terminate');
	}

	protected function genCustTerminate($criteria) {
		$this->addQueueItem('RptCustterminate', $criteria, 'A3');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Complaint Cases
	protected static function allowComplaint() {
		return Yii::app()->user->validFunction('B01');
	}
	
	public function actionComplaint() {
		$this->function_id = 'B01';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('complaint','Complaint Cases Report');
	}

	protected function genComplaint($criteria) {
		$this->addQueueItem('RptComplaint', $criteria, 'A3');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Customer enquiry
	protected static function allowEnquiry() {
		return Yii::app()->user->validFunction('B06');
	}
	
	public function actionEnquiry() {
		$this->function_id = 'B06';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('enquiry','Customer Report - Enquiry');
	}

	protected function genEnquiry($criteria) {
		$this->addQueueItem('RptEnquiry', $criteria, 'A3');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Product Delivery 
	protected static function allowLogistic() {
		return Yii::app()->user->validFunction('B07');
	}
	
	public function actionLogistic() {
		$this->function_id = 'B07';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('logistic','Product Delivery Report');
	}

	protected function genLogistic($criteria) {
		$this->addQueueItem('RptLogistic', $criteria, 'A3');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: QC
	protected static function allowQc() {
		return Yii::app()->user->validFunction('B08');
	}
	
	public function actionQc() {
		$this->function_id = 'B08';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('qc', 'Quality Control Report');
	}
	
	protected function genQc($criteria) {
		$this->addQueueItem('RptQc', $criteria, 'A3');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Staff
	protected static function allowStaff() {
		return Yii::app()->user->validFunction('B09');
	}
	
	public function actionStaff() {
		$this->function_id = 'B09';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('staff', 'Staff Report');
	}

	protected function genStaff($criteria) {
		$this->addQueueItem('RptStaff', $criteria, 'A3');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Renewal
	protected static function allowRenewal() {
		return Yii::app()->user->validFunction('B13');
	}
	
	public function actionRenewal() {
		$this->function_id = 'B13';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('renewal', 'Renewal Reminder Report', 'target_dt,format,city');
	}

	protected function genRenewal($criteria) {
		$this->addQueueItem('RptRenewal', $criteria, 'A4');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Feedback Statistic
	protected static function allowFeedbackstat() {
		return Yii::app()->user->validFunction('B16');
	}
	
	public function actionFeedbackstat() {
		$this->function_id = 'B16';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('feedbackstat', 'Feedback Statistics Report', 'year,month,format');
	}

	protected function genFeedbackstat($criteria) {
		$this->addQueueItem('RptFeedbackstat', $criteria, 'A4');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Feedback
	protected static function allowFeedback() {
		return Yii::app()->user->validFunction('B17');
	}
	
	public function actionFeedback() {
		$this->function_id = 'B17';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUIFbList('feedback', 'Feedback List Report', 'start_dt,end_dt,format');
	}

	protected function genFeedback($criteria) {
		$this->addQueueItem('RptFeedback', $criteria, 'A4');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// Report: Monthly
	protected static function allowMonthly() {
		return Yii::app()->user->validFunction('B14');
	}
	
	public function actionMonthly() {
		$this->function_id = 'B14';
		Yii::app()->session['active_func'] = $this->function_id;
		$this->showUI('monthly', 'Monthly Report', 'year,month,city');
	}

	protected function genMonthly($criteria) {
		$criteria->format = 'MTHRPT';
		$this->addQueueItem('RptMonthly', $criteria, 'A4');
		Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
	}

// ***************************
//  Common Functions
// ***************************
	protected function showUI($id, $name, $fields='start_dt,end_dt,format,city') {
		$this->showUICore($id, $name, $fields, 'form');
	}
	
	protected function showUIFeedback($id, $name) {
		$this->showUICore($id, $name, 'start_dt,end_dt,format,city', 'form_feedback');
	}
	
	protected function showUIFbList($id, $name) {
		$this->showUICore($id, $name, 'start_dt,end_dt,format,city', 'form_fblist');
	}

	protected function showUICore($id, $name, $fields='start_dt,end_dt,format,city', $form='form') {
		$model = new ReportForm;
		if (isset($_POST['ReportForm'])) {
			$model->attributes = $_POST['ReportForm'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_r00']) && !empty($session['criteria_r00'])) {
				$criteria = $session['criteria_r00'];
				$model->setCriteria($criteria);
			} else {
				$model->end_dt = date("Y/m/d");
//				$model->start_dt = date("Y/m/d", strtotime("-1 months", strtotime($model->end_dt)));
				$model->start_dt = date("Y", strtotime($model->end_dt)).'/'.date("m", strtotime($model->end_dt)).'/01';
				$model->target_dt = date("Y/m/d");
				$model->format = 'EXCEL';
				$model->year = date("Y");
				$model->month = date("m");
			}
		}
		$model->id = $id;
		$model->name = $name;
		if (Yii::app()->user->isSingleCity())
			$model->city = Yii::app()->user->city();
//		else {
//			$items = explode(",",str_replace("'","",Yii::app()->user->city_allow()));
//			$model->city = $items[0];
//		}
		$model->fields = $fields;
		$model->form = $form;
		$this->render($form,array('model'=>$model));
	}

	public function actionGenerate() {
		if (isset($_POST['ReportForm'])) {
			$model = new ReportForm();
			$model->attributes = $_POST['ReportForm'];
			$report = $model->id;
			if ($model->validate()) {
				if ($model->id=='complaint') $this->genComplaint($model);
				if ($model->id=='custnew') $this->genCustNew($model);
				if ($model->id=='custrenew') $this->genCustRenew($model);
				if ($model->id=='custamend') $this->genCustAmend($model);
				if ($model->id=='custsuspend') $this->genCustSuspend($model);
				if ($model->id=='custresume') $this->genCustResume($model);
				if ($model->id=='custterminate') $this->genCustTerminate($model);
				if ($model->id=='qc') $this->genQc($model);
				if ($model->id=='logistic') $this->genLogistic($model);
				if ($model->id=='staff') $this->genStaff($model);
				if ($model->id=='enquiry') $this->genEnquiry($model);
				if ($model->id=='all') $this->genAll($model);
				if ($model->id=='renewal') $this->genRenewal($model);
				if ($model->id=='monthly') $this->genMonthly($model);
				if ($model->id=='feedbackstat') $this->genFeedbackstat($model);
				if ($model->id=='feedback') $this->genFeedback($model);
//				Yii::app()->end();
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
			}
//			$form = ($model->format=='FEED') ? 'form_feedback' : 'form';
			$form = $model->form;
			$this->render($form,array('model'=>$model,));
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='logistic-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
?>
