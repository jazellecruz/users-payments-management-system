<?php 

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/external_api.php';
require_once __DIR__ . '/../utils/utils.php';
require_once __DIR__ . '/../enums/Prefix.php';
require_once __DIR__ . '/../queries/platform_accounts.php';
require_once __DIR__ . '/../db/db_conn.php';

use \GuzzleHttp\Client;

function createNewPlatformAccount($accountData) {
    $conn = getDBConnection();

    try {
        $httpClient = new Client([
            'base_uri' => XENDIT_BASE_URL,
            'auth' => [XENDIT_API_KEY_TOKEN, ''], // store api key in username field
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $jsonBody = json_encode([
            "email" => $accountData['email'],
            "type" => "OWNED",
            "public_profile" => [
                "business_name" => $accountData['business_name'],
            ]
            ]);

        $res = $httpClient->post(XENDIT_ACCOUNTS_ENDPOINT, [
            'body' => $jsonBody
        ]);
        

        $responseData = json_decode($res->getBody(), true);
        $subAcctId = $responseData['id'];
        $subAccEmail = $responseData['email'];
        $subAccBusName = $responseData['public_profile']['business_name'];
                                 
        // sample format: ACC-SA-9BCD4FG5IJKL
        $publicPlatformAccountId = Prefix::SUB_ACCOUNT->value . '-' . strtoupper(generateNanoId(12));

        $newAccData = [
            'public_platform_account_id' => $publicPlatformAccountId,
            'platform_account_name' => $subAccBusName,
            'platform_account_email' => $subAccEmail,
            'platform_account_type' => $accountData['account_type'],
            'external_account_id' => $subAcctId,
            'owner_type' => $accountData['user_type'],
            'owner_id' => $accountData['user_id']
        ];

        $newAccRes = addNewPlatformAccount($conn, $newAccData);

        if (!$newAccRes) {
            throw new Exception("Failed to create new platform account in database.");
        }
        // INTEGRATION REFACTOR - RESPONSE : Return what is needed to return 
    } catch(Exception $e) {
        // INTEGRATION REFACTOR - ERROR HANDLING: Proper error handling
        echo $e->getMessage();
        exit;
    }
}

function getBalanceOfAccount($conn, $platformAccountId) {
    try{
        $balance = null;
        
        $accBalance = getAccBalanceByPlatformAcctId($conn, $platformAccountId);
        
        if(!empty($accBalance)) {
            $balance = convertStrToFloat($accBalance['current_balance']);
        }
        return $balance;
    } catch(Exception $e) {
        throw $e;
    }
}

// @param deductionData array {
//     @type array deduction {
//         @type string deduction_for
//         @type float amount_to_deduct
//     }
//     @type array user {
//         @type int user_id
//     }
//     @type array platform_account {
//         @type int platform_account_id
//     }
// }
function addPendingDeductionForPlatformAcc($conn, $deductionData) {
    try {
        $platformAccountId = $deductionData['platform_account']['platform_account_id'];
        $userId = $deductionData['user']['user_id'];
        $amountToDeduct = $deductionData['deduction']['amount_to_deduct'];
        $deductionFor = $deductionData['deduction']['deduction_for'];
        $deductionStatus = TransactionStatus::PENDING->value;

        $isDeductionAdded = addNewPendingDeduction($conn, [
            'platform_account_id' => $platformAccountId,
            'user_id' => $userId,
            'deduction_for' => $deductionFor,
            'amount_to_deduct' => $amountToDeduct,
            'deduction_status' => $deductionStatus
        ]);

        return $isDeductionAdded;
    } catch(Exception $e) {
        throw $e;
    }
}

function addToAccountBalance($conn, $platformAccountId, $amountToAdd) {
    try {
        $currentBalance = getBalanceOfAccount($conn, $platformAccountId);
        $newBalance = $currentBalance + $amountToAdd;
        $isBalanceUpdated = updatePlatformAccountBalance($conn, $platformAccountId, $newBalance);
        return $isBalanceUpdated;
    } catch(Exception $e) {
        throw $e;
    }
}

function deductFromAccountBalance($conn, $platformAccountId, $amountToDeduct) {
    try {
        $currentBalance = getBalanceOfAccount($conn, $platformAccountId);
        // TO DO: Handle insufficient balance case
        $newBalance = $currentBalance - $amountToDeduct;
        $isBalanceUpdated = updatePlatformAccountBalance($conn, $platformAccountId, $newBalance);
        return $isBalanceUpdated;
    } catch(Exception $e) {
        throw $e;
    }
}


?>