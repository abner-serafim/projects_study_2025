<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

Route::get('/', function () {

    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::get('/debug-env-test', function () {
    $envVars = [
        'APP_NAME_env' => env('APP_NAME'),
        'APP_ENV_env' => env('APP_ENV'),
        'APP_DEBUG_env' => env('APP_DEBUG'),
        'APP_KEY_exists_env' => !empty(env('APP_KEY')), // Verifica se APP_KEY tem algum valor
        'DB_CONNECTION_env' => env('DB_CONNECTION'),
        'DB_HOST_env' => env('DB_HOST'),
        'DB_PORT_env' => env('DB_PORT'),
        'DB_DATABASE_env' => env('DB_DATABASE'),
        'DB_USERNAME_env' => env('DB_USERNAME'),
        'DB_PASSWORD_env' => env('DB_PASSWORD'),
        'MAIL_FROM_ADDRESS_env' => env('MAIL_FROM_ADDRESS'),
        // Não exiba DB_PASSWORD diretamente por segurança, mesmo em debug
    ];

    $configVars = [
        'app_name_config' => Config::get('app.name'),
        'app_env_config' => Config::get('app.env'),
        'app_debug_config' => Config::get('app.debug'),
        'db_connection_config' => Config::get('database.default'),
        'db_host_config' => Config::get('database.connections.mysql.host'),
        'db_port_config' => Config::get('database.connections.mysql.port'),
        'db_database_config' => Config::get('database.connections.mysql.database'),
        'db_password' => Config::get('database.connections.mysql.password'),
        'db_username_config' => Config::get('database.connections.mysql.username'),
    ];

    $dbConnectionStatus = 'Não testado';
    try {
        DB::connection()->getPdo();
        $dbConnectionStatus = 'Conexão com o banco de dados bem-sucedida!';
    } catch (\Exception $e) {
        $dbConnectionStatus = 'Falha ao conectar com o banco de dados: ' . $e->getMessage();
    }

    return response()->json([
        'valores_do_env_direto' => $envVars,
        'valores_da_configuracao_laravel' => $configVars,
        'status_conexao_db' => $dbConnectionStatus,
    ]);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
