<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sent_emails', function (Blueprint $table) {
            $table->id();

            $table->string('subject')->nullable();  // assunto do e-mail
            $table->text('body')->nullable();       // corpo do e-mail
            $table->string('recipient_email');      // destinatário
            $table->timestamp('sent_at')->nullable(); // data/hora envio

            // chave estrangeira para users
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade'); // se o user for deletado, apaga os e-mails também

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sent_emails');
    }
};
