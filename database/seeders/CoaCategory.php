<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoaCategory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            \App\Models\CoaCategory::insert(
                [
                    'id' => 1,
                    'name' => 'Salary',
                ],
                [
                    'id' => 2,
                    'name' => 'Other Income',
                ],
                [
                    'id' => 3,
                    'name' => 'Family Expense',
                ],
                [
                    'id' => 4,
                    'name' => 'Transport Expense',
                ],
                [
                    'id' => 5,
                    'name' => 'Meal Expense',
                ],
            );
        });
    }
}
