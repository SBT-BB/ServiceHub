<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\BookingController;

$request = Request::create('/booking/search-customers', 'GET', ['q' => 'a']);
$controller = new BookingController();

try {
    $response = $controller->searchCustomers($request);
    echo "Response status code: " . $response->getStatusCode() . "\n";
    echo "Response content:\n" . $response->getContent() . "\n";
} catch (\Throwable $e) {
    echo "Error calling searchCustomers: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
