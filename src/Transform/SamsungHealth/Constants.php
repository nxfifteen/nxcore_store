<?php

namespace App\Transform\SamsungHealth;


use App\Transform\Transform;

class Constants extends Transform
{
    const FITBITSERVICE = "Fitbit";
    const SAMSUNGHEALTHSERVICE = "Samsung Health";
    const SAMSUNGHEALTHEPDAILYSTEPS = "count_daily_steps";
    const SAMSUNGHEALTHEPDEVICES = "tracking_devices";
    const SAMSUNGHEALTHEPINTRADAYSTEPS = "intraday_steps";
    const SAMSUNGHEALTHEPINTRADAYFLOORS = "count_daily_floors";
    const SAMSUNGHEALTHEPCONSUMWATER = "water_intakes";
    const SAMSUNGHEALTHEPCONSUMCAFFINE = "caffeine_intakes";
    const SAMSUNGHEALTHEPBODYWEIGHT = "body_weights";
    const SAMSUNGHEALTHEPEXERCISE = "exercises";
}