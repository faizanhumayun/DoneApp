<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class CreateDefaultWorkflows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflows:create-defaults';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default built-in workflows for all companies that don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating default workflows for companies...');

        $companies = Company::all();
        $created = 0;

        foreach ($companies as $company) {
            // Check if company already has built-in workflows
            $hasBuiltIn = $company->workflows()->where('is_builtin', true)->exists();

            if (!$hasBuiltIn) {
                $company->createDefaultWorkflows();
                $created++;
                $this->info("✓ Created default workflows for: {$company->name}");
            } else {
                $this->comment("- Skipped {$company->name} (already has built-in workflows)");
            }
        }

        $this->newLine();
        $this->info("Done! Created default workflows for {$created} " . str('company')->plural($created));

        return self::SUCCESS;
    }
}
