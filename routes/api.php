<?php

use App\Helpers\ResponseHelper;

router()->get('/api/health', static function (array $params = []): void {
    ResponseHelper::json([
        'status' => 'ok',
        'app' => app('app.name'),
        'time' => date('c'),
    ]);
});
