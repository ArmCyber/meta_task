<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('old-assets:cleanup')->hourly();
