<?php


namespace App\DTO;


class FondyPaymentDTO
{
    public string $masked_card;
    public string $response_signature_string;
    public string $response_status;
    public string $reversal_amount;
    public string $settlement_amount;
    public string $actual_amount;
    public string $order_status;
    public string $response_description;
    public string $currency;
    public string $actual_currency;
    public string $order_id;
    public string $signature;
    public string $amount;
    public string $sender_cell_phone;
    public string $sender_email;
    public string $merchant_data;
    public string $verification_status;
}
