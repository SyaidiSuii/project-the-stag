<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RecommendationService;

class AiServiceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:status 
                           {--json : Output as JSON}
                           {--detailed : Show detailed information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check AI recommendation service status and health';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $recommendationService = app(RecommendationService::class);

        try {
            // Get service health check
            $isHealthy = $recommendationService->healthCheck();
            
            // Get detailed service status
            $serviceStatus = $recommendationService->getServiceStatus();
            
            // Prepare status information
            $status = [
                'service_healthy' => $isHealthy,
                'timestamp' => now()->toISOString(),
                'service_url' => config('services.ai_recommender.base_url'),
                'enabled' => config('services.ai_recommender.enabled', true),
            ];
            
            // Add detailed information if requested
            if ($this->option('detailed')) {
                $status = array_merge($status, $serviceStatus);
            }
            
            // Output as JSON if requested
            if ($this->option('json')) {
                $this->line(json_encode($status, JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            }
            
            // Console table output
            $this->info('🤖 AI Recommendation Service Status');
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            
            // Service health
            $healthIcon = $isHealthy ? '✅' : '❌';
            $healthText = $isHealthy ? 'HEALTHY' : 'UNHEALTHY';
            $this->line("Status: {$healthIcon} {$healthText}");
            
            $this->line("Service URL: {$status['service_url']}");
            $this->line("Enabled: " . ($status['enabled'] ? '✅ Yes' : '❌ No'));
            
            if ($this->option('detailed') && isset($serviceStatus['model_trained'])) {
                $this->line('');
                $this->info('📊 Model Information:');
                
                $modelIcon = $serviceStatus['model_trained'] ? '✅' : '❌';
                $modelText = $serviceStatus['model_trained'] ? 'TRAINED' : 'NOT TRAINED';
                $this->line("Model Status: {$modelIcon} {$modelText}");
                
                if (isset($serviceStatus['last_training'])) {
                    $this->line("Last Training: {$serviceStatus['last_training']}");
                }
                
                if (isset($serviceStatus['available_menu_items'])) {
                    $this->line("Available Menu Items: {$serviceStatus['available_menu_items']}");
                }
                
                if (isset($serviceStatus['training_records_available'])) {
                    $this->line("Training Records: {$serviceStatus['training_records_available']}");
                }
                
                if (isset($serviceStatus['model_version'])) {
                    $this->line("Model Version: {$serviceStatus['model_version']}");
                }
            }
            
            $this->line("Checked at: {$status['timestamp']}");
            
            return $isHealthy ? Command::SUCCESS : Command::FAILURE;
            
        } catch (\Exception $e) {
            if ($this->option('json')) {
                $this->line(json_encode([
                    'service_healthy' => false,
                    'error' => $e->getMessage(),
                    'timestamp' => now()->toISOString()
                ], JSON_PRETTY_PRINT));
            } else {
                $this->error('❌ Failed to check AI service status');
                $this->error("Error: {$e->getMessage()}");
            }
            
            return Command::FAILURE;
        }
    }
}