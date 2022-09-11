<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\View;
use App\Exceptions\FileUploadingException;
use App\Models\Transactions;

class TransactionController
{
    public function index(): View
    {

        $transactions = new Transactions();
        $table = $transactions->getTransactions();

        $date = [];
        $amount = [];
        $check = [];
        $color = [];
        $description = [];

        foreach($table as $transaction) {
            array_push($date, $transaction['Date']);
            array_push($amount, $transaction['Amount']);
            array_push($check, $transaction['Check_']);
            array_push($color, $transaction['Color']);
            array_push($description, $transaction['Description']);
        }

        $income = array_sum(array_map(function($string) {
                    return str_replace(['$', ','], ['', ''], $string);
                },array_filter($amount, fn($string) => $string[0] !== '-')));

        $totalIncome = '$' . $this->addCommas($income);

        $expense = array_sum(array_map(function($string) {
                    return str_replace(['$', ','], ['', ''], $string);
                },array_filter($amount, fn($string) => $string[0] == '-')));

        $totalExpense = substr_replace($this->addCommas($expense), '$', 1, 0);

        $total = (float) $income + (float) $expense;

        if(((string) $total)[0] ===  '-') {
            $netTotal = substr_replace($this->addCommas($total), '$', 1, 0);
        } else {
            $netTotal =  '$' . $this->addCommas($total);
        }

        return View::make('transactions', [
            'date'          => $date,
            'amount'        => $amount,
            'check'         => $check, 
            'color'         => $color, 
            'description'   => $description,
            'totalIncome'   => $totalIncome,
            'totalExpense'  => $totalExpense,
            'netTotal'      => $netTotal
        ]);
    }


    public function submit(): View
    {
        return View::make('submit');
    }

    public function upload(): void
    {
        try{
            $filePath = STORAGE_PATH . '/' . $_FILES['table']['name'];
            move_uploaded_file($_FILES['table']['tmp_name'], $filePath);

            if(substr($_FILES['table']['name'], -4, 4) !== '.csv' ) {
                throw new FileUploadingException();
            }

            $table = $this->fetchData($filePath);
            $this->editDate($table);

            $transactions = new \App\Models\Transactions();
            $transactions->upload($table);

        } catch(FileUploadingException $e) {
            echo $e->getMessage();
            exit();
        }
         
        header('Location: /transactions');
        exit();
    }

    private function fetchData(string $filePath): array {
        $table = ['Date' => [], 'Check #' => [], 'Description' => [], 'Amount' => [], 'Color' => []];
        $result = [];
    
         try{
            $file = fopen($filePath, 'r');

            while (($line = fgetcsv($file)) !== false) {
                array_push($result, $line);
            }

            for($j = 1; $j < count($result); $j++) {
                    array_push($table['Date'], $result[$j][0]);
                    array_push($table['Check #'], $result[$j][1]);
                    array_push($table['Description'], $result[$j][2]);
                    array_push($table['Amount'], $result[$j][3]);
                    if($result[$j][3][0] === '-' ) {
                        array_push($table['Color'], 'red');
                    }
                    else {
                        array_push($table['Color'], 'green');
                    }
            }
            $result = [];
            return $table;
        } catch(\Exception) {
            throw new FileUploadingException();
        }
    }

    private function editDate(array &$table): void {
        for($i = 0; $i < count($table['Date']); $i++) {
            $date = date_parse($table['Date'][$i]);
            $table['Date'][$i] = date('M d, Y', mktime(0, 0, 0, $date['month'], $date['day'], $date['year']));
        }
    }

    private function addCommas(int|float|string $amount): string {
        $str = (string)(int)$amount;
        $result = (string)$amount;
        for($i = strlen($str) - 3; $i > 0; $i = $i - 3) { 
            $result = substr_replace($result, ',', $i, 0);
        }
        return $result;
    }

    
}