<?php
header("Content-Type: text/html; charset=UTF-8");

function get_rand($proArr) {
	$result = '';
	//概率数组的总概率精度
	$proSum = array_sum($proArr);
	//概率数组循环
	print_r($proArr);
	foreach($proArr as $key=>$proCur) {
		$randNum = mt_rand(1, $proSum);
// 		echo 'randNum:='.$randNum.'<>'.$proCur.'    ';
// 		echo 'proSum:='.$proSum.'|';
		if ($randNum <= $proCur) {
			$result = $key;
// 			echo '==='.$key.'===';
			break;
		} else {
			$proSum -= $proCur;
		}
	}
	unset($proArr);
	return $result;
}

$prize_arr = array(
		0 => array('id' => 1, 'prize' => '平板电脑1', 'v' => 1),
		1 => array('id' => 2, 'prize' => '数码相机2', 'v' => 1),
		2 => array('id' => 3, 'prize' => '音响设备3', 'v' => 1),
		3 => array('id' => 4, 'prize' => '4G优盘4', 'v' => 3),
		4 => array('id' => 5, 'prize' => '10Q币5', 'v' => 4),
		5 => array('id' => 6, 'prize' => '谢谢惠顾6', 'v' => 90),
);

foreach($prize_arr as $key => $val) {
	$arr[$val['id']] = $val['v'];
}
$rid = get_rand($arr);			//根据概率获取奖项id

$res['yes'] = $prize_arr[$rid-1]['prize'];			//中奖项
unset($prize_arr[$rid-1]);							//将中奖项从数组中剔除，剩下未中奖项
shuffle($prize_arr);								//打乱数组顺序
for ($i=0; $i<count($prize_arr); $i++) {
	$pr[] = $prize_arr[$i]['prize'];
}
$res['no'] = $pr;
print_r($res);
// echo json_encode($res);