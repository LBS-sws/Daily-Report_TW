<?php
// Common Functions

class CalcStaff extends Calculation {
	// Count Resigned Staff
	//
	public static function countStaffResignTech($year, $month) {
		return CalcStaff::countStaffResign('TECHNICIAN', $year, $month);
	}
	
	public static function countStaffResignSales($year, $month) {
		return CalcStaff::countStaffResign('SALES', $year, $month);
	}
	
	public static function countStaffResignOffice($year, $month) {
		return CalcStaff::countStaffResign('OFFICE', $year, $month);
	}
	
	public static function countStaffResign($stafftype, $year, $month) {
		$rtn = array();
		$sql = "select a.city, count(a.id) as counter from swo_staff_v a
				where a.leave_dt is not null and timestampdiff(MONTH,a.join_dt,a.leave_dt) > 0
				and year(a.leave_dt)=$year and month(a.leave_dt)=$month 
				and a.staff_type='$stafftype' 
				group by a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

	// Count Existing Staff
	//
	public static function countStaffTech($year, $month) {
		return CalcStaff::countStaff('TECHNICIAN', $year, $month);
	}
	
	public static function countStaffSales($year, $month) {
		return CalcStaff::countStaff('SALES', $year, $month);
	}
	
	public static function countStaffOffice($year, $month) {
		return CalcStaff::countStaff('OFFICE', $year, $month);
	}
	
	public static function countStaff($stafftype, $year, $month) {
		$rtn = array();
		$d1 = $year.'-'.$month.'-1';
		$d2 = date("Y-m-t",strtotime($d1));
/*
		$sql = "select a.city, count(a.id) as counter from swo_staff_v a
				where (a.leave_dt is null or a.leave_dt >= date_add('$d', interval 1 month))
				and a.join_dt < date_add('$d', interval 1 month)
				and a.staff_type='$stafftype' 
				group by a.city
			";
*/
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select e.city, count(e.code) as counter from (
					select a.id as employee_id, a.city, a.code, a.name, a.staff_type, a.staff_status, a.entry_time, a.leave_time, a.lud, a.position
					from hr$suffix.hr_employee a
					where a.id not in (
						select w.employee_id
						from hr$suffix.hr_employee_operate w
						left outer join hr$suffix.hr_employee_operate x on w.employee_id=x.employee_id and x.id > w.id
						where x.id is null and w.lud > '$d2 23:59:59'
					)
				union
					select b.employee_id, b.city, b.code, b.name, b.staff_type, b.staff_status, b.entry_time, b.leave_time, b.lud, b.position
					from hr$suffix.hr_employee_operate b
					left outer join hr$suffix.hr_employee_operate c on b.employee_id=c.employee_id and c.id > b.id
					where c.id is null and b.lud > '$d2 23:59:59'
				) e
				inner join hr$suffix.hr_employee f on f.id = e.employee_id
				left join hr$suffix.hr_dept z on e.position = z.id 
				where (ifnull(str_to_date(e.entry_time,'%Y/%m/%d'),str_to_date(e.entry_time,'%Y-%m-%d')) is null or 
					ifnull(str_to_date(e.entry_time,'%Y/%m/%d'),str_to_date(e.entry_time,'%Y-%m-%d')) < date_add('$d1', interval 1 month))
					and (ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) is null or 
					ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) >= date_add('$d1', interval 1 month))
					and (upper(e.staff_type)='$stafftype' or (e.staff_type='' and z.dept_class='$stafftype'))
				group by e.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}

	// Count Leader
	//
	public static function countLeaderGroup($year, $month) {
		return CalcStaff::countLeader('GROUP', $year, $month);
	}

	public static function countLeaderTeam($year, $month) {
		return CalcStaff::countLeader('TEAM', $year, $month);
	}

	public static function countLeader($type, $year, $month) {
		$rtn = array();
		$d1 = $year.'-'.$month.'-1';
		$d2 = date("Y-m-t",strtotime($d1));
		switch ($type) {
			case 'GROUP': $typex = 'GROUP LEADER'; break;
			case 'TEAM': $typex = 'TEAM LEADER'; break;
			default: $typex = 'NIL';
		}
/*		
		$sql = "select a.city, count(a.id) as counter from swo_staff_v a
				where (a.leave_dt is null or a.leave_dt >= date_add('$d', interval 1 month))
				and a.join_dt < date_add('$d', interval 1 month)
				and a.leader='$type' 
				group by a.city
			";
*/
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select e.city, count(e.code) as counter from (
					select a.id as employee_id, a.city, a.code, a.name, a.staff_type, a.staff_status, a.entry_time, a.leave_time, a.lud, a.position, a.staff_leader
					from hr$suffix.hr_employee a
					where a.id not in (
						select w.employee_id
						from hr$suffix.hr_employee_operate w
						left outer join hr$suffix.hr_employee_operate x on w.employee_id=x.employee_id and x.id > w.id
						where x.id is null and w.lud > '$d2 23:59:59'
					)
				union
					select b.employee_id, b.city, b.code, b.name, b.staff_type, b.staff_status, b.entry_time, b.leave_time, b.lud, b.position, b.staff_leader
					from hr$suffix.hr_employee_operate b
					left outer join hr$suffix.hr_employee_operate c on b.employee_id=c.employee_id and c.id > b.id
					where c.id is null and b.lud > '$d2 23:59:59'
				) e
					inner join hr$suffix.hr_employee f on f.id = e.employee_id
				where (ifnull(str_to_date(e.entry_time,'%Y/%m/%d'),str_to_date(e.entry_time,'%Y-%m-%d')) is null or 
					ifnull(str_to_date(e.entry_time,'%Y/%m/%d'),str_to_date(e.entry_time,'%Y-%m-%d')) < date_add('$d1', interval 1 month))
					and (ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) is null or 
					ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) >= date_add('$d1', interval 1 month))
					and upper(e.staff_leader)='$typex'
				group by e.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}
		return $rtn;
	}
	
	public static function countNoContract($year, $month) {
		$rtn = array();
		$d1 = $year.'-'.$month.'-1';
		$d2 = date("Y-m-t",strtotime($d1));
/*		
		$sql = "select a.city, count(a.id) as counter from swo_staff_v a
				where a.ctrt_start_dt is null and a.join_dt < date_add('$d', interval -1 month)
				and (a.leave_dt is null or a.leave_dt >= date_add('$d', interval 1 month)) 
				group by a.city
			";
*/
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select e.city, count(e.code) as counter from (
					select a.id as employee_id, a.city, a.code, a.name, a.staff_type, a.staff_status, a.entry_time, a.leave_time, a.lud, a.position, a.start_time
					from hr$suffix.hr_employee a
					where a.id not in (
						select w.employee_id
						from hr$suffix.hr_employee_operate w
						left outer join hr$suffix.hr_employee_operate x on w.employee_id=x.employee_id and x.id > w.id
						where x.id is null and w.lud > '$d2 23:59:59'
					)
				union
					select b.employee_id, b.city, b.code, b.name, b.staff_type, b.staff_status, b.entry_time, b.leave_time, b.lud, b.position, b.start_time
					from hr$suffix.hr_employee_operate b
					left outer join hr$suffix.hr_employee_operate c on b.employee_id=c.employee_id and c.id > b.id
					where c.id is null and b.lud > '$d2 23:59:59'
				) e
					inner join hr$suffix.hr_employee f on f.id = e.employee_id
				where (ifnull(str_to_date(e.entry_time,'%Y/%m/%d'),str_to_date(e.entry_time,'%Y-%m-%d')) < date_add('$d1', interval -1 month))
				and (ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) is null or 
					ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) >= date_add('$d1', interval 1 month))
					and e.start_time is null
				group by e.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['counter'];
		}

