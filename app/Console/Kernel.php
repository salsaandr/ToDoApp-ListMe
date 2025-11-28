protected $middlewareGroups = [
    'web' => [
        // middleware lain...
        \App\Http\Middleware\CheckTodosMiddleware::class,
    ],
];
