<?php
namespace App\Services\Payment;
use Auth;
use Exception;
use Log;
class FlutterwavePayment implements PaymentInterface {
    private string $secretKey;
    private string $currencyCode;
    public function __construct($secretKey, $currencyCode) {
        $this->secretKey = $secretKey;
        $this->currencyCode = $currencyCode;
    }
    /**
     * Create a payment intent using Flutterwave
     * @param $amount
     * @param $customMetaData
     * @return array
     * @throws Exception
     */
    public function createPaymentIntent($amount, $customMetaData): array {
        try {
            $amount = $this->minimumAmountValidation($this->currencyCode, $amount);
            $tx_ref = uniqid();
            $email = Auth::user()->email ?? 'no-email@domain.com';
            $paymentTransactionId = $customMetaData['payment_transaction_id'] ?? uniqid();
            // Create cURL request for payment initialization
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'tx_ref' => $tx_ref,
                    'amount' => $amount,
                    'currency' => $this->currencyCode,
                    'payment_type' => 'mobilemoneyghana',
                    'customer' => [
                        'email' => "customer@example.com",
                        'phonenumber' => "08012345678",
                        'name' => "John Doe"
                    ],
                    'customer_email' => $email,
                    'redirect_url' => 'https://webhook.site/40abb70c-62f8-4b51-b859-2b3938874817', // Corrected redirect_url
                    'order_id' => $paymentTransactionId
                ]),
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $this->secretKey,
                    'Content-Type: application/json',
                ]
            ]);
            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);
            if ($error) {
                throw new Exception("cURL error: " . $error);
            }
            $responseArray = json_decode($response, true);
            // Log the raw response for debugging
            Log::info('Flutterwave Create Payment Intent Response: ' . $response);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON response: ' . json_last_error_msg());
                throw new Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            if ($responseArray['status'] !== 'success') {
                throw new Exception("Error creating payment intent: " . $responseArray['message']);
            }
            return $responseArray; // Return raw response
        } catch (Exception $e) {
            Log::error('Error in createPaymentIntent: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Create and format a payment intent
     * @param $amount
     * @param $customMetaData
     * @return array
     */
    public function createAndFormatPaymentIntent($amount, $customMetaData): array {
        $paymentIntent = $this->createPaymentIntent($amount, $customMetaData);
        return $this->format($paymentIntent);
    }
    /**
     * Retrieve and format a payment intent
     * @param $paymentId
     * @return array
     * @throws Exception
     */
    public function retrievePaymentIntent($paymentId): array {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$paymentId}/verify",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $this->secretKey,
                    'Content-Type: application/json'
                ],
            ]);
            // Execute cURL request and fetch response
            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);
            // Check for cURL errors
            if ($error) {
                throw new Exception("cURL error: " . $error);
            }
            // Log the raw response for debugging
            Log::info('Flutterwave Raw API Response: ' . $response);
            // Check if response is empty or malformed
            if (empty($response)) {
                throw new Exception("Empty response from Flutterwave API");
            }
            // Attempt to decode the JSON response
            $responseArray = json_decode($response, true);
            // Handle invalid JSON responses
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Log the invalid JSON error and raw response
                Log::error('Invalid JSON response: ' . json_last_error_msg());
                throw new Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            // Check if the response status is 'success'
            if (!isset($responseArray['status']) || $responseArray['status'] !== 'success') {
                // Log the error message from Flutterwave
                Log::error('Error retrieving payment intent: ' . json_encode($responseArray));
                throw new Exception("Error retrieving payment intent: " . ($responseArray['message'] ?? 'Unknown error'));
            }
            // Return formatted response data
            return $this->format($responseArray['data']);
        } catch (Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error in retrievePaymentIntent: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Format the payment intent response
     * @param $paymentIntent
     * @return array
     */
    public function format($paymentIntent): array {
        return $this->formatPaymentIntent(
            $paymentIntent['id'] ?? 'N/A',
            $paymentIntent['amount'] ?? 0,
            $paymentIntent['currency'] ?? 'N/A',
            $paymentIntent['status'] ?? 'unknown',
            $paymentIntent['meta'] ?? [],
            $paymentIntent
        );
    }
    /**
     * Format payment intent details
     * @param $id
     * @param $amount
     * @param $currency
     * @param $status
     * @param $metadata
     * @param $paymentIntent
     * @return array
     */
    public function formatPaymentIntent($id, $amount, $currency, $status, $metadata, $paymentIntent): array {
        return [
            'id'                       => $id ?? 'N/A',
            'amount'                   => $amount ?? 0,
            'currency'                 => $currency ?? 'N/A',
            'metadata'                 => $metadata,
            'status'                   => match ($status) {
                'canceled'   => 'failed',
                'successful' => 'succeed',
                'pending'    => 'pending',
                default      => 'unknown',
            },
            'payment_gateway_response' => $paymentIntent
        ];
    }
    /**
     * Validate the minimum amount based on currency
     * @param $currency
     * @param $amount
     * @return float|int
     */
    public function minimumAmountValidation($currency, $amount) {
        return max(1, $amount);
    }
}



<?php

namespace App\Services\Payment;

use Throwable;
use RuntimeException;
use Illuminate\Support\Facades\Log;

class FlutterwavePayment implements PaymentInterface
{
    private string $currencyCode;

