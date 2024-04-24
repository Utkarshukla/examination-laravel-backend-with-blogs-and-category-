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
       // $user = JWTAuth::parseToken()->authenticate();
        $user_id =2;// $user->id;
       // $user_email=$user->email;
        $oid =1; //$id;
        // $participate = Participate::with('participantOlympiad')->where('olympiad_id', $oid)
        //     ->where('user_id', $user_id)
        //     ->firstOrFail(); 

        
        /*based on the user like student or incharge create payload*/
        $price = 10;//$participate->total_amount;
        $participateid = 1;//$participate->id;
        $olympiadName= 'abcd';//$participate->participantOlympiad->name;
        $stripeSecretKey = config('services.stripe.secret_key');
        $frontendurl= config('services.frontend_url.frontend_url_r');

        //return response()->json(['price'=>$price,'olypiad name'=>$olympiadName,'data'=>$frontendurl]);
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
            'success_url' =>$frontendurl."/contact/?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => $frontendurl."/cancel",
            //'customer_email' =>$user_email , 
            'billing_address_collection' => 'auto', 
            'shipping_address_collection' => [
                'allowed_countries' => ['US', 'CA', 'GB', 'AU'], 
            ],
        ]);
        $order = Payment::create([
            'amount'=>$price,
            'customer_id'=>$user_id,
            'participate_id'=>$participateid,
            'status'=>'pending',
            'olympiad_id'=>$oid,
            'session_id'=>$checkout_session->id
        ]);

        return response()->json(['url' => $checkout_session->url]);
    }

    public function success(Request $request){
        
        $session = $request->get('session_id');
        $stripeSecretKey = config('services.stripe.secret_key');
        
        Stripe::setApiKey($stripeSecretKey);
        $sessionn= \Stripe\Checkout\Session::retrieve($session);
        if(!$sessionn){
            return response()->json(['status'=>'failure','message'=>'success method fail, session id not found']);
        }
        return response()->json(['data'=>'you are in success, update database']);
    }

    public function cencel(Request $request){
        return response()->json(['status'=>'cancel','message'=>'payment process cancelled']);
    }
    public function webhook(Request $request){
        $payload = $request->getContent();
        $sig_header = $request->server('HTTP_STRIPE_SIGNATURE');
        $endpoint_secret = config('services.stripe.webhook_secret');
        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        switch ($event->type) {
            case 'payment_intent.succeeded':
                break;
            case 'payment_intent.payment_failed':
                break;
            default:
                break;
        }
        return response()->json(['success' => true]);
    }
}
