<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateQRImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:generate {--session-code= : Generate QR for specific session code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR code images for table sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sessionCode = $this->option('session-code');
        
        if ($sessionCode) {
            // Generate QR for specific session
            $session = \App\Models\TableQrcode::where('session_code', $sessionCode)->first();
            
            if (!$session) {
                $this->error("Session with code '{$sessionCode}' not found.");
                return 1;
            }
            
            $this->info("Generating QR images for session: {$sessionCode}");
            $session->generateQRCode();
            $this->info("✅ QR images generated successfully!");
            $this->line("PNG: {$session->qr_code_png}");
            $this->line("SVG: {$session->qr_code_svg}");
            
        } else {
            // Generate QR for all active sessions without images
            $sessions = \App\Models\TableQrcode::where('status', 'active')
                ->where('expires_at', '>', now())
                ->whereNull('qr_code_png')
                ->get();
            
            if ($sessions->isEmpty()) {
                $this->info("No active sessions need QR image generation.");
                return 0;
            }
            
            $this->info("Found {$sessions->count()} sessions that need QR images...");
            $bar = $this->output->createProgressBar($sessions->count());
            
            foreach ($sessions as $session) {
                $session->generateQRCode();
                $bar->advance();
            }
            
            $bar->finish();
            $this->line('');
            $this->info("✅ Generated QR images for {$sessions->count()} sessions!");
        }
        
        return 0;
    }
}
