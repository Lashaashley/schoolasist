<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Student;

class Mpesa1Controller extends Controller
{

    public function showPayFeesForm() {
    $students = Student::all(); // or filter by class/stream
    return view('mpesa.pay', compact('students'));
}

      public function lipaNaMpesaPassword()
    {
        //timestamp
        $timestamp = Carbon::rawParse('now')->format('YmdHms');
        //passkey
        $passKey ="32635a02980be25306015bf76cf80b1facf44ad1a49235ce7fd8db346fed55f6";
        $businessShortCOde =6786779;
        //generate password
        $mpesaPassword = base64_encode($businessShortCOde.$passKey.$timestamp);

        return $mpesaPassword;
       
    }
   

    public function newAccessToken()
    {
        $consumer_key="1zTeRFjxMlnAUlY4aK0feD4mRFd35oarCQh3VXE2F57KpIEi";
        $consumer_secret="YqJ98oN9HpQq2bUx54GriUA1NiTuBMNEgIKXsmA9z2qXA2Ar7oZysqAUfS1x5Jl2";
        $credentials = base64_encode($consumer_key.":".$consumer_secret);
        $url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic ".$credentials,"Content-Type:application/json"));
        curl_setopt($curl, CURLOPT_HEADER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $access_token=json_decode($curl_response);
        curl_close($curl);
       
        return $access_token->access_token;  
       
    }



    public function stkPush(Request $request)
    {  
     
            $user = $request->user;
            $amount = 1;
            $phone =  $request->phone;
            $formatedPhone = substr($phone, 1);
            $code = "254";
            $phoneNumber =  $phone;

     
       


        $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $curl_post_data = [
            'BusinessShortCode' =>6786779,
            'Password' => $this->lipaNaMpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phoneNumber,
            'PartyB' => 222111,
            'PhoneNumber' => $phoneNumber,
            
            'CallBackURL' => 'https://summitrobotics.live/api/stk/push/callback/url',
            'AccountReference' => "20810",
            'TransactionDesc' => "School Fees Payment"
        ];


        $data_string = json_encode($curl_post_data);


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->newAccessToken()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);



  MpesaTransaction::create([

             'admno' => '$admno',
            'phone' => $phone,
            'amount' => $amount,
            'checkout_request_id' => $checkoutRequestID,
            'mpesa_receipt' => $mpesaReceipt,
            'status' => $resultCode == 0 ? 1 : 0,

         ]);

        return redirect()->route('pay-fees')->with('alert','STK initiated');




       
     }  


     public function stkCallback(Request $request)
{
    // Log the callback data for debugging
    \Log::info('M-PESA STK Callback:', $request->all());

    // Example: You can extract transaction details
    $callback = $request->Body['stkCallback'] ?? null;

    if ($callback) {
        $merchantRequestID = $callback['MerchantRequestID'] ?? null;
        $checkoutRequestID = $callback['CheckoutRequestID'] ?? null;
        $resultCode = $callback['ResultCode'] ?? null;
        $resultDesc = $callback['ResultDesc'] ?? null;

        $amount = $callback['CallbackMetadata']['Item'][0]['Value'] ?? null;
        $phoneNumber = $callback['CallbackMetadata']['Item'][1]['Value'] ?? null;
           
        

        // Save to database if you have a table for M-PESA transactions
      

        \Log::info('Transaction recorded successfully');
    }

    // Return this JSON so Safaricom knows callback was received
    return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
}

}
