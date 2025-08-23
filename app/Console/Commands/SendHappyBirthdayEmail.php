<?php
namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Mail\HappyBirthday;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;


class SendHappyBirthdayEmail extends Command
{
    protected $signature = 'app:send-happy-birthday-email';


    protected $description = 'Sending happy birthday email';


    public function handle() {


        // Dapatkan pengguna dengan hari lahir hari ini
        $users = User::whereMonth('dob', now()->month)
            ->whereDay('dob', now()->day)->get();


        // progress bar
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();


            // Menghantar email kepada pengguna
            foreach ($users as $user) {
                Mail::to($user->email)->send(new HappyBirthday($user));
                $bar->advance();
            }


        $bar->finish();
        $this->newLine(2);
        $this->info('Birthday emails sent successfully to ' . $users->count() . ' users.');
    }
}