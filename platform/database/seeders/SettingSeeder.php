<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'office_phone' => '(281) 541-0027',
            'office_address' => '22955 State Highway 249 Suite 26, Tomball, TX 77375',
            'hours' => 'Mon–Sat 7am–7pm · Emergency 24/7',
            'notify_emails' => 'owner@corefourroofing.com',
            'notify_phones' => '2815410027',
            'chat_offline' => 'We are offline — leave a number and we will call you from the Tomball office.',
            'google_review_url' => 'https://www.google.com/search?q=Core+Four+Roofing+Tomball+TX+reviews',
            'yelp_review_url' => 'https://www.yelp.com/biz/core-four-roofing-tomball-3',
        ];

        foreach ($defaults as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
