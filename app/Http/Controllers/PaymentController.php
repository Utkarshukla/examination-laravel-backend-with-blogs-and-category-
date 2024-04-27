<?php

namespace App\Http\Controllers;

use App\Models\Olympiad;
use App\Models\Participate;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Tymon\JWTAuth\Facades\JWTAuth;

class PaymentController extends Controller
{
    
    public function checkout(Request $request,string $id){ 
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;
        $user_role= $user->role;
        $oid =$id;
        $data = [];
        if ($user_role == 5) {
           
            $participate = Participate::with('participantUser')->where('user_id', $user_id)
                ->where('olympiad_id', $oid)->where('isfullPaid' ,null)
                ->firstOrFail();
            if($participate){
                $olympiad = Olympiad::find($oid);
                $olympiadName = $olympiad->name;
                $participateid= $participate->id;
                $price = $participate->total_amount;
                $participant_name = $participate->participantUser->name;
                $olympiadName= $participate->participantOlympiad->name;
                $stripeSecretKey = config('services.stripe.secret_key');
                $frontendurl= config('services.frontend_url.frontend_url_r');
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
                    'success_url' =>$frontendurl."/sucess/?session_id={CHECKOUT_SESSION_ID}",
                    'cancel_url' => $frontendurl."/cancel",
                    'billing_address_collection' => 'auto', 
                    'shipping_address_collection' => [
                        'allowed_countries' => ['US', 'CA', 'GB', 'AU','IN'], 
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
            
        } else if ($user_role == 2) {
            $participatesData = Participate::with('participantUser')->where('created_by', $user_id)
                ->where('olympiad_id', $oid) 
                ->get();
            $price = $participatesData->where('isfullPaid', '!=', 1)->sum('total_amount');
            $olympiad = Olympiad::find($oid);
            $olympiadName = $olympiad->name;

            $stripeSecretKey = config('services.stripe.secret_key');
                $frontendurl= config('services.frontend_url.frontend_url_r');
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
                    'success_url' =>$frontendurl."/sucess/?session_id={CHECKOUT_SESSION_ID}",
                    'cancel_url' => $frontendurl."/cancel",
                    'billing_address_collection' => 'auto', 
                    'shipping_address_collection' => [
                        'allowed_countries' => ['US', 'CA', 'GB', 'AU','IN'], 
                    ],
                ]);
                $order = Payment::create([
                    'amount'=>$price,
                    'customer_id'=>$user_id,
                    'created_by'=>$user_id,
                    'status'=>'pending',
                    'olympiad_id'=>$oid,
                    'session_id'=>$checkout_session->id
                ]);
                return response()->json(['url' => $checkout_session->url]);
        }

    }

    public function success(Request $request, string $session_id){
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;
        $user_role=$user->role;
        if($user_role==5){
            $session = $session_id;
            $stripeSecretKey = config('services.stripe.secret_key');
            Stripe::setApiKey($stripeSecretKey);
            $sessionn= \Stripe\Checkout\Session::retrieve($session);
            if(!$sessionn){
                return response()->json(['status'=>'failure','message'=>'success method fail, session id not found']);
            }
            $order=Payment::where('session_id',$sessionn->id)->firstOrFail();
            $participate_id = $order->participate_id;
            $olympiad_id = $order->olympiad_id;
            $user_id = $order->customer_id;
            
            $participateupdatePaymentstatus = Participate::where('olympiad_id',$olympiad_id)->where('user_id',$user_id)->where('id',$participate_id)->firstOrFail();
            $participateupdatePaymentstatus->update([
                'isfullPaid'=>1,
                'total_ammount_locked'=>1,
                'payment_id'=>$order->id
            ]);
            $order->update([
                'status'=>'success'
            ]);
            return response()->json(['status'=>'success', 'data'=>$sessionn,'message'=>'Your Payment successfully Completed, Wait for Matrix Admin response, we will send you your admit card soon', 'url'=>$olympiad_id]);
        } else if($user_role ==2){
            $session = $session_id;
            $stripeSecretKey = config('services.stripe.secret_key');
            Stripe::setApiKey($stripeSecretKey);
            $sessionn= \Stripe\Checkout\Session::retrieve($session);
            if(!$sessionn){
                return response()->json(['status'=>'failure','message'=>'failure, Payement Session Id is wrong or Not fount Cantact to Admin']);
            }

            $order=Payment::where('session_id',$sessionn->id)->firstOrFail();
            $incharge = $order->created_by;
            $olympiad_id = $order->olympiad_id;

            $participate = Participate::where('created_by', $incharge)
                ->where('olympiad_id', $olympiad_id)
                ->first();

            if ($participate) {
                $participate->isfullPaid = 1;
                $participate->total_ammount_locked = 1;
                $participate->save();

                $order->update([
                    'status'=>'success'
                ]);
            }

            return response()->json(['status'=>'success','message'=>'Payment Processing Done, Data has been Updated', 'url'=>$olympiad_id]);
        }
        
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
