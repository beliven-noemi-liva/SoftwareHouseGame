<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Dev;
use App\Models\Sale;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
        'state',
        'patrimonio',
        'last_update_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function devs()
    {
        return $this->hasMany(Dev::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function processEconomyTick()
    {
        // Processa progetti completati (status = done) e aggiungi valore al patrimonio, cambia status a complete      
        $doneProjects = $this->projects()->where('status', 'done')->get();
        $ProjectMoney = 0;
        foreach ($doneProjects as $project) {
            $ProjectMoney += $project->value;
            $project->status = 'complete';
            $project->save();
        }

        // Calcola stipendi totali di dev e sale
        $totalSalaries = $this->sales()->sum('stipendio') + $this->devs()->sum('stipendio');

        // Sottrai stipendi
        $this->patrimonio = $this->patrimonio - $totalSalaries;

        // Aggiungi soldi dei progetti
        $this->patrimonio = $this->patrimonio + $ProjectMoney;
        // Controlla se il gioco è finito
        if ($this->patrimonio <= 0) {
            $this->state = 'finish';
        }

        //verifico che i sales procaccino progetti quando il gioco è ready
        if (in_array($this->state, ['in_progress'])) {
            foreach ($this->sales as $sale) {
                $sale->procacciaProgetto($this);
            }
        }
        $this->last_update_at = now();
        $this->save();

        return [
            'total_salaries' => $totalSalaries,
            'collected' => $ProjectMoney,
            'patrimonio' => $this->patrimonio,
            'state' => $this->state,
        ];
    }
}