/*
		$sql = "select a.city, count(a.id) as counter from swo_staff_v a
				where a.ctrt_start_dt < a.ctrt_renew_dt and a.ctrt_renew_dt < date_add('$d', interval -1 month)
				and a.ctrt_renew_dt is not null and a.ctrt_start_dt is not null 
				and (a.leave_dt is null or a.leave_dt >= date_add('$d', interval 1 month)) 
				group by a.city
			";
*/
		$sql = "select e.city, count(e.code) as counter from (
					select a.id as employee_id, a.city, a.code, a.name, a.staff_type, a.staff_status, a.entry_time, a.leave_time, a.lud, a.position, a.start_time,
					if((isnull(a.end_time) or (a.end_time = '')),NULL,(ifnull(str_to_date(a.end_time,'%Y/%m/%d'),str_to_date(a.end_time,'%Y-%m-%d')) + interval 1 day)) AS ctrt_renew_dt
					from hr$suffix.hr_employee a
					where a.id not in (
						select w.employee_id
						from hr$suffix.hr_employee_operate w
						left outer join hr$suffix.hr_employee_operate x on w.employee_id=x.employee_id and x.id > w.id
						where x.id is null and w.lud > '$d2 23:59:59'
					)
				union
					select b.employee_id, b.city, b.code, b.name, b.staff_type, b.staff_status, b.entry_time, b.leave_time, b.lud, b.position, b.start_time,
					if((isnull(b.end_time) or (b.end_time = '')),NULL,(ifnull(str_to_date(b.end_time,'%Y/%m/%d'),str_to_date(b.end_time,'%Y-%m-%d')) + interval 1 day)) AS ctrt_renew_dt
					from hr$suffix.hr_employee_operate b
					left outer join hr$suffix.hr_employee_operate c on b.employee_id=c.employee_id and c.id > b.id
					where c.id is null and b.lud > '$d2 23:59:59'
				) e
				inner join hr$suffix.hr_employee f on f.id = e.employee_id
				where e.start_time < e.ctrt_renew_dt and e.ctrt_renew_dt < date_add('$d1', interval -1 month)
					and (ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) is null or 
					ifnull(str_to_date(f.leave_time,'%Y/%m/%d'),str_to_date(f.leave_time,'%Y-%m-%d')) >= date_add('$d1', interval 1 month))
					and e.start_time is not null and e.ctrt_renew_dt is not null
				group by e.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) 
				if (isset($rtn[$row['city']])) $rtn[$row['city']] += $row['counter'];
		}
		return $rtn;
	}

	public static function sumFlagQty($year, $month) {
		$rtn = array();

		$suffix = Yii::app()->params['envSuffix'];
		$start_dt = date("Y-m-d",strtotime("$year-$month-1"));
		$end_dt = date("Y-m-t",strtotime("$year-$month-1"));

		$sql = "select b.city, sum(a.type_num) as total
                from hr$suffix.hr_prize a 
                LEFT JOIN hr$suffix.hr_employee b ON a.employee_id = b.id
                where a.status=3 and a.prize_date >= '$start_dt' and a.prize_date <= '$end_dt' AND a.id is NOT NULL 
				and a.prize_type=1 and b.city is not null
				group by b.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) $rtn[$row['city']] = $row['total'];
		}
		return $rtn;
	}
}

?>