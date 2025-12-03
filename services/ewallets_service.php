<?php

require_once __DIR__ . '/../utils/utils.php';
require_once __DIR__ . '/../db/db_conn.php';
require_once __DIR__ . '/../ex/CustomException.php';
require_once __DIR__ . '/../enums/ExceptionTypes.php';

function createEwallet($ewalletData) {
    $conn = getDBConnection();

    try {
        $isEwalletValid = isEWalletValid(
            $ewalletData['external_wallet_provider'], 
            $ewalletData['external_wallet_number']
        );

        if(!$isEwalletValid) {
            throw new CustomException(
                null,
                ExceptionTypes::INVALID_EWALLET_ACCOUNT,
                ExceptionTypes::INVALID_EWALLET_ACCOUNT->toErrorData()['statusCode'], 
                ExceptionTypes::INVALID_EWALLET_ACCOUNT->toErrorData()['message']
            );
        }

        // samole format: EWLT-9BCD4FG5IJKL
        $newEwalletPubId = Prefix::EWALLET->value . '-' . strtoupper(generateNanoId(12));

        $newEwalletData = [
            'public_ewallet_id' => $newEwalletPubId,
            'external_wallet_provider' => $ewalletData['external_wallet_provider'],
            'external_wallet_number' => $ewalletData['external_wallet_number'],
            'owner_type' => $ewalletData['owner_type'],
            'owner_id' => $ewalletData['owner_id']
        ];

        $res = addNewEwallet($conn, $newEwalletData);

        if(!$res) {
            throw new CustomException(
                null,
                ExceptionTypes::INTERNAL_SERVER_ERROR,
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'],
                ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
            );
        }

        $newId = $conn->insert_id;
        $newEwallet = getEwalletById($conn, $newId);

        // INTEGRATION REFACTOR - SERVICE RESPONSE: Return what is needed to return from the service
        return $newEwallet;
    } catch (Exception $e) {
        // just rethrow if error was already wrapped and thrown from above
        if($e instanceof CustomException) throw $e;
        
        throw new CustomException(
            null,
            ExceptionTypes::INTERNAL_SERVER_ERROR,
            ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['statusCode'],
            ExceptionTypes::INTERNAL_SERVER_ERROR->toErrorData()['message']
        );
    }
}

?>