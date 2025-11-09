<?php

namespace App\Events;

use App\Models\TableQrcode;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QRSessionCompletedEvent
{
    use Dispatchable, SerializesModels;

    public $tableQrcode;
    public $completedAt;

    /**
     * Create a new event instance.
     *
     * @param TableQrcode $tableQrcode
     * @return void
     */
    public function __construct(TableQrcode $tableQrcode)
    {
        $this->tableQrcode = $tableQrcode;
        $this->completedAt = now();
    }
}