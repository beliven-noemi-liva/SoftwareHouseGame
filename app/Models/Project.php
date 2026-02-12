<?php

namespace App\Models;

use App\Models\Dev;
use App\Models\Sale;    
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'complex',
        'sale_id',
        'value',
        'game_id',
        'status',
        'initial_complex',
    ];
    public function devs()
    {
        return $this->hasMany(Dev::class);
    }
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function game() {
        return $this->belongsTo(Game::class);
    }

    public function assignAvailableDevs()
    {
        // Se il progetto non è in stato "ready", esci
        if ($this->status !== 'ready') {
            return null;
        }

        // Trova i dev disponibili (senza progetto assegnato)
        $availableDevs = $this->game
            ->devs()
            ->whereNull('project_id')
            ->get();

        if ($availableDevs->isEmpty()) {
            return [
                'devs_assigned' => 0,
                'status_changed' => false,
                'new_project_created' => false,
            ];
        }

        // Assegna i dev al progetto
        foreach ($availableDevs as $dev) {
            $dev->update(['project_id' => $this->id]);
        }

        // Cambia lo stato del progetto a "in_progress" e salva la complessità iniziale
        $this->update([
            'status' => 'in_progress',
            'initial_complex' => $this->complex
        ]);
        // Il sale procaccia un nuovo progetto il wait rende lento il processo
        $this->sale->procacciaProgetto($this->game);
        return [
            'devs_assigned' => $availableDevs->count(),
            'status_changed' => true,
            'new_project_created' => true,
        ];
    }

    public function updateProgress()
    {
        // Se il progetto non è in stato "in_progress", esci
        if ($this->status !== 'in_progress') {
            return [
                'updated' => false,
                'message' => 'Project not in progress'
            ];
        }

        // Somma exp dei dev che lavorano
        $devs = $this->devs;
        $progressReduction = $devs->sum('exp');

        // Riduci complessità
        $newComplexity = max(0, $this->complex - $progressReduction);
        $this->complex = $newComplexity;

        // Se la complessità raggiunge 0, aggiorna il progetto a stato done e libera i dev
        if ($newComplexity <= 0) {
            $this->status = 'done';
            foreach ($devs as $dev) {
                $dev->update(['project_id' => null]);   
            }
            //aggiunto in seguito prima lo gestivo diversamente se le logiche non tornano.
            $game = $this->game;
            $game->patrimonio += $this->value;
            $game->save();
            $this->status = 'complete';
        }

        $this->save();
        
        return [
            'updated' => true,
            'complex' => $this->complex,
            'status' => $this->status,
            'progress_reduction' => $progressReduction,
            'devs_count' => $devs->count()
        ];
    }
}
