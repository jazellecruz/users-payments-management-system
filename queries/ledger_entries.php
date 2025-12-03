<?php 

function addNewLedgerEntry($conn, $ledgerData) {
    $sql = "INSERT INTO ledger_entries 
            (public_entry_id, entry_type, transaction_id, transaction_type, payment_type, entry_category, payment_id, withdrawal_id, payout_id, amount, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssisssiiids",
        $ledgerData['public_entry_id'],
        $ledgerData['entry_type'],
        $ledgerData['transaction_id'],
        $ledgerData['transaction_type'],
        $ledgerData['payment_type'],
        $ledgerData['entry_category'],
        $ledgerData['payment_id'],
        $ledgerData['withdrawal_id'],
        $ledgerData['payout_id'],
        $ledgerData['amount'],
        $ledgerData['notes']
    );

    return $stmt->execute();
}


?>