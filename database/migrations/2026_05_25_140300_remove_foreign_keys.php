<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropForeign(['role_id']);
      $table->dropColumn('role_id');
    });

    Schema::table('subcategories', function (Blueprint $table) {
      $table->dropForeign(['category_id']);
      $table->dropColumn('category_id');
    });

    Schema::table('sla_configs', function (Blueprint $table) {
      $table->dropForeign(['category_id']);
      $table->dropColumn('category_id');
    });

    Schema::table('tickets', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->dropForeign(['assigned_to']);
      $table->dropForeign(['category_id']);
      $table->dropForeign(['subcategory_id']);
      $table->dropForeign(['asset_id']);
      $table->dropForeign(['reviewed_by']);

      $table->dropColumn('user_id');
      $table->dropColumn('assigned_to');
      $table->dropColumn('category_id');
      $table->dropColumn('subcategory_id');
      $table->dropColumn('asset_id');
      $table->dropColumn('reviewed_by');
    });

    Schema::table('ticket_attachments', function (Blueprint $table) {
      $table->dropForeign(['ticket_id']);
      $table->dropForeign(['uploaded_by']);

      $table->dropColumn('ticket_id');
      $table->dropColumn('uploaded_by');
    });

    Schema::table('ticket_activities', function (Blueprint $table) {
      $table->dropForeign(['ticket_id']);
      $table->dropForeign(['user_id']);

      $table->dropColumn('ticket_id');
      $table->dropColumn('user_id');
    });

    Schema::table('ticket_histories', function (Blueprint $table) {
      $table->dropForeign(['ticket_id']);
      $table->dropForeign(['user_id']);

      $table->dropColumn('ticket_id');
      $table->dropColumn('user_id');
    });
  }

  public function down(): void
  {
  }
};
