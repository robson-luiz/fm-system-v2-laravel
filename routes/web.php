<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Admin\TwoFactorSettingsController;
use App\Http\Controllers\Admin\EmailSmsSettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\ExpenseController;
use App\Http\Controllers\Finance\CreditCardController;
use App\Http\Controllers\Finance\IncomeController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserStatusController;
use Illuminate\Support\Facades\Route;

// Página inicial como redirecionamento para o login
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Tela de login
Route::get('/login', [AuthController::class, 'index'])->name('login');

// Processar os dados do login
Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.process');

// Logout
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Formulário cadastrar novo usuário
Route::get('/register', [AuthController::class, 'create'])->name('register');

// Receber os dados do formulário e cadastrar novo usuário
Route::post('/register', [AuthController::class, 'store'])->name('register.store');

// Solicitar link para resetar senha
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Formulário para redefinir a senha com o token
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showRequestForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Rotas de 2FA (fora do middleware auth para permitir acesso após login inicial)
Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show')->middleware('auth');
Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify')->middleware('auth');
Route::post('/two-factor/send', [TwoFactorController::class, 'sendCode'])->name('two-factor.send')->middleware('auth');
Route::post('/two-factor/resend', [TwoFactorController::class, 'resend'])->name('two-factor.resend')->middleware('auth');

