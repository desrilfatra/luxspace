 <?php

namespace App\Http\Controllers\API;

use Midtrans\Config;
use App\Http\Controllers\Controller;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Midtrans\Notification;

use function PHPSTORM_META\type;

class MidtransController extends Controller
{
    public function callback()
    {
        //Set Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        // Buat instance midtrans notification
        $notification = new Notification();

        //Assign ke variable untuk memudahkan coding
        $status = $notification->transaction_status;
        $type = $notification->payment_type;
        $fraud = $notification->fraud_status;
        $order_id = $notification->order_id; 

        // Get transaction id
        $order = explode('-', $order_id);

        // Cari transaksi berdasarkan ID
        $transaction = Transaction::findOrFail($orde[1]);

        // Handle Notification status midtrans
        if ($status == 'capture') {
            if($type == 'credit_card'){
                if ($fraud == 'challenge') {
                    $transaction->status = 'PENDING';
                }else{
                    $transaction->status = 'SUCCESS';
                }
            }
        }
        elseif ($status = 'settlement') {
            $transaction->status = 'SUCCESS';
        }
        elseif ($status = 'Pending') {
            $transaction->status = 'PENDING';
        }
        elseif ($status = 'deny') {
            $transaction->status = 'PENDING';
        }
        elseif ($status = 'expire') {
            $transaction->status = 'CANCELLED';
        }
        elseif ($status = 'cancel') {
            $transaction->status = 'CANCELLED';
        }
         
        // simpan transaksi
        $transaction->save();

        // Return Response untuk midtrans
        return response()->json([
            'meta' =>[
                'code' => 200,
                'message' => 'Midtrans Notification Success!' 
            ]
        ]);
    }
}
