<?php

namespace App\Http\Controllers;

use App\Models\Participate;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Tymon\JWTAuth\Facades\JWTAuth;

class PaymentController extends Controller
{
    
    public function checkout(Request $request,string $id){ //
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;
        $oid = $id;
        $participate = Participate::with('participantOlympiad')->where('olympiad_id', $oid)
            ->where('user_id', $user_id)
            ->firstOrFail(); 

        
        /*based on the user like student or incharge create payload*/
        $price =$participate->total_amount;
        $participateid =$participate->id;
        $olympiadName= $participate->participantOlympiad->name;
        $stripeSecretKey = config('services.stripe.secret_key');
        $frontendurl= config('services.frontend_url.frontend_url_r');


        return response()->json(['price'=>$price,'participateid'=>$participateid,'olypiad name'=>$olympiadName,'url'=>$frontendurl,'data'=>$participate]);
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
            'success_url' => $frontendurl.'/success'."?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => $frontendurl.'/cancel',
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

    public function cencel(Request $request){
        return response()->json(['status'=>'cancel','message'=>'payment process cancelled']);
    }

}
