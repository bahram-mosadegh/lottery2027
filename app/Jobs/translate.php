<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Applicant;
use App\Models\Spouse;
use App\Models\AdultChild;
use App\Models\Child;
use Illuminate\Support\Facades\Http;

class translate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;
    public $table;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $table)
    {
        $this->id = $id;
        $this->table = $table;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->table == 'applicant') {
            $person = Applicant::find($this->id);
            $birth_city_en = $person->birth_city_en;
            $residence_state_en = $person->residence_state_en;
            $residence_city_en = $person->residence_city_en;
            $residence_address_en = $person->residence_address_en;
        } elseif ($this->table == 'spouse') {
            $person = Spouse::find($this->id);
            $birth_city_en = $person->birth_city_en;
            $residence_state_en = true;
            $residence_city_en = true;
            $residence_address_en = true;
        } elseif ($this->table == 'adult_child') {
            $person = AdultChild::find($this->id);
            $birth_city_en = $person->birth_city_en;
            $residence_state_en = true;
            $residence_city_en = true;
            $residence_address_en = true;
        } elseif ($this->table == 'child') {
            $person = Child::find($this->id);
            $birth_city_en = $person->birth_city_en;
            $residence_state_en = true;
            $residence_city_en = true;
            $residence_address_en = true;
        } else {
            $person = null;
        }
        
        if ($person && (!$birth_city_en || !$residence_address_en)) {
            $key = env('googleapis_key', 'AIzaSyB8N9RRMbsYGk0cWr4ujQli28Nd3wECh5Y');

            if (!$birth_city_en) {
                $response = Http::withoutVerifying()->timeout(10)->post('https://translation.googleapis.com/language/translate/v2?key='.$key, [
                    'q' => $person->birth_city,
                    'source' => 'fa',
                    'target' => 'en',
                    'format' => 'text',
                ]);

                if ($response->successful()) {
                    $person->birth_city_en = $response->object()->data->translations[0]->translatedText;
                }
            }

            if (!$residence_state_en) {
                $response = Http::withoutVerifying()->timeout(10)->post('https://translation.googleapis.com/language/translate/v2?key='.$key, [
                    'q' => $person->residence_state,
                    'source' => 'fa',
                    'target' => 'en',
                    'format' => 'text',
                ]);

                if ($response->successful()) {
                    $person->residence_state_en = $response->object()->data->translations[0]->translatedText;
                }
            }

            if (!$residence_city_en) {
                $response = Http::withoutVerifying()->timeout(10)->post('https://translation.googleapis.com/language/translate/v2?key='.$key, [
                    'q' => $person->residence_city,
                    'source' => 'fa',
                    'target' => 'en',
                    'format' => 'text',
                ]);

                if ($response->successful()) {
                    $person->residence_city_en = $response->object()->data->translations[0]->translatedText;
                }
            }

            if (!$residence_address_en) {
                $response = Http::withoutVerifying()->timeout(10)->post('https://translation.googleapis.com/language/translate/v2?key='.$key, [
                    'q' => 'واحد '.$person->residence_unit.'، پلاک '.$person->residence_no.'، کوچه '.$person->residence_alley.'، خیابان'.$person->residence_street,
                    'source' => 'fa',
                    'target' => 'en',
                    'format' => 'text',
                ]);

                if ($response->successful()) {
                    $person->residence_address_en = $response->object()->data->translations[0]->translatedText;
                }
            }

            $person->save();
        }
    }
}
