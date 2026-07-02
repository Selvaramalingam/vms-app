<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/dashboard', 'GET');
$kernel->handle($request);
Auth::loginUsingId(1);
try {
    $controller = app('App\Http\Controllers\DashboardController');
    $response = $controller->index($request);
    echo "Dashboard loaded successfully\n";
    $view = $response->render();
    echo "View rendered successfully\n";
} catch (\Throwable $e) {
    echo $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}
