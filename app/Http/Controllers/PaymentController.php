<?php

namespace App\Http\Controllers;

use App\Models\Participate;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Tymon\JWTAuth\Facades\JWTAuth;

class PaymentController extends Controller
{
    
    public function checkout(){ //Request $request,string $id
        //$user = JWTAuth::parseToken()->authenticate();
        $user_id = 1;       //$user->id;
        $oid = 1;           //$id;
        $participate = Participate::where('olympiad_id', $oid)
            ->where('user_id', $user_id)
            ->first(); // or findOrFail() if it's guaranteed to exist

        
        /*based on the user like student or incharge create payload*/
        $price =10;                     //$participate->total_amount;
        $participateid = 1;             // $participate->id;
        $olympiadName= "Your Beautiful Olympiad";
        $stripeSecretKey = config('services.stripe.secret_key');
        
        Stripe::setApiKey($stripeSecretKey);

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                    'price_data' => [
                    'currency' => 'inr',
                    'product_data' => [
                        'name' => $olympiadName,
                    ],
                    'unit_amount' => $price * 100,
                    ],
                    'quantity' => 1,
                    
                    
            ]],
            'mode' => 'payment',
            'success_url' => 'http://127.0.0.1:8000/success'."?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => 'http://127.0.0.1:8000/cancel',
            'customer_email' => 'auto', 
            'billing_address_collection' => 'auto', 
            'shipping_address_collection' => [
                'allowed_countries' => ['US', 'CA', 'GB', 'AU'], 
            ],
        ]);
        $order = Payment::create([
            'amount'=>$price,
            'customer_id'=>$user_id,
            'status'=>'pending',
            'olympiad_id'=>$oid,
            'session_id'=>$checkout_session->id
        ]);

        return redirect($checkout_session->url);
    }

    public function success(Request $request){
        $session = $request->get('session_id');
        $stripeSecretKey = config('services.stripe.secret_key');
        
        Stripe::setApiKey($stripeSecretKey);
        $sessionn= \Stripe\Checkout\Session::retrieve($session);
        if(!$sessionn){
            return response()->json(['status'=>'failure','message'=>'success method fail, session id not found']);
        }
        
    }

}
