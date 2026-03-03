<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        // update the project
        $inProgressProjects = $this->projects()->where('status', 'in_progress')->get();
        foreach ($inProgressProjects as $proj) {
            $proj->updateProgress();
            $proj->save();
        }
        // Add the value of done project (status = done) at the patrimonio and change the status in complete
        $doneProjects = $this->projects()->where('status', 'done')->get();
        $ProjectMoney = 0;
        foreach ($doneProjects as $project) {
            $ProjectMoney += $project->value;
            $project->status = 'complete';
            $project->save();
        }
        $this->patrimonio = $this->patrimonio + $ProjectMoney;

        // Update patrimonio with salaries of devs and sales
        $totalSalaries = $this->sales()->sum('stipendio') + $this->devs()->sum('stipendio');
        $this->patrimonio = $this->patrimonio - $totalSalaries;

        // check if the game is over
        if ($this->patrimonio <= 0) {
            $this->state = 'finish';
        }

        // check if sales get new projects
        if (in_array($this->state, ['in_progress'])) {
            foreach ($this->sales as $sale) {
                $sale->procacciaProgetto($this);
            }
        }
        $this->last_update_at = now();
        $this->save();

        return [
            'total_salaries' => $totalSalaries,
            'collected'      => $ProjectMoney,
            'patrimonio'     => $this->patrimonio,
            'state'          => $this->state,
        ];
    }
}
