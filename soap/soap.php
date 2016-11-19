<?php
header("content-type:text/html;charset=utf-8");
try {
	$url = './EaopServerPort.wsdl';
// 	$url = 'https://221.179.11.204:9090/eaop/EaopServerPort?wsdl';
// 	ini_set("soap.wsdl_cache_enabled", 0);
	$local_cert = "./ca.pem";
// 	$header = array(
// 			'local_cert' => $local_cert, //client证书信息
// 			'passphrase'=> 'ydgj123icmcc' //密码
// 	);
	/*$context = stream_context_create( array (
			'ssl' => array (
					'verify_peer' => false,
					'allow_self_signed' => true
			),
	));
	$options['stream_context'] = $context;*/
	$context = stream_context_create( array (
			'ssl' => array (
					'local_cert' => $local_cert, //client证书信息
					'passphrase'=> 'ydgj123icmcc', //密码
					'verify_peer' => true,
					'allow_self_signed' => true
			),
	));
	$options['stream_context'] = $context;
	$client = new SoapClient($url, $options);
	
// 	$client = new SoapClient($url, array( 'local_cert' => $local_cert, 
// 				'verify_peer' => false,
// 				'allow_self_signed' => true, 
// 				'trace' => 1,
//                 'exceptions' => 1,
//                 'soap_version' => SOAP_1_1,));

	/*$xml = "<queryreq>
				<msgheader>
					<menuid>0</menuid>
					<process_code>query</process_code>
					<req_time>20121001123030</req_time>
					<req_seq>20121001123030000001</req_seq>
					<route>
						<route_type>1</route_type>
						<route_value>15011891096</route_value>
					</route>
					<channelinfo>
					    <operatorid>0</operatorid>
					    <channelid>baishimedia</channelid>
					    <unitid>0</unitid>
					</channelinfo>
				</msgheader>
				<msgbody>
					<userinfo>
						<servernum>15011891096</servernum>
					</userinfo>
					<serviceinfo>
						<id>GPRS_FLOW</id>
					</serviceinfo>
				</msgbody>
			</queryreq>";*/
	
	$xml = "<ccqrysubselectprodsreq> 
				<msgheader>
    				<menuid>0</menuid>
    				<process_code>ccqrysubselectprods</process_code>
    				<verify_code>0</verify_code>
    				<req_time>20121001123030</req_time>
    				<req_seq>20121001123030000001</req_seq>
    				<unicontact></unicontact>
    				<testflag></testflag>
   					<route>
        				<route_type>0</route_type>
        				<route_value>200</route_value>
    				</route>
					<channelinfo>
					<operatorid>0</operatorid>
						<channelid>baishimedia</channelid>
						<unitid>0</unitid>
					</channelinfo>
				</msgheader> 
				<msgbody> 
					<servnumber>15986742061</servnumber>  
					<prodcode></prodcode>  
					<querytype>0</querytype>  
					<prodchinfo> 
						<prodchinfolist> 
							<prodid>prod.10086000004543</prodid>  
							<solutionid></solutionid> 
						</prodchinfolist> 
					</prodchinfo> 
					<prodgroupid></prodgroupid> 
				</msgbody> 
			</ccqrysubselectprodsreq>";

// 	print_r($client->__getFunctions());
	echo '------------';
// 	print_r($client->__getTypes());
	$res = $client->handle(array('arg0'=>$xml));
	var_dump($res);
} catch (SOAPFault $e) {
	print $e;
}