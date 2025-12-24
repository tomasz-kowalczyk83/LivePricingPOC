<?php

namespace Database\Seeders;

use App\Models\Part;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Database Integration Supplier
        $databaseSupplier = Supplier::create([
            'name' => 'AutoParts Direct (Database)',
            'integration_type' => 'database',
            'api_endpoint' => null,
            'notification_channels' => null,
            'is_active' => true,
        ]);

        // Add parts for database supplier
        Part::create([
            'supplier_id' => $databaseSupplier->id,
            'sku' => 'BRK-001',
            'name' => 'Front Brake Pads',
            'description' => 'Premium ceramic brake pads for most vehicles',
            'price' => 45.99,
            'stock_quantity' => 150,
            'fits_vehicle' => [
                'year' => 2020,
                'make' => 'Toyota',
                'model' => 'Camry',
            ],
        ]);

        Part::create([
            'supplier_id' => $databaseSupplier->id,
            'sku' => 'OIL-001',
            'name' => 'Engine Oil Filter',
            'description' => 'High-performance oil filter',
            'price' => 12.99,
            'stock_quantity' => 300,
            'fits_vehicle' => [
                'year' => 2021,
                'make' => 'Honda',
                'model' => 'Civic',
            ],
        ]);

        Part::create([
            'supplier_id' => $databaseSupplier->id,
            'sku' => 'AIR-001',
            'name' => 'Cabin Air Filter',
            'description' => 'HEPA cabin air filter',
            'price' => 18.50,
            'stock_quantity' => 200,
            'fits_vehicle' => [
                'year' => 2019,
                'make' => 'Ford',
                'model' => 'F-150',
            ],
        ]);

        // Create API Integration Supplier
        Supplier::create([
            'name' => 'PartsHub API (API)',
            'integration_type' => 'api',
            'api_endpoint' => 'https://api.partshub.example/quote',
            'notification_channels' => null,
            'is_active' => true,
        ]);

        // Create Manual Integration Supplier
        Supplier::create([
            'name' => 'Premium Parts Co (Manual)',
            'integration_type' => 'manual',
            'api_endpoint' => null,
            'notification_channels' => ['email', 'sms'],
            'is_active' => true,
        ]);

        // Create additional database supplier
        $supplier2 = Supplier::create([
            'name' => 'Quick Parts Supply (Database)',
            'integration_type' => 'database',
            'api_endpoint' => null,
            'notification_channels' => null,
            'is_active' => true,
        ]);

        Part::create([
            'supplier_id' => $supplier2->id,
            'sku' => 'BRK-002',
            'name' => 'Front Brake Pads - Budget',
            'description' => 'Economy brake pads',
            'price' => 29.99,
            'stock_quantity' => 100,
            'fits_vehicle' => [
                'year' => 2020,
                'make' => 'Toyota',
                'model' => 'Camry',
            ],
        ]);

        $this->command->info('Database seeded with suppliers and parts!');
        $this->command->info('- 2 Database suppliers with parts');
        $this->command->info('- 1 API supplier');
        $this->command->info('- 1 Manual supplier');

        // Create test users for Filament panels
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'supplier_id' => null,
        ]);

        \App\Models\User::create([
            'name' => 'AutoParts Vendor',
            'email' => 'vendor1@example.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
            'supplier_id' => $databaseSupplier->id,
        ]);

        \App\Models\User::create([
            'name' => 'Quick Parts Vendor',
            'email' => 'vendor2@example.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
            'supplier_id' => $supplier2->id,
        ]);

        \App\Models\User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'supplier_id' => null,
        ]);

        $this->command->info('✅ Created test users:');
        $this->command->info('  Admin: admin@example.com / password');
        $this->command->info('  Vendor 1: vendor1@example.com / password');
        $this->command->info('  Vendor 2: vendor2@example.com / password');
        $this->command->info('  User: user@example.com / password');
    }
}
