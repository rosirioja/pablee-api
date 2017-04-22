<?php

use Illuminate\Database\Seeder;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status')->insert([
          'display_name' => 'Active',
          'name' => 'active'
        ]);

        DB::table('status')->insert([
          'display_name' => 'Inactive',
          'name' => 'inactive'
        ]);

        DB::table('status')->insert([
          'display_name' => 'Open',
          'name' => 'open'
        ]);

        DB::table('status')->insert([
          'display_name' => 'Offered',
          'name' => 'offered'
        ]);

        DB::table('status')->insert([
          'display_name' => 'On Purchase',
          'name' => 'on_purchase'
        ]);

        DB::table('status')->insert([
          'display_name' => 'On Delivery',
          'name' => 'on_delivery'
        ]);

        DB::table('status')->insert([
          'display_name' => 'On Acknowledgement',
          'name' => 'on_acknowledgement'
        ]);

        DB::table('status')->insert([
          'display_name' => 'Completed',
          'name' => 'completed'
        ]);

        DB::table('status')->insert([
          'display_name' => 'Accepted',
          'name' => 'accepted'
        ]);

        DB::table('status')->insert([
          'display_name' => 'Rejected',
          'name' => 'rejected'
        ]);

        DB::table('status')->insert([
          'display_name' => 'Cancelled',
          'name' => 'cancelled'
        ]);
    }
}
