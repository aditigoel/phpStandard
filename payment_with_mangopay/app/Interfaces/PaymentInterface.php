<?php

namespace App\Interfaces;

use App\Http\Requests\BankAccountRequest;
use Illuminate\Http\Request;

interface PaymentInterface
{
    public function createAccount(Request $request);

    public function createWallet(Request $request);

    public function viewWallet(Request $request);

    public function addCard(Request $request);

    public function getUserCards(Request $request);

    public function createBank(BankAccountRequest $request);

    public function deleteBankAccount(Request $request);

    public function getUserBankAccount(Request $request);

    public function createDirectPayIn(Request $request);

    public function releasePayment(Request $request);
}
