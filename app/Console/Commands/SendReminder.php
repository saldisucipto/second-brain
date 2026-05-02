<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FollowUp;
use App\Mail\ReminderMail;
use Illuminate\Support\Facades\Mail;

class SendReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $followUps = FollowUp::where('status', 'pending')
            ->where('reminder_at', '<=', now())
            ->get();

        foreach ($followUps as $item) {

            Mail::to('saldisucipto@gmail.com')->send(new ReminderMail($item));

            $item->update([
                'status' => 'done'
            ]);
        }
    }
}
