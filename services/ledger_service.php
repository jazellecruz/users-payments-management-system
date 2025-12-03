<?php 

require_once __DIR__ . '/../utils/utils.php';
require_once __DIR__ . '/../db/db_conn.php';

require_once __DIR__ . '/../enums/Prefix.php';
require_once __DIR__ . '/../enums/EntryCategory.php';
require_once __DIR__ . '/../enums/PaymentType.php';
require_once __DIR__ . '/../enums/LedgerEntryType.php';

require_once __DIR__ . '/../queries/ledger_entries.php';
require_once __DIR__ . '/../config/config.php';

/**
 * @param mysqli $conn - to allow db transactions (preferably)
 * @param array $entryData {
 *    @type int entry_type - 'debit' or 'credit'
 *    @type int transaction_id - associated internal transaction id 
 *    @type string transaction_type - 'payment', 'payout', 'withdrawal', 'refund'
 *    @type string payment_type - 'full_payment','down_payment','balance_payment' - for payments
 *    @type string entry_category - 'tax_pay','platform_fee','net_pay','total_amount','transfer_fee','withdrawal_amount' - category of the ledger entry
 *    @type int payment_id - associated internal payment id (optional)
 *    @type int withdrawal_id - associated internal withdrawal id (optional)
 *    @type int payout_id - associated internal payout id (optional)
 *    @type int amount - amount for the ledger entry
 *    @type string notes - optional notes for the ledger entry
 * }
 */
function createLedgerEntry($conn, $entryData) {

    $entryType = $entryData['entry_type'];
    $transactionId = $entryData['transaction_id'];
    $transactionType = $entryData['transaction_type'];
    $paymentType = $entryData['payment_type'] ? PaymentType::fromString($entryData['payment_type'])->value : null;
    $entryCategory = EntryCategory::fromString($entryData['entry_category'])->value;
    $paymentId = isset($entryData['payment_id']) ? $entryData['payment_id'] : null;
    $withdrawalId = isset($entryData['withdrawal_id']) ? $entryData['withdrawal_id'] : null;
    $payoutId = isset($entryData['payout_id']) ? $entryData['payout_id'] : null;
    $amount = $entryData['amount'];
    $notes = $entryData['notes'] ?? null;

    $publicEntryId = Prefix::LEDGER_ENTRY->value . '-' . strtoupper(generateNanoId(12));

    // prepare data for insertion
    $ledgerData = [
        'public_entry_id' => $publicEntryId,
        'entry_type' => $entryType,
        'transaction_id' => $transactionId,
        'transaction_type' => $transactionType,
        'payment_type' => $paymentType,
        'entry_category' => $entryCategory,
        'payment_id' => $paymentId,
        'withdrawal_id' => $withdrawalId,
        'payout_id' => $payoutId,
        'amount' => $amount,
        'notes' => $notes
    ];
    $res = addNewLedgerEntry($conn, $ledgerData);
    return $res;
}

function createLedgerEntries($conn, $entriesArray) {
    $allSuccess = true;
    foreach ($entriesArray as $entryData) {
        $res = createLedgerEntry($conn, $entryData);
        if (!$res) {
            $allSuccess = false;
            break;
        }
    }
    return $allSuccess;
}

?>