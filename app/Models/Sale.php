<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Game;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'project_id',
        'exp',
        'stipendio',
        'game_id',
    ];
    
    public function projects() {
        return $this->hasMany(Project::class);
    }
    public function currentProject() {
        return $this->hasOne(Project::class)->where('status', 'ready');
    }
    public function procacciaProgetto(Game $game) {
        // Se il sale ha già un progetto ready, non fa nulla
        if ($this->currentProject) {
            return null;
        }
        // Crea complessità del progetto e value in base alla complessità e calcola il tempo d'attesa 
        $complexity = rand(10, 50);
        $value = $complexity * 100 + rand(0, 5000);
        //ATTENZIONE: risulta che li trovano subito, ma se metto più tempo non mi rigenera la pagina, capire dove é il problema
        $tempoAttesa = ($complexity / $this->exp)*0.5;

        // Crea un nuovo progetto con nome casuale associato al sale e al gioco dopo il tempo di attesa
        sleep($tempoAttesa);
        $newProject = Project::create([
            'name' => 'Progetto ' . strtoupper(substr(md5(rand()), 0, 5)),
            'complex' => $complexity,
            'value' => $value,
            'game_id' => $game->id,
            'sale_id' => $this->id,
            'status' => 'ready',
        ]);

        return $newProject;
    }
}
