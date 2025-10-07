<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RecommendationService;

class AiRetrain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:retrain 
                           {--force : Force retrain even if service is unhealthy}
                           {--json : Output as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force retrain AI recommendation model with latest data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $recommendationService = app(RecommendationService::class);

        try {
            // Check service health first unless forced
            if (!$this->option('force')) {
                $this->info('🔍 Checking AI service health...');
                
                $isHealthy = $recommendationService->healthCheck();
                
                if (!$isHealthy) {
                    $this->error('❌ AI service is not healthy. Use --force to proceed anyway.');
                    return Command::FAILURE;
                }
                
                $this->info('✅ AI service is healthy');
            }
            
            // Start retraining
            $this->info('🚀 Starting AI model retraining...');
            $this->line('This may take a few seconds...');
            
            // Show progress bar
            $bar = $this->output->createProgressBar(3);
            $bar->start();
            
            $bar->advance();
            sleep(1); // Simulate progress
            
            // Force retrain
            $result = $recommendationService->forceRetrain();
            
            $bar->advance();
            sleep(1);
            
            $bar->finish();
            $this->line(''); // New line after progress bar
            
            // Output results
            if ($this->option('json')) {
                $this->line(json_encode([
                    'success' => $result['success'],
                    'message' => $result['message'],
                    'result' => $result['result'] ?? null,
                    'timestamp' => now()->toISOString()
                ], JSON_PRETTY_PRINT));
            } else {
                if ($result['success']) {
                    $this->info('✅ ' . $result['message']);
                    
                    if (isset($result['result']['records_used'])) {
                        $this->line("📊 Training records used: {$result['result']['records_used']}");
                    }
                    
                    if (isset($result['result']['status'])) {
                        $this->line("📈 Training status: {$result['result']['status']}");
                    }
                    
                    $this->line("⏰ Completed at: " . now()->format('Y-m-d H:i:s'));
                } else {
                    $this->error('❌ ' . $result['message']);
                    
                    if (isset($result['error'])) {
                        $this->error("Error details: {$result['error']}");
                    }
                }
            }
            
            return $result['success'] ? Command::SUCCESS : Command::FAILURE;
            
        } catch (\Exception $e) {
            if ($this->option('json')) {
                $this->line(json_encode([
                    'success' => false,
                    'message' => 'AI retrain failed',
                    'error' => $e->getMessage(),
                    'timestamp' => now()->toISOString()
                ], JSON_PRETTY_PRINT));
            } else {
                $this->error('❌ AI model retrain failed');
                $this->error("Error: {$e->getMessage()}");
            }
            
            return Command::FAILURE;
        }
    }
}