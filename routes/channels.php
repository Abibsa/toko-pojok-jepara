<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('stock.{productId}', function () {
    return true; // Public channel, everyone can listen
});