    /**
     * FlutterwavePayment constructor.
     * @param string $currencyCode
     */
    public function __construct(string $currencyCode, $secret_key)
    {
        $this->currencyCode = $currencyCode;
        $this->secretKey = $secret_key;
    }

    /**
     * @param mixed $amount
     * @param mixed $customMetaData
     * @return array
     * @throws RuntimeException
     */
    public function createPaymentIntent($amount, $customMetaData)
    {
        try {
            $finalAmount = $amount * 100;
            $tx_ref = uniqid();
            $data = [
                "tx_ref" => $tx_ref,
                "amount" => $finalAmount,
                "currency" => $this->currencyCode,
                "redirect_url" => route('flutterwave.success'),
                "metadata" => $customMetaData
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $this->secretKey,
                    'Content-Type: application/json',
                ];
            ]);
            $response = curl_exec($curl);
        
            $error = curl_error($curl);
          
            curl_close($curl);
            if ($error) {
                throw new RuntimeException("cURL error: " . $error);
            }
            $responseArray = json_decode($response, true);

            // Log the raw response for debugging
            Log::info('Flutterwave Create Payment Intent Response: ' . $response);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON response: ' . json_last_error_msg());
                throw new RuntimeException('Invalid JSON response: ' . json_last_error_msg());
            }
            if ($responseArray['status'] !== 'success') {
                throw new RuntimeException("Error creating payment intent: " . $responseArray['message']);
            }
            return $responseArray; // Return raw response

        } catch (Throwable $e) {
            Log::error('Error in createPaymentIntent: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data ?? null,
            ]);
            throw new RuntimeException("Error creating payment intent: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param mixed $amount
     * @param mixed $customMetaData
     * @return array
     */
    public function createAndFormatPaymentIntent($amount, $customMetaData): array
    {
        $response = $this->createPaymentIntent($amount, $customMetaData);
        return $this->format($response, $amount, $this->currencyCode, $customMetaData);
    }

    /**
     * @param mixed $paymentId
     * @return array
     * @throws RuntimeException
     */
    public function retrievePaymentIntent($paymentId): array {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$paymentId}/verify",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $this->header()
            ]);
            // Execute cURL request and fetch response
            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);
            // Check for cURL errors
            if ($error) {
                throw new RuntimeException("cURL error: " . $error);
            }
            // Log the raw response for debugging
            Log::info('Flutterwave Raw API Response: ' . $response);
            // Check if response is empty or malformed
            if (empty($response)) {
                throw new RuntimeException("Empty response from Flutterwave API");
            }
            // Attempt to decode the JSON response
            $responseArray = json_decode($response, true);
            // Handle invalid JSON responses
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Log the invalid JSON error and raw response
                Log::error('Invalid JSON response: ' . json_last_error_msg());
                throw new RuntimeException('Invalid JSON response: ' . json_last_error_msg());
            }
            // Check if the response status is 'success'
            if (!isset($responseArray['status']) || $responseArray['status'] !== 'success') {
                // Log the error message from Flutterwave
                Log::error('Error retrieving payment intent: ' . json_encode($responseArray));
                throw new RuntimeException("Error retrieving payment intent: " . ($responseArray['message'] ?? 'Unknown error'));
            }
            // Return formatted response data
            return $this->format($responseArray['data']);
        } catch (RuntimeException $e) {
            // Log the exception for debugging purposes
            Log::error('Error in retrievePaymentIntent: ' . $e->getMessage());
            throw $e;
        }
    }
    // ... rest of the class methods remain the same ...

    /**
     * Generate a unique reference for the payment
     *
     * @return string
     */
    public function minimumAmountValidation($currency, $amount)
    {
        $minimumAmounts = [
            'USD' => 1.00,
            'NGN' => 100.00,
            'EUR' => 1.00,
            // Add more currencies and minimum amounts as necessary
        ];

        if (isset($minimumAmounts[$currency])) {
            return $amount >= $minimumAmounts[$currency];
        }

        return false;
    }
    public function formatPaymentIntent($id, $amount, $currency, $status, $metadata, $paymentIntent): array {
        return [
            'id'                       => $id,
            'amount'                   => $amount,
            'currency'                 => $currency,
            'metadata'                 => $metadata,
            'status'                   => match ($status) {
                "abandoned" => "failed",
                "succeed" => "succeed",
                default => $status ?? true
            },
            'payment_gateway_response' => $paymentIntent
        ];
    }
    public function format($paymentIntent, $amount, $currencyCode, $metadata) {
        return $this->formatPaymentIntent($paymentIntent['data']['reference'], $amount, $currencyCode, $paymentIntent['status'], $metadata, $paymentIntent);
    }
    public function header()
    {
        $header =[
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/json',
        ];
        return $header;
    }
}


'customer' => [
    'student_id' => $customMetaData['student_id'],
    'parent_id'  => $customMetaData['parent_id'],
    'class_id'  => $customMetaData['class_id'],
    "email" => $customMetaData['email'],
    "session_year_id" => $customMetaData['session_year_id'],
    "payment_transaction_id" => $customMetaData['payment_transaction_id'],
    "is_fully_paid" => $customMetaData['is_fully_paid'],
    "type_of_fee" => $customMetaData['type_of_fee'],
    "is_due_charges" => $customMetaData['is_due_charges'],
    "due_charges" => $customMetaData['due_charges'],
    "optional_fees_paid" => $customMetaData['optional_fees_paid'],
    "installment_fees_paid" => $customMetaData['installment_fees_paid']
],