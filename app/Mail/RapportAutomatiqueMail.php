<?php

namespace App\Mail;

use App\Models\RapportAuto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RapportAutomatiqueMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RapportAuto $rapport,
        public array $donnees,
        public string $debut,
        public string $fin,
        public string $frequence,
    ) {}

    public function build(): self
    {
        $sujet = match ($this->frequence) {
            'quotidien' => 'Rapport quotidien — ' . now()->format('d/m/Y'),
            'hebdomadaire' => 'Rapport hebdomadaire — semaine du ' . $this->debut,
            'mensuel' => 'Rapport mensuel — ' . now()->subMonth()->format('F Y'),
            default => 'Rapport de satisfaction',
        };

        return $this
            ->subject($sujet)
            ->view('emails.rapport-automatique');
    }
}