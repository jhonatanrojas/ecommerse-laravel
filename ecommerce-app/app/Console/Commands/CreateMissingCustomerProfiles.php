<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateMissingCustomerProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:create-missing-profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea perfiles de cliente para usuarios que no tienen uno';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Buscando usuarios sin perfil de cliente...');

        $usersWithoutCustomer = User::doesntHave('customer')->get();

        if ($usersWithoutCustomer->isEmpty()) {
            $this->info('Todos los usuarios ya tienen perfil de cliente.');
            return Command::SUCCESS;
        }

        $this->info("Se encontraron {$usersWithoutCustomer->count()} usuarios sin perfil de cliente.");

        $bar = $this->output->createProgressBar($usersWithoutCustomer->count());
        $bar->start();

        foreach ($usersWithoutCustomer as $user) {
            $user->customer()->create([
                'phone' => $user->phone,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('¡Perfiles de cliente creados exitosamente!');

        return Command::SUCCESS;
    }
}
