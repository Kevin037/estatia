<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Sales;
use App\Models\Unit;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\Feedback;
use App\Models\JournalEntry;
use App\Models\Account;
use App\Models\Project;
use App\Models\Cluster;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // 1. Seed Master Data (from existing seeders)
        $this->command->info('📦 Seeding master data...');
        $this->call([
            AccountSeeder::class,
            TypeSeeder::class,
            LandSeeder::class,
            MaterialSeeder::class,
            MaterialSupplierSeeder::class,
            SupplierSeeder::class,
            ContractorSeeder::class,
            MilestoneSeeder::class,
            FormulaSeeder::class,
        ]);

        // 2. Create Users
        $this->command->info('👤 Creating users...');
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@estatia.com',
            'password' => bcrypt('password'),
        ]);
        User::factory(5)->create();

        // 3. Create Customers
        $this->command->info('🏠 Creating customers...');
        Customer::factory(50)->create();

        // 4. Create Sales People
        $this->command->info('💼 Creating sales people...');
        Sales::factory(10)->create();

        // 5. Seed Products first (needed for units)
        $this->command->info('📦 Seeding products...');
        $this->call(ProductSeeder::class);

        // 6. Create Projects with Clusters and Units
        $this->command->info('🏗️ Creating projects, clusters, and units...');
        $this->call(ProjectSeeder::class); // This creates projects
        
        // Get existing projects from ProjectSeeder
        $projects = Project::all();
        
        foreach ($projects as $project) {
            // Create 2-5 clusters per project
            $clusters = Cluster::factory(rand(2, 5))->create([
                'project_id' => $project->id
            ]);

            foreach ($clusters as $cluster) {
                // Get existing products
                $products = Product::inRandomOrder()->limit(3)->get();
                
                // Create 5-15 units per cluster with different statuses
                foreach ($products as $product) {
                    $unitsCount = rand(5, 15);
                    
                    // Create available units
                    Unit::factory($unitsCount * 0.4)->available()->create([
                        'cluster_id' => $cluster->id,
                        'product_id' => $product->id,
                        'sales_id' => Sales::inRandomOrder()->first()->id,
                    ]);

                    // Create sold units
                    Unit::factory($unitsCount * 0.4)->sold()->create([
                        'cluster_id' => $cluster->id,
                        'product_id' => $product->id,
                        'sales_id' => Sales::inRandomOrder()->first()->id,
                    ]);

                    // Create reserved units
                    Unit::factory($unitsCount * 0.2)->reserved()->create([
                        'cluster_id' => $cluster->id,
                        'product_id' => $product->id,
                        'sales_id' => Sales::inRandomOrder()->first()->id,
                    ]);
                }
            }
        }

        $this->command->info("   ✓ Created {$projects->count()} projects");
        $this->command->info("   ✓ Created " . Cluster::count() . " clusters");
        $this->command->info("   ✓ Created " . Unit::count() . " units");

        // 6. Create Orders (only for sold/reserved units)
        $this->command->info('📝 Creating orders...');
        $soldUnits = Unit::whereIn('status', ['sold', 'reserved'])->get();
        
        foreach ($soldUnits as $unit) {
            Order::factory()->create([
                'customer_id' => Customer::inRandomOrder()->first()->id,
                'project_id' => $unit->cluster->project_id,
                'cluster_id' => $unit->cluster_id,
                'unit_id' => $unit->id,
                'total' => $unit->price,
                'status' => $unit->status === 'sold' ? 'completed' : 'pending',
            ]);
        }
        $this->command->info("   ✓ Created " . Order::count() . " orders");

        // 7. Create Invoices for completed orders
        $this->command->info('💰 Creating invoices...');
        $completedOrders = Order::where('status', 'completed')->get();
        
        foreach ($completedOrders as $order) {
            Invoice::factory()->create([
                'order_id' => $order->id,
                'dt' => $order->dt,
            ]);
        }
        $this->command->info("   ✓ Created " . Invoice::count() . " invoices");

        // 8. Create Payments for invoices
        $this->command->info('💳 Creating payments...');
        $invoices = Invoice::all();
        
        foreach ($invoices as $invoice) {
            $totalAmount = $invoice->order->total;
            $paidSoFar = 0;
            
            // Create 1-3 payments per invoice
            $numPayments = rand(1, 3);
            
            for ($i = 0; $i < $numPayments; $i++) {
                if ($paidSoFar >= $totalAmount) break;
                
                $remaining = $totalAmount - $paidSoFar;
                $isLastPayment = ($i === $numPayments - 1);
                
                // Last payment should cover remaining amount
                $amount = $isLastPayment ? $remaining : rand($remaining * 0.2, $remaining * 0.5);
                
                Payment::factory()->create([
                    'invoice_id' => $invoice->id,
                    'dt' => fake()->dateTimeBetween($invoice->dt, 'now'),
                    'amount' => $amount,
                ]);
                
                $paidSoFar += $amount;
            }

            // Update invoice status based on payments
            $totalPaid = $invoice->payments()->sum('amount');
            if ($totalPaid >= $totalAmount) {
                $invoice->update(['status' => 'paid']);
            } elseif ($totalPaid > 0) {
                $invoice->update(['status' => 'partial']);
            }
        }
        $this->command->info("   ✓ Created " . Payment::count() . " payments");

        // 9. Create Tickets for some completed orders
        $this->command->info('🎫 Creating tickets...');
        $ordersForTickets = Order::where('status', 'completed')
            ->inRandomOrder()
            ->limit(Order::where('status', 'completed')->count() * 0.3)
            ->get();
        
        foreach ($ordersForTickets as $order) {
            Ticket::factory(rand(1, 2))->create([
                'order_id' => $order->id,
            ]);
        }
        $this->command->info("   ✓ Created " . Ticket::count() . " tickets");

        // 10. Create Feedbacks for completed orders
        $this->command->info('⭐ Creating feedbacks...');
        $ordersForFeedback = Order::where('status', 'completed')
            ->inRandomOrder()
            ->limit(Order::where('status', 'completed')->count() * 0.4)
            ->get();
        
        foreach ($ordersForFeedback as $order) {
            Feedback::factory()->create([
                'order_id' => $order->id,
            ]);
        }
        $this->command->info("   ✓ Created " . Feedback::count() . " feedbacks");

        // 11. Seed Purchase Orders and Sales
        $this->command->info('🛒 Seeding purchase orders and sales...');
        $this->call([
            PurchaseOrderSeeder::class,
            SaleSeeder::class,
        ]);

        // 12. Create Journal Entries for each transaction type (10 entries per type)
        $this->command->info('📊 Creating journal entries...');
        $this->createBalancedJournalEntries();
        
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📈 Summary:');
        $this->command->info("   - Users: " . User::count());
        $this->command->info("   - Customers: " . Customer::count());
        $this->command->info("   - Sales People: " . Sales::count());
        $this->command->info("   - Projects: " . Project::count());
        $this->command->info("   - Clusters: " . Cluster::count());
        $this->command->info("   - Units: " . Unit::count());
        $this->command->info("   - Orders: " . Order::count());
        $this->command->info("   - Invoices: " . Invoice::count());
        $this->command->info("   - Payments: " . Payment::count());
        $this->command->info("   - Tickets: " . Ticket::count());
        $this->command->info("   - Feedbacks: " . Feedback::count());
        $this->command->info("   - Journal Entries: " . JournalEntry::count());
    }

    /**
     * Create balanced journal entries for each transaction type
     */
    protected function createBalancedJournalEntries(): void
    {
        $transactionTypes = [
            'Order' => Order::class,
            'Payment' => Payment::class,
            'PurchaseOrder' => \App\Models\PurchaseOrder::class,
        ];

        // Get accounts for different entry types
        $kasAccount = Account::where('code', 'like', '101%')->where('parent_id', '!=', null)->first();
        $piutangAccount = Account::where('code', 'like', '102%')->where('parent_id', '!=', null)->first();
        $pendapatanAccount = Account::where('code', 'like', '401%')->where('parent_id', '!=', null)->first();
        $utangAccount = Account::where('code', 'like', '201%')->where('parent_id', '!=', null)->first();
        $biayaAccount = Account::where('code', 'like', '501%')->where('parent_id', '!=', null)->first();
        $persediaanAccount = Account::where('code', 'like', '103%')->where('parent_id', '!=', null)->first();

        $journalEntryId = 1;

        foreach ($transactionTypes as $transactionName => $modelClass) {
            // Get 20 random transactions of this type (10 existing + 10 new)
            $transactions = $modelClass::inRandomOrder()->limit(20)->get();

            foreach ($transactions as $transaction) {
                // Determine amount based on transaction type
                $amount = match($transactionName) {
                    'Order' => $transaction->total,
                    'Payment' => $transaction->amount,
                    'PurchaseOrder' => $transaction->total ?? rand(1000000, 50000000),
                    default => rand(1000000, 50000000)
                };

                $dt = $transaction->dt ?? $transaction->created_at;

                // Create balanced entries based on transaction type
                if ($transactionName === 'Order') {
                    // Debit: Piutang, Credit: Pendapatan
                    JournalEntry::create([
                        'transaction_id' => $transaction->id,
                        'transaction_name' => $transactionName,
                        'dt' => $dt,
                        'account_id' => $piutangAccount->id,
                        'debit' => $amount,
                        'credit' => 0,
                        'desc' => "Piutang dari Order #{$transaction->no}",
                        'journal_entry_id' => $journalEntryId + 1,
                    ]);

                    JournalEntry::create([
                        'transaction_id' => $transaction->id,
                        'transaction_name' => $transactionName,
                        'dt' => $dt,
                        'account_id' => $pendapatanAccount->id,
                        'debit' => 0,
                        'credit' => $amount,
                        'desc' => "Pendapatan dari Order #{$transaction->no}",
                        'journal_entry_id' => $journalEntryId,
                    ]);
                } elseif ($transactionName === 'Payment') {
                    // Debit: Kas, Credit: Piutang
                    JournalEntry::create([
                        'transaction_id' => $transaction->id,
                        'transaction_name' => $transactionName,
                        'dt' => $dt,
                        'account_id' => $kasAccount->id,
                        'debit' => $amount,
                        'credit' => 0,
                        'desc' => "Pembayaran #{$transaction->no}",
                        'journal_entry_id' => $journalEntryId + 1,
                    ]);

                    JournalEntry::create([
                        'transaction_id' => $transaction->id,
                        'transaction_name' => $transactionName,
                        'dt' => $dt,
                        'account_id' => $piutangAccount->id,
                        'debit' => 0,
                        'credit' => $amount,
                        'desc' => "Pelunasan Piutang #{$transaction->no}",
                        'journal_entry_id' => $journalEntryId,
                    ]);
                } elseif ($transactionName === 'PurchaseOrder') {
                    // Debit: Persediaan/Biaya, Credit: Utang/Kas
                    JournalEntry::create([
                        'transaction_id' => $transaction->id,
                        'transaction_name' => $transactionName,
                        'dt' => $dt,
                        'account_id' => $persediaanAccount->id ?? $biayaAccount->id,
                        'debit' => $amount,
                        'credit' => 0,
                        'desc' => "Pembelian bahan #{$transaction->no}",
                        'journal_entry_id' => $journalEntryId + 1,
                    ]);

                    JournalEntry::create([
                        'transaction_id' => $transaction->id,
                        'transaction_name' => $transactionName,
                        'dt' => $dt,
                        'account_id' => $utangAccount->id,
                        'debit' => 0,
                        'credit' => $amount,
                        'desc' => "Utang PO #{$transaction->no}",
                        'journal_entry_id' => $journalEntryId,
                    ]);
                }

                $journalEntryId += 2;
            }
        }
    }
}