// Grupo de rotas restritas (com 2FA)
Route::group(['middleware' => ['auth', 'two-factor']], function () {
    // Página inicial do administrativo
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('permission:dashboard');

    // Perfil
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show')->middleware('permission:show-profile');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('permission:edit-profile');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update')->middleware('permission:edit-profile');
        Route::get('/edit-image', [ProfileController::class, 'editImage'])->name('profile.edit_image')->middleware('permission:edit-profile');
        Route::put('/image', [ProfileController::class, 'updateImage'])->name('profile.update_image')->middleware('permission:edit-profile');
        Route::get('/edit-password', [ProfileController::class, 'editPassword'])->name('profile.edit_password')->middleware('permission:edit-password-profile');
        Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update_password')->middleware('permission:edit-password-profile');
    });

    // Usuários
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index')->middleware('permission:index-user');
        Route::get('/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:create-user');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:show-user');
        Route::post('/', [UserController::class, 'store'])->name('users.store')->middleware('permission:create-user');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:edit-user');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:edit-user');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:destroy-user');

        Route::get('/{user}/edit-password', [UserController::class, 'editPassword'])->name('users.edit_password')->middleware('permission:edit-password-user');
        Route::put('/{user}/update-password', [UserController::class, 'updatePassword'])->name('users.update_password')->middleware('permission:edit-password-user');

        Route::get('/{user}/edit-image', [UserController::class, 'editImage'])->name('users.edit_image')->middleware('permission:edit-image-user');
        Route::put('/{user}/image', [UserController::class, 'updateImage'])->name('users.update_image')->middleware('permission:edit-image-user');

        Route::get('/{user}/password-recovery-link', [UserController::class, 'passwordRecoveryLink'])->name('users.password_recovery_link')->middleware('permission:password-recovery-link-user');
    });

    // Usuários Status
    Route::prefix('user-statuses')->group(function () {
        Route::get('/', [UserStatusController::class, 'index'])->name('user_statuses.index')->middleware('permission:index-user-status');
        Route::get('/create', [UserStatusController::class, 'create'])->name('user_statuses.create')->middleware('permission:create-user-status');
        Route::get('/{userStatus}', [UserStatusController::class, 'show'])->name('user_statuses.show')->middleware('permission:show-user-status');
        Route::post('/', [UserStatusController::class, 'store'])->name('user_statuses.store')->middleware('permission:create-user-status');
        Route::get('/{userStatus}/edit', [UserStatusController::class, 'edit'])->name('user_statuses.edit')->middleware('permission:edit-user-status');
        Route::put('/{userStatus}', [UserStatusController::class, 'update'])->name('user_statuses.update')->middleware('permission:edit-user-status');
        Route::delete('/{userStatus}', [UserStatusController::class, 'destroy'])->name('user_statuses.destroy')->middleware('permission:destroy-user-status');
    });

    // Papéis
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:index-role');
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:create-role');
        Route::get('/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('permission:show-role');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:create-role');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:edit-role');
        Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:edit-role');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:destroy-role');
        Route::get('/update-order/{role}', [RoleController::class, 'updateOrder'])->name('roles.update-order')->middleware('permission:edit-role');
    });

    // Permissão do papel
    Route::prefix('role-permissions')->group(function () {
        Route::get('/{role}', [RolePermissionController::class, 'index'])->name('role-permissions.index')->middleware('permission:index-role-permission');
        Route::get('/{role}/{permission}', [RolePermissionController::class, 'update'])->name('role-permissions.update')->middleware('permission:update-role-permission');
    });

    // Permissão
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:index-permission');
        Route::get('/create', [PermissionController::class, 'create'])->name('permissions.create')->middleware('permission:create-permission');
        Route::get('/{permission}', [PermissionController::class, 'show'])->name('permissions.show')->middleware('permission:show-permission');
        Route::post('/', [PermissionController::class, 'store'])->name('permissions.store')->middleware('permission:create-permission');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit')->middleware('permission:edit-permission');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('permissions.update')->middleware('permission:edit-permission');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('permission:destroy-permission');
    });

    // Finanças - Despesas
    Route::prefix('expenses')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('expenses.index')->middleware('permission:index-expense');
        Route::get('/create', [ExpenseController::class, 'create'])->name('expenses.create')->middleware('permission:create-expense');
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('expenses.show')->middleware('permission:show-expense');
        Route::post('/', [ExpenseController::class, 'store'])->name('expenses.store')->middleware('permission:create-expense');
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit')->middleware('permission:edit-expense');
        Route::put('/{expense}', [ExpenseController::class, 'update'])->name('expenses.update')->middleware('permission:edit-expense');
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy')->middleware('permission:destroy-expense');
        
        // Rotas para marcar parcelas como paga/não paga
        Route::post('/installments/{installment}/mark-as-paid', [ExpenseController::class, 'markInstallmentAsPaid'])->name('installments.mark-as-paid')->middleware('permission:edit-expense');
        Route::post('/installments/{installment}/mark-as-unpaid', [ExpenseController::class, 'markInstallmentAsUnpaid'])->name('installments.mark-as-unpaid')->middleware('permission:edit-expense');
        
        // Rotas para marcar despesa inteira como paga/atrasada
        Route::post('/{expense}/mark-paid', [ExpenseController::class, 'markPaid'])->name('expenses.mark-paid')->middleware('permission:edit-expense');
        Route::post('/{expense}/mark-overdue', [ExpenseController::class, 'markOverdue'])->name('expenses.mark-overdue')->middleware('permission:edit-expense');
    });

    // Finanças - Cartões de Crédito
    Route::prefix('credit-cards')->group(function () {
        Route::get('/', [CreditCardController::class, 'index'])->name('credit-cards.index')->middleware('permission:index-credit-card');
        Route::get('/create', [CreditCardController::class, 'create'])->name('credit-cards.create')->middleware('permission:create-credit-card');
        Route::get('/{creditCard}', [CreditCardController::class, 'show'])->name('credit-cards.show')->middleware('permission:show-credit-card');
        Route::post('/', [CreditCardController::class, 'store'])->name('credit-cards.store')->middleware('permission:create-credit-card');
        Route::get('/{creditCard}/edit', [CreditCardController::class, 'edit'])->name('credit-cards.edit')->middleware('permission:edit-credit-card');
        Route::put('/{creditCard}', [CreditCardController::class, 'update'])->name('credit-cards.update')->middleware('permission:edit-credit-card');
        Route::delete('/{creditCard}', [CreditCardController::class, 'destroy'])->name('credit-cards.destroy')->middleware('permission:destroy-credit-card');
        
        // Rota para ativar/desativar cartão
        Route::post('/{creditCard}/toggle-status', [CreditCardController::class, 'toggleStatus'])->name('credit-cards.toggle-status')->middleware('permission:edit-credit-card');
    });

    // Finanças - Receitas
    Route::prefix('incomes')->group(function () {
        Route::get('/', [IncomeController::class, 'index'])->name('incomes.index')->middleware('permission:index-income');
        Route::get('/create', [IncomeController::class, 'create'])->name('incomes.create')->middleware('permission:create-income');
        Route::get('/{income}', [IncomeController::class, 'show'])->name('incomes.show')->middleware('permission:show-income');
        Route::post('/', [IncomeController::class, 'store'])->name('incomes.store')->middleware('permission:create-income');
        Route::get('/{income}/edit', [IncomeController::class, 'edit'])->name('incomes.edit')->middleware('permission:edit-income');
        Route::put('/{income}', [IncomeController::class, 'update'])->name('incomes.update')->middleware('permission:edit-income');
        Route::delete('/{income}', [IncomeController::class, 'destroy'])->name('incomes.destroy')->middleware('permission:destroy-income');

        // Rota para alternar status recebida/pendente
        Route::post('/{income}/toggle-status', [IncomeController::class, 'toggleStatus'])->name('incomes.toggle-status')->middleware('permission:edit-income');
    });

    // Rotas administrativas de 2FA
    Route::prefix('admin/two-factor')->name('admin.two-factor.')->group(function () {
        Route::get('/', [TwoFactorSettingsController::class, 'index'])->name('index')->middleware('permission:manage-system-settings');
        Route::put('/update', [TwoFactorSettingsController::class, 'update'])->name('update')->middleware('permission:manage-system-settings');
        Route::get('/statistics', [TwoFactorSettingsController::class, 'statistics'])->name('statistics')->middleware('permission:manage-system-settings');
    });

    // Rotas administrativas de Email e SMS
    Route::prefix('admin/email-sms')->name('admin.email-sms.')->group(function () {
        Route::get('/', [EmailSmsSettingsController::class, 'index'])->name('index')->middleware('permission:manage-system-settings');
        Route::put('/update-email', [EmailSmsSettingsController::class, 'updateEmail'])->name('update-email')->middleware('permission:manage-system-settings');
        Route::put('/update-sms', [EmailSmsSettingsController::class, 'updateSms'])->name('update-sms')->middleware('permission:manage-system-settings');
        Route::post('/test-email', [EmailSmsSettingsController::class, 'testEmail'])->name('test-email')->middleware('permission:manage-system-settings');
        Route::post('/test-sms', [EmailSmsSettingsController::class, 'testSms'])->name('test-sms')->middleware('permission:manage-system-settings');
    });

});
