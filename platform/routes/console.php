<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('email:send-due')->everyFiveMinutes();
