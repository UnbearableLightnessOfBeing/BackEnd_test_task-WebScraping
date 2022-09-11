<?php

declare(strict_types = 1);

namespace App\Models;

use App\Model;

class Transactions extends Model {

    public function upload(array $table): void {

        $this->db->prepare('truncate table transactions;')->execute();

        $query = 'update into transactions (Date, Description, Amount, Check_, Color) values (?, ?, ?, ?, ?);';

        // $query = 'select * from transactions';
        $stmt = $this->db->prepare($query);

        
        for($i = 0; $i < count($table['Date']); $i ++) {
            $stmt->execute([$table['Date'][$i], $table['Description'][$i], $table['Amount'][$i], $table['Check #'][$i], $table['Color'][$i]]);
        }
        
    }

    public function getTransactions(): array {
        $stmt = $this->db->prepare('select * from transactions;');

        $stmt->execute();

        $table = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $table;
    }
}
