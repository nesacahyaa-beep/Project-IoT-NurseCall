config/nursecall.php:

php
<?php
return [
    'device_key'         => env('NURSECALL_DEVICE_KEY'),
    'offline_after'      => 30,   // detik tanpa heartbeat → offline
    'sensor_store_every' => 30,   // simpan pembacaan sensor tiap N detik
    'temp_range'         => [22, 24],
    'humidity_range'     => [40, 60],
];