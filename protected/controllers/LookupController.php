<?php

class LookupController extends Controller
{
	public $interactive = false;
	
	/**
	 * @return array action filters
	 */
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('company','staff','product','companyex2','companyex','staffex','staffex2','productex','template','userstaffex'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Lists all models.
	 */
	public function actionCompany($search) {
		$city = Yii::app()->user->city();
		$searchx = str_replace("'","\'",$search);
		$sql = "select id, concat(left(concat(code,space(8)),8),name) as value from swo_company
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'";
		$result = Yii::app()->db->createCommand($sql)->queryAll();
		$data = TbHtml::listData($result, 'id', 'value');
		echo TbHtml::listBox('lstlookup', '', $data, array('size'=>'15', 'multiple'=>true));
	}

	public function actionCompanyEx($search) {
		$city = Yii::app()->user->city();
		$result = array();
		$searchx = str_replace("'","\'",$search);
		$sql = "select id, code, name, cont_name, cont_phone, address from swo_company
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
			and name not like '%客户!C%'";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$code = $record['code'];
				$name = $record['name'];
					$result[] = array(
						'id'=>$record['id'],
						'value'=>substr($code.str_repeat(' ',8),0,8).$name,
						'contact'=>trim($record['cont_name']).'/'.trim($record['cont_phone']),
						'address'=>$record['address'],
					);
			}
		}
		print json_encode($result);
	}
	
	public function actionCompanyEx2($search) {
		$city = Yii::app()->user->city();
		$result = array();
		$hidden = '';
		$searchx = str_replace("'","\'",$search);
		$sql = "select id, code, name, cont_name, cont_phone, address from swo_company
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[$record['id']] = substr($record['code'].str_repeat(' ',8),0,8).$record['name'];
				$hidden .= TbHtml::hiddenField('otherfld_'.$record['id'].'_contact',trim($record['cont_name']).'/'.trim($record['cont_phone']));
				$hidden .= TbHtml::hiddenField('otherfld_'.$record['id'].'_address',$record['address']);
			}
			$list = TbHtml::radioButtonList('lstlookup','',$result);
			echo $list.$hidden;
		} else {
			echo TbHtml::label(Yii::t('dialog','No Record Found'),false);
		}
	}
	
	public function actionStaff($search)
	{
		$city = Yii::app()->user->city();
		$searchx = str_replace("'","\'",$search);

		$sql = "select id, concat(name, ' (', code, ')') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
				and leave_dt is null or leave_dt=0 or leave_dt > now() ";
		$result1 = Yii::app()->db->createCommand($sql)->queryAll();

		$sql = "select id, concat(name, ' (', code, ')',' ".Yii::t('app','(Resign)')."') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
				and  leave_dt is not null and leave_dt<>0 and leave_dt <= now() ";
		$result2 = Yii::app()->db->createCommand($sql)->queryAll();
//		$result2 = array();
		
		$result = array_merge($result1, $result2);
		$data = TbHtml::listData($result, 'id', 'value');
		echo TbHtml::listBox('lstlookup', '', $data, array('size'=>'15',));
	}

	public function actionStaffEx($search) {
		$city = Yii::app()->user->city();
		$result = array();
		$searchx = str_replace("'","\'",$search);

		$sql = "select id, concat(name, ' (', code, ')') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
				and (leave_dt is null or leave_dt=0 or leave_dt > now()) ";
		$result1 = Yii::app()->db->createCommand($sql)->queryAll();

		$sql = "select id, concat(name, ' (', code, ')',' ".Yii::t('app','(Resign)')."') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
				and  leave_dt is not null and leave_dt<>0 and leave_dt <= now() ";
		$result2 = Yii::app()->db->createCommand($sql)->queryAll();
//		$result2 = array();
		
		$records = array_merge($result1, $result2);
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['id'],
						'value'=>$record['value'],
					);
			}
		}
		print json_encode($result);
	}

	public function actionStaffEx2($search)	{
		$city = Yii::app()->user->city();
		$result = array();
		$searchx = str_replace("'","\'",$search);

		$sql = "select id, concat(name, ' (', code, ')') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
				and (leave_dt is null or leave_dt=0 or leave_dt > now()) ";
        $records = Yii::app()->db->createCommand($sql)->queryAll();

//		$sql = "select id, concat(name, ' (', code, ')',' ".Yii::t('app','(Resign)')."') as value from swo_staff_v
//				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
//				and  leave_dt is not null and leave_dt<>0 and leave_dt <= now() ";
//		$result2 = Yii::app()->db->createCommand($sql)->queryAll();
		
//		$records = array_merge($result1, $result2);
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[$record['id']] = $record['value'];
			}
			$list = TbHtml::radioButtonList('lstlookup','',$result);
			echo $list;
		} else {
			echo TbHtml::label(Yii::t('dialog','No Record Found'),false);
		}
	}

	public function actionUserstaffEx($search, $incity='')
	{
		$city = $incity;
		$result = array();
		$searchx = str_replace("'","\'",$search);

		$sql = "select id, concat(name, ' (', code, ')') as value from swo_staff_v
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
			";
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['id'],
						'value'=>$record['value'],
					);
			}
		}
		print json_encode($result);
	}

	public function actionProduct($search)
	{
		$city = '99999';	//Yii::app()->user->city();
		$searchx = str_replace("'","\'",$search);
		$sql = "select id, concat(left(concat(code,space(8)),8),description) as value from swo_product
				where (code like '%".$searchx."%' or description like '%".$searchx."%') and city='".$city."'";
		$result = Yii::app()->db->createCommand($sql)->queryAll();
		$data = TbHtml::listData($result, 'id', 'value');
		echo TbHtml::listBox('lstlookup', '', $data, array('size'=>'15',));
	}

	public function actionProductEx($search)
	{
		$city = '99999';	//Yii::app()->user->city();
		$result = array();
		$searchx = str_replace("'","\'",$search);
		$sql = "select id, concat(left(concat(code,space(8)),8),description) as value from swo_product
				where (code like '%".$searchx."%' or description like '%".$searchx."%') and city='".$city."'";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['id'],
						'value'=>$record['value'],
					);
			}
		}
		print json_encode($result);
	}

	public function actionTemplate($system) {
		$result = array();
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select temp_id, temp_name from security$suffix.sec_template
				where system_id='$system'
			";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['temp_id'],
						'name'=>$record['temp_name'],
					);
			}
		}
		print json_encode($result);
	}

//	public function actionSystemDate()
//	{
//		echo CHtml::tag( date('Y-m-d H:i:s'));
//		Yii::app()->end();
//	}

	protected function detectUTF8($string){
	        return preg_match('%(?:
		        [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
		        |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
		        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
		        |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
		        |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
		        |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
		        |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
	        )+%xs', $string);
	}

	protected function removeNonUtf8($string) {
		$regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;
		return preg_replace($regex, '$1', $string);
	}
}
