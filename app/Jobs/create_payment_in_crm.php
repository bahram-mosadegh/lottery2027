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

class create_payment_in_crm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;
    public $auth_user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $auth_user_id)
    {
        $this->id = $id;
        $this->auth_user_id = $auth_user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $applicant = Applicant::find($this->id);
        if ($applicant && !$applicant->crm_guid && $applicant->payment_status == 'paid') {
            $domain = rtrim(env('CRM_ENDPOINT', 'http://81.12.71.131'), '/');
            $url = $domain.'/main/api/data/v9.0/rhs_financials';
            $username = env('CRM_USERNAME', 'tech');
            $password = env('CRM_PASSWORD', 'qwer.4321');

            $trantime = strtotime($applicant->updated_at ? $applicant->updated_at : date('Y-m-d H:i:s'));
            $rhs_transactiondate = date('Y-m-d', $trantime).'T'.date('H:i:s', $trantime).'Z';

            if ($applicant->registration_type == 'onsite') {
                $rhs_transactiontype = 130770007;
            } else {
                $rhs_transactiontype = 130770006;
            }
            
            $post_data = [
                'rhs_Opportunity@odata.bind' => '/opportunities(1da3f215-135d-ee11-8fd6-005056bfe138)',
                'rhs_name' => ucwords(strtolower($applicant->name.' '.$applicant->last_name)),
                'rhs_transactiontype' => $rhs_transactiontype,
                'rhs_transactiondate' => $rhs_transactiondate,
                'rhs_paymentstatus' => 130770000,
                'new_contractnumfinancial' => 'N700000000001',
                'rhs_description' => ucwords(strtolower($applicant->name.' '.$applicant->last_name)).' | '.$applicant->mobile.' | L'.$applicant->id.' | '.\Helper::gregorian_to_jalali(date('Y', $trantime), date('m', $trantime), date('d', $trantime), '/').' | '.($applicant->registration_type == 'onsite' ? 'kiosk' : 'oline')
            ];

            
            $post_data['rhs_paidvalueirr'] = (int)$applicant->price;

            $response = Http::withOptions([
                'auth' => [$username, $password, 'ntlm', 'domain' => $domain],
            ])->post($url, $post_data);

            if (count($response->headers()) && isset($response->headers()['OData-EntityId']) && count($response->headers()['OData-EntityId'])) {
                preg_match('/rhs_financials\((.*?)\)/', $response->headers()['OData-EntityId'][0], $match);
                $crm_guid = isset($match[1]) ? $match[1] : null;
                if ($crm_guid) {
                    $applicant->crm_guid = $crm_guid;
                    $applicant->save();
                }
            }
        }
    }
}
