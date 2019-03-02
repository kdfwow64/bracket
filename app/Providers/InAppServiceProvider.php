<?php

namespace App\Providers;

use App\Providers\BaseServiceProvider;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Bracket;
use App\Models\Receipt;
use App\Models\Transaction;
use DB;
use App\Utility\CommonUtility;

/**
 * InAppServiceProvider class contains methods for InApp of user management
 */
class InAppServiceProvider extends BaseServiceProvider {

    /**
     * List of subscription Users
     * @return array
     */
    public static function getSubscriptionUsers() {
        return Subscription::getSubscriptionUsers();
    }

    /**
     * Total purchases in current month
     * @return array
     */
    public static function getTotalPurchases() {
        return Subscription::getTotalPurchases();
    }

    /**
     * In app purchase
     * @return array
     */
    public static function inAppPurchase($user_id, $request) {
        $data = array(
            'type' => $request['type'],
            'user_id' => $user_id,
            'bundle_id' => $request['bundleId'],
            'price' => $request['price'],
        );

        if ($request['type'] == 1) {
            //additional bracket purchase
            $subscription_id = Subscription::insertGetId($data);
            $user = User::find($user_id);
            $user->paid_bracket_count = $user->paid_bracket_count + 1;
            $success = $user->save();
        } else {
            //bracket monthly subscription purchase
            $subscription = Subscription::select('id')
                    ->where('type', 2)
                    ->where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            $success = 1;
            if (!is_object($subscription)) {
                $environment = config('environment.in_app_environment');
                $receipt_info = CommonUtility::validateInAppReceipt($environment, $request['receipt_data']);
                if ($receipt_info['status'] == 1) {
                    $subscription_id = Subscription::insertGetId($data);
                    $receipt_data = array(
                        'receipt_data' => $request['receipt_data'],
                        'subscription_id' => $subscription_id
                    );
                    $latest_receipt_info = end($receipt_info['receipt_data']->latest_receipt_info);
                    $end_time = CommonUtility::generateDate($latest_receipt_info->expires_date_ms);
                    $receipt_id = Receipt::insertGetId($receipt_data);
                    $subscription = Subscription::select(DB::raw('NOW() AS time'), 'created_at')
                                    ->where('id', $subscription_id)->first();
                    $transaction_data = array(
                        'subscription_id' => $subscription_id,
                        'receipt_id' => $receipt_id,
                        'start_date' => $subscription->created_at,
                        'end_date' => $end_time,
                        'user_id' => $user_id
                    );
                    $success = Transaction::insert($transaction_data);
                    $count = Bracket::where('user_id', $user_id)
                            ->where('type', 0)
                            ->whereRaw('Date(created_at) = CURDATE()')
                            ->count();
                    if ($count >= 1) {
                        User::where('id', $user_id)->update([
                            'is_paid' => 1,
                            'free_bracket_count' => 1
                        ]);
                    } else {
                        User::where('id', $user_id)->update([
                            'is_paid' => 1,
                            'free_bracket_count' => 2
                        ]);
                    }
                    Bracket::where('user_id', $user_id)
                            ->where('type', 0)
                            ->where('is_completed', 0)
                            ->orderBy('created_at', 'desc')
                            ->update(['is_time_bounded' => 0, 'is_paid_bracket' => 1]);
                } else {
                    $success = 0;
                }
            } else {
                Receipt::where('subscription_id', $subscription->id)
                        ->update(['receipt_data' => $request['receipt_data']]);
            }
        }
        if ($success) {
            static::$response['message'] = trans('messages.success.in_app');
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.in_app');
        }
        return static::$response;
    }

}
