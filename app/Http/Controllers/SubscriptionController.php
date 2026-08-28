<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Subscription;
use App\Repositories\SettingRepository;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /** @var SettingRepository $settingRepository*/
    private $settingRepository;

    public function __construct(SettingRepository $settingRepo)
    {
        $this->settingRepository = $settingRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $date['subscriptions'] = config('subscription.subscriptions');

        //
        return view('Subscription.index', $date);
    }

    public function payment($paymentType, $key)
    {
        $subscriptionArray = config('subscription.subscriptions');
        $fullAmount = number_format($subscriptionArray[$key]['price'], 2);

        // Base configurations
        $url = 'https://oppwa.com/v1/checkouts';
        //$url = "https://eu-test.oppwa.com/v1/checkouts";
        $authTokens = [
            1 => 'Bearer OGFjOWE0Y2E3NGEyYWQ3MjAxNzRkOWNhZjRhZTJmZWF8WDVxUENFOUdjaw==',
            2 => 'Bearer OGFjOWE0Y2E3NGEyYWQ3MjAxNzRkOWNhZjRhZTJmZWF8WDVxUENFOUdjaw==',
            3 => 'Bearer OGFjZGE0Yzk4MmU5NzA3ODAxODJmODRjOTNiOTVjMzN8elE3N1loTVg5Zw==',
        ];

        $entityIds = [
            1 => '8ac9a4ca74a2ad720174d9dbb65130a5',
            2 => '8ac9a4ca74a2ad720174d9cbd0482ff3',
            3 => '8acda4c982e970780182f84d4cca5c58',
        ];

        if (!isset($authTokens[$paymentType]) || !isset($entityIds[$paymentType])) {
            return back();
        }

        $authToken = $authTokens[$paymentType];
        $entityId = $entityIds[$paymentType];

        // Common data
        $name = auth()->user()->name;
        $email = auth()->user()->email;
        $transactionId = e(rand(100000000, 999999999));
        $billingInfo = [
            'billing.street1' => 'Riyadh',
            'billing.city' => 'Riyadh',
            'billing.state' => 'Riyadh',
            'billing.country' => 'SA',
            'billing.postcode' => '11564',
            'customer.givenName' => $name,
            'customer.surname' => $name,
            'customer.email' => $email,
            'merchantTransactionId' => $transactionId,
        ];

        // Prepare request data
        $postData = http_build_query(
            array_merge(
                [
                    'entityId' => $entityId,
                    'amount' => $fullAmount,
                    'currency' => 'SAR',
                    'paymentType' => 'DB',
                ],
                $billingInfo,
            ),
        );

        // Send CURL request
        $responseData = $this->sendCurlRequest($url, $authToken, $postData);

        if (!$responseData) {
            return back();
        }

        $response = json_decode($responseData);

        if ($response->result->code === '000.200.100') {
            $subscription = new Subscription();
            $subscription->chachtoken = $response->id;
            $subscription->payment_type = $paymentType;
            $subscription->price = $subscriptionArray[$key]['price'];
            $subscription->from_user = $subscriptionArray[$key]['from'];
            $subscription->to_user = $subscriptionArray[$key]['to'];
            $subscription->save();
            return redirect(route('Subscription.paymentSubscription', $subscription->id));
        }

        return back();
    }

    private function sendCurlRequest($url, $authToken, $postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $authToken"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        }

        curl_close($ch);
        return $responseData;
    }

    public function paymentSubscription($id)
    {
        $date['Subscription'] = Subscription::findOrFail($id);

        return view('Subscription.sub', $date);
    }

    public function paymentSubscriptionSave(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        $paymentTypes = [
            1 => [
                'url' => "https://oppwa.com/v1/checkouts/$request->id/payment",
                'entityId' => '8ac9a4ca74a2ad720174d9dbb65130a5',
                'authToken' => 'OGFjOWE0Y2E3NGEyYWQ3MjAxNzRkOWNhZjRhZTJmZWF8WDVxUENFOUdjaw==',
            ],
            2 => [
                'url' => "https://oppwa.com/v1/checkouts/$request->id/payment",
                'entityId' => '8ac9a4ca74a2ad720174d9cbd0482ff3',
                'authToken' => 'OGFjOWE0Y2E3NGEyYWQ3MjAxNzRkOWNhZjRhZTJmZWF8WDVxUENFOUdjaw==',
            ],
            3 => [
                'url' => "https://eu-prod.oppwa.com/v1/checkouts/$request->id/payment",
                'entityId' => '8acda4c982e970780182f84d4cca5c58',
                'authToken' => 'OGFjZGE0Yzk4MmU5NzA3ODAxODJmODRjOTNiOTVjMzN8elE3N1loTVg5Zw==',
            ],
        ];

        if (!array_key_exists($subscription->payment_type, $paymentTypes)) {

            return redirect(route('Subscription.paymentMessage',__('models/Subscription.message.Payment_type')));
        }

        $paymentData = $paymentTypes[$subscription->payment_type];
        $url = $paymentData['url'] . '?entityId=' . $paymentData['entityId'];

        // Execute the CURL request
        $responseData = $this->makeCurlRequest($url, $paymentData['authToken']);
        if (!$responseData) {
            return redirect(route('Subscription.paymentMessage',__('models/Subscription.message.process_failed')));
        }

        $response = json_decode($responseData);

        if ($response->result->code == '000.000.000') {
            $this->updateSubscription($subscription);

            return redirect(route('Subscription.paymentMessage',__('models/Subscription.message.Payment_successfully')));


        }

        return redirect(route('Subscription.paymentMessage',__('models/Subscription.message.Payment_failed')));



    }

    /**
     * Helper function to perform CURL request.
     */
    private function makeCurlRequest($url, $authToken)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $authToken"]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Should be true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return $response;
    }





    /**
     * Helper function to update subscription details.
     */
    private function updateSubscription($subscription)
    {
        $subscription->type = 'success';
        $subscription->date = now()->format('Y-m-d');
        $subscription->save();

        $settings = Setting::first();
        $settings->actual_user = $subscription->to_user;
        $settings->save();
    }



    public function paymentMessage($subscription)
    {
        return view('Subscription.message',)->with('subscription',$subscription);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
