<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

header('Content-Type: application/json');

global $qb;
global $user;
global $serverName;

ini_set('display_errors', '1');

$result = ['error' => ['message' => 'Payment was not completed']];

$answer = $_GET;

if(!isset($answer['params']) || !isset($answer['method']))
    exit(json_encode($result, JSON_UNESCAPED_UNICODE));

$params = $answer['params'];

$acc = intval($params['account']);

switch ($answer['method']) {
    case 'check':
        $isUser = $qb->createQueryBuilder('orders')->selectSql()->where('id = ' . $acc)->executeQuery()->getSingleResult();
        if(!empty($isUser))
            $result = ['result' => ['message' => 'Verification completed successfully. User found in database']];
        else
            $result = ['error' => ['message' => 'User was not found in the system Appi']];
        break;
    case 'pay':

        //if(round($params['orderSum']) >= round($params['payerSum'])) {

        $signature = $user->getSignature($answer['method'], $params, '3930b5cc60c8bc46869a6536f2d4d0f3');

        if($signature == $params['signature']) {

            $userInfo = $user->getAccountInfo($acc[1]);

            $donateMoney = round($params['orderSum']);

            /*if($donateMoney >= 10000)
                $donateMoney = round($donateMoney * 2.3);
            else if($donateMoney >= 5000)
                $donateMoney = $donateMoney * 2;*/

            $donateMoney = $donateMoney * 1;


            $resultSql = $qb
                ->createQueryBuilder('accounts')
                ->updateSql(['money_donate'], [round($donateMoney + $userInfo['money_donate'])])
                ->where('id = \'' . $acc[1] . '\'')
                ->orWhere('login = \'' . $acc[1] . '\'')
                ->executeQuery()
                ->getResult()
            ;

            $resultSqlLog = $qb
                ->createQueryBuilder('log_donate_payment')
                ->insertSql(
                    ['user_id', 'money_s', 'money_p', 'money_f'],
                    [$acc[1], $donateMoney, round($params['profit']), round($donateMoney + $userInfo['money_donate'])]
                )
                ->executeQuery()
                ->getResult()
            ;

            if ($resultSql)
                $result = ['result' => ['message' => 'Payment successfully completed']];
            else
                $result = ['error' => ['message' => 'There was an error performing the payment']];
        }
        else
            $result = ['error' => ['message' => 'Hacking attempt']];
        /*}
        else
            $result = ['error' => ['message' => 'Amount does not match. Order: ' . round($params['orderSum']) . ', Payer: ' . round($params['payerSum'])]];*/
        break;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);