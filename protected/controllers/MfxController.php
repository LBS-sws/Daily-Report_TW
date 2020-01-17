<?php

class MfxController extends Controller
{
	public $function_id='H02';
	
    protected static $actions = array(
        'index'=>'H02',
        'overtimelist'=>'YB02',
        'pennantexlist'=>'YB05',
        'pennantculist'=>'YB06',
        'leavelist'=>'YB03',
    );
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
				'actions'=>array('new','edit','delete','save',"templates",'fileupload','fileremove','filedownload'),
				'expression'=>array('MfxController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downs','filedownload'),
				'expression'=>array('MfxController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
        $model = new ReportH02Form;
        $model->retrieveDatas($model);
//        print_r('<pre>');
//        print_r($model);
        $this->render('index',array('model'=>$model));
	}



	public function actionView()
	{
        $model=new ReportH02Form($_POST['ReportH02Form']);

//        $date1 = "2008-08-11";
//        $date2 = "2008-11-06";
//        $monthNum = $this->getMonth( $date1 , $date2 );

        $model->retrieveData($model);
//        print_r('<pre>');
//        print_r($model);
			$this->render('form',array('model'=>$model,));

	}

	public function actiondowns(){
        $model = new ReportH02Form;
        $model->scenario = $_POST['ReportH02Form'];
        $model->retrieveData($model);
        $model->retrieveXiaZai($model);
//        print_r("<pre>");
//        print_r($model);
    }
//	public function getMonth( $date1, $date2){
//        $time1 = strtotime($date1); // 自动为00:00:00 时分秒
//        $time2 = strtotime($date2);
//        $monarr = array();
//        while( ($time1 = strtotime('+1 month', $time1)) <= $time2){
//            $monarr[] = date('n',$time1); // 取得递增月;
//        }
////        print_r($monarr);
//
//    }

    public function getMonthNum( $date1, $date2, $tags='-' ){
        $date1 = explode($tags,$date1);
        $date2 = explode($tags,$date2);

        return abs($date1[0] - $date2[0]) * 12 + abs($date1[1] - $date2[1]);
    }


	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('H02');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('H02');
	}
}
