<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Applicant;
use Illuminate\Support\Facades\Http;

class send_sms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;
    public $message;
    public $type;
    public $mobile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id = null, $message, $type = 'register', $mobile = null)
    {
        $this->id = $id;
        $this->message = $message;
        $this->type = $type;
        $this->mobile = $mobile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $applicant = Applicant::find($this->id);

        if ($applicant &&
            (
                ($this->type == 'register' && $applicant->registration_type != 'agent')
                || ($this->type == 'check_data' && $applicant->registration_type == 'online')
                || ($this->type == 'tracking_number')
                || ($this->type == 'lottery_status')
            )
        ) {
            $respose = $this->call_api($applicant->mobile, $this->message);

            if ($respose) {
                if ($respose->return->status == 200) {
                    $sms_status = 'success';
                } else {
                    $sms_status = 'fail';
                }
            } else {
                $sms_status = 'fail';
            }

            if ($this->type == 'lottery_status') {
                $applicant->lottery_status_sms = $sms_status;
            } else {
                $applicant->sms_status = $sms_status;
            }
            
            $applicant->save();
        }

        if (is_null($this->id) && $this->mobile) {
            $respose = $this->call_api($this->mobile, $this->message);
        }
    }

    public function call_api($mobile, $message)
    {
        $data = [
            'receptor' => $mobile,
            'sender' => '2000700075085',
            'message' => $message
        ];

        $query = http_build_query($data);

        $web_service_res = Http::withoutVerifying()->get('https://api.kavenegar.com/v1/595A564C58416F6E5A456468366C424678547151542F51776542307254565249314B503975495A754941733D/sms/send.json?'.$query);
        $respose = $web_service_res->object();

        return $respose;
    }
}
