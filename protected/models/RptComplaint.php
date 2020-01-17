<?php
class RptComplaint extends ReportData2 {	public function fields() {		return array(			'entry_dt'=>array('label'=>Yii::t('followup','Date'),'width'=>18,'align'=>'C'),			'type'=>array('label'=>Yii::t('followup','Type'),'width'=>8,'align'=>'C'),
			'company_name'=>array('label'=>Yii::t('service','Customer'),'width'=>22,'align'=>'L'),
			'content'=>array('label'=>Yii::t('followup','Content'),'width'=>20,'align'=>'L'),
			'cont_info'=>array('label'=>Yii::t('followup','Contact'),'width'=>23,'align'=>'L'),
			'resp_staff'=>array('label'=>Yii::t('followup','Resp. Staff'),'width'=>15,'align'=>'L'),
			'resp_tech'=>array('label'=>Yii::t('followup','Resp. Tech.'),'width'=>15,'align'=>'L'),			'mgr_notify'=>array('label'=>Yii::t('followup','Notify Manager'),'width'=>8,'align'=>'C'),			'sch_dt'=>array('label'=>Yii::t('followup','Schedule Date'),'width'=>18,'align'=>'C'),
			'follow_staff'=>array('label'=>Yii::t('followup','Follow-up Staff'),'width'=>15,'align'=>'L'),
			'leader'=>array('label'=>Yii::t('followup','Is Leader'),'width'=>8,'align'=>'C'),
			'follow_tech'=>array('label'=>Yii::t('followup','Follow-up Tech.'),'width'=>15,'align'=>'L'),			'fin_dt'=>array('label'=>Yii::t('followup','Finish Date'),'width'=>18,'align'=>'C'),			'follow_action'=>array('label'=>Yii::t('followup','Follow-up Action'),'width'=>15,'align'=>'L'),
			'mgr_talk'=>array('label'=>Yii::t('followup','Talk with Manager'),'width'=>8,'align'=>'C'),
			'changex'=>array('label'=>Yii::t('followup','Change'),'width'=>15,'align'=>'L'),
			'tech_notify'=>array('label'=>Yii::t('followup','Notify Tech.'),'width'=>15,'align'=>'L'),
			'fp_fin_dt'=>array('label'=>Yii::t('followup','Finish Follow Up Date'),'width'=>18,'align'=>'C'),
			'fp_call_dt'=>array('label'=>Yii::t('followup','Call Date'),'width'=>18,'align'=>'C'),
			'fp_cust_name'=>array('label'=>Yii::t('followup','Customer Name'),'width'=>15,'align'=>'L'),
			'fp_comment'=>array('label'=>Yii::t('followup','Comment'),'width'=>15,'align'=>'L'),
			'svc_next_dt'=>array('label'=>Yii::t('followup','Next Service Date'),'width'=>18,'align'=>'C'),
			'svc_call_dt'=>array('label'=>Yii::t('followup','Call Date'),'width'=>18,'align'=>'C'),
			'svc_cust_name'=>array('label'=>Yii::t('followup','Customer Name'),'width'=>15,'align'=>'L'),
			'svc_comment'=>array('label'=>Yii::t('followup','Comment'),'width'=>15,'align'=>'L'),
			'mcard_remarks'=>array('label'=>Yii::t('followup','Med. Card Remarks'),'width'=>10,'align'=>'L'),
			'mcard_staff'=>array('label'=>Yii::t('followup','Med. Card Staff'),'width'=>10,'align'=>'L'),
			'date_diff'=>array('label'=>Yii::t('followup','Date Diff.'),'width'=>10,'align'=>'L'),
		);	}
	public function header_structure() {
		return array(
			'entry_dt',
			'type',
			'company_name',
			'content',
			'cont_info',
			'resp_staff',
			'resp_tech',
			'mgr_notify',
			'sch_dt',
			'follow_staff',
			'leader',
			'follow_tech',
			'fin_dt',
			'follow_action',
			'mgr_talk',
			'changex',
			'tech_notify',
			array(
				'label'=>Yii::t('followup','Call Back'),
				'child'=>array(
					array(
						'label'=>Yii::t('followup','Follow Up After Finish'),
						'child'=>array(
							'fp_fin_dt',
							'fp_call_dt',
							'fp_cust_name',
							'fp_comment',
						),
					),
					array(
						'label'=>Yii::t('followup','Follow Up After Service'),
						'child'=>array(
							'svc_next_dt',
							'svc_call_dt',
							'svc_cust_name',
							'svc_comment',
						),
					),
				),
			),
			'mcard_remarks',
			'mcard_staff',
			'date_diff',
			
		);
	}
	
//	public function subsections() {
//		return array(
//			array(
//				'call_type'=>array('label'=>Yii::t('followup','Call Type'),'width'=>30,'align'=>'L'),
//				'call_dt'=>array('label'=>Yii::t('followup','Call Date'),'width'=>30,'align'=>'L'),
//				'cust_name'=>array('label'=>Yii::t('followup','Customer Name'),'width'=>30,'align'=>'L'),
//				'comment'=>array('label'=>Yii::t('followup','Comment'),'width'=>30,'align'=>'L'),
//				'call_fin_dt'=>array('label'=>Yii::t('followup','Finish Date'),'width'=>30,'align'=>'L'),
//				'next_svc_dt'=>array('label'=>Yii::t('followup','Next Service Date'),'width'=>30,'align'=>'L'),
//			),
//		);
//	}
		public function retrieveData() {
//		$city = Yii::app()->user->city();
		$city = $this->criteria->city;
		$sql = "select a.*
					from swo_followup a 
		";
		$where = "where a.city='".$city."'";
		if (isset($this->criteria)) {			if (isset($this->criteria->start_dt))				$where .= (($where=='where') ? " " : " and ")."a.entry_dt>='".General::toDate($this->criteria->start_dt)."'";			if (isset($this->criteria->end_dt))				$where .= (($where=='where') ? " " : " and ")."a.entry_dt<='".General::toDate($this->criteria->end_dt)."'";		}		if ($where!='where') $sql .= $where;	
		$sql .= " order by a.entry_dt";		$rows = Yii::app()->db->createCommand($sql)->queryAll();		if (count($rows) > 0) {			foreach ($rows as $row) {				$temp = array();				$temp['entry_dt'] = General::toDate($row['entry_dt']);				$temp['type'] = $row['type'];
				$temp['company_name'] = $row['company_name'];				$temp['content'] = $row['content'];
				$temp['cont_info'] = $row['cont_info'];
				$temp['resp_staff'] = $row['resp_staff'];				$temp['resp_tech'] = $row['resp_tech'];				$temp['mgr_notify'] = ($row['mgr_notify']=='Y'?Yii::t('misc','Yes'):Yii::t('misc','No'));
				$temp['sch_dt'] = General::toDate($row['sch_dt']);
				$temp['follow_staff'] = $row['follow_staff'];
				$temp['leader'] = ($row['leader']=='Y'?Yii::t('misc','Yes'):Yii::t('misc','No'));
				$temp['follow_tech'] = $row['follow_tech'];
				$temp['fin_dt'] = General::toDate($row['fin_dt']);
				$temp['follow_action'] = $row['follow_action'];
				$temp['mgr_talk'] = ($row['mgr_talk']=='Y'?Yii::t('misc','Yes'):Yii::t('misc','No'));
				$temp['changex'] = $row['changex'];
				$temp['tech_notify'] = $row['tech_notify'];
				$temp['fp_fin_dt'] = General::toDate($row['fp_fin_dt']);
				$temp['fp_call_dt'] = General::toDate($row['fp_call_dt']);
				$temp['fp_cust_name'] = $row['fp_cust_name'];
				$temp['fp_comment'] = $row['fp_comment'];
				$temp['svc_next_dt'] = General::toDate($row['svc_next_dt']);
				$temp['svc_call_dt'] = General::toDate($row['svc_call_dt']);
				$temp['svc_cust_name'] = $row['svc_cust_name'];
				$temp['svc_comment'] = $row['svc_comment'];
				$temp['mcard_remarks'] = $row['mcard_remarks'];
				$temp['mcard_staff'] = $row['mcard_staff'];
				$temp['date_diff'] = (empty($temp['fp_call_dt']) || empty($temp['entry_dt'])) ? '' : (strtotime($temp['fp_call_dt'])-strtotime($temp['entry_dt']))/86400;
				$this->data[] = $temp;			}		}		return true;	}
	
	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria->city) : '';
		return parent::getReportName().$city_name;
	}
}
?>