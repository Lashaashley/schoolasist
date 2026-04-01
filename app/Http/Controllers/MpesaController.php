<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\Transaction;


class MpesaController extends Controller
{
    public function showPayFeesForm() {
        $students = Student::all();
        return view('mpesa.pay', compact('students'));
    }

    // Generate M-PESA STK password
    private function lipaNaMpesaPassword()
    {
        $timestamp = Carbon::now()->format('YmdHis'); // Correct format
        $passKey = "32635a02980be25306015bf76cf80b1facf44ad1a49235ce7fd8db346fed55f6";
        $businessShortCode = "6786779";

        return base64_encode($businessShortCode.$passKey.$timestamp);
    }

    
    private function newAccessToken()
    {
        $consumer_key = "1zTeRFjxMlnAUlY4aK0feD4mRFd35oarCQh3VXE2F57KpIEi";
        $consumer_secret = "YqJ98oN9HpQq2bUx54GriUA1NiTuBMNEgIKXsmA9z2qXA2Ar7oZysqAUfS1x5Jl2";
        $credentials = base64_encode($consumer_key.":".$consumer_secret);
        $url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

        $curl = curl_init();
             curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic ".$credentials,
                "Content-Type: application/json"
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response);
        return $result->access_token ?? null;
    }

    // Initiate STK push

    public function stkPush(Request $request)
{
    $student_info = preg_split('/[-#]/', $request->student_info);
    $admno = $student_info[0] ?? null;
    $stream = $student_info[1] ?? null;

    $amount = $request->amount;
    $phone = formatPhoneNumber($request->phone); // new
    //$phone = '254'.ltrim($request->phone, '0'); // format correctly
    $accountReference = "20810";
    $schoolAcc = $accountReference.'#'.$admno.'#'.$stream;

    $payload = [
        'BusinessShortCode' => "6786779",
        'Password' => $this->lipaNaMpesaPassword(),
        'Timestamp' => Carbon::now()->format('YmdHis'),
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => "222111",
        'PhoneNumber' => $phone,
        'CallBackURL' => 'https://summitrobotics.live/api/stk/push/callback/url',
        'AccountReference' => $accountReference,
        'TransactionDesc' => "School Fees Payment"
    ];

    $curl = curl_init('https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->newAccessToken()
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $response_data = json_decode($response, true);

    // Log the full STK response
    \Log::info('M-PESA STK Push Response:', $response_data);

    $checkoutRequestID = $response_data['CheckoutRequestID'] ?? null;

    Transaction::create([
        'admno' => $admno,
        'stream' => $stream,
        'school_acc' => $schoolAcc,
        'account_reference' => $accountReference,
        'phone' => $phone,
        'amount' => $amount,
        'transaction_type' => 'CustomerPayBillOnline',
        'checkout_request_id' => $checkoutRequestID,
        'mpesa_receipt' => null,
        'result_code' => null,
        'result_desc' => null,
        'response' => json_encode($response_data)
    ]);

    return redirect()->route('pay_fees')->with('alert','STK Push initiated. Check your phone.');
}
   
    // Callback from M-PESA
    public function stkCallback(Request $request)
    {
        $callback = $request->input('Body.stkCallback', null);

        if ($callback) {
            $checkoutRequestID = $callback['CheckoutRequestID'] ?? null;
            $resultCode = $callback['ResultCode'] ?? null;
            $resultDesc = $callback['ResultDesc'] ?? null;

            $items = $callback['CallbackMetadata']['Item'] ?? [];
            $amount = $items[0]['Value'] ?? null;
            $phoneNumber = $items[1]['Value'] ?? null;
            $mpesaReceipt = $items[3]['Value'] ?? null;

            $transaction = Transaction::where('checkout_request_id', $checkoutRequestID)->first();
            if ($transaction) {
                $transaction->update([
                    'mpesa_receipt' => $mpesaReceipt,
                    'result_code' => $resultCode,
                    'result_desc' => $resultDesc,
                    'response' => json_encode($request->all())
                ]);
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }
}