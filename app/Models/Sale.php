<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        // 'project_id',
        'exp',
        'stipendio',
        'game_id',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function currentProject()
    {
        return $this->hasOne(Project::class)->where('status', 'ready');
    }

    public function procacciaProgetto(Game $game)
    {
        // If sale ha already a ReadyProject, dont create a new one
        if ($this->currentProject) {
            return null;
        }
        // create complexity and the value related to the complexity and calculated the waiting time
        $complexity = rand(10, 50);
        $value = $complexity * 100 + rand(0, 5000);
        // WARNING: They are found immediately, but if I increase the time the page does not reload; need to understand where the problem is.
        $waitingTime = ($complexity / $this->exp) * 0.5;

        // Creates a new project with a random name associated with the salt and the game after the waiting time
        sleep($waitingTime);
        $newProject = Project::create([
            'name'    => 'Progetto ' . strtoupper(substr(md5(rand()), 0, 5)),
            'complex' => $complexity,
            'value'   => $value,
            'game_id' => $game->id,
            'sale_id' => $this->id,
            'status'  => 'ready',
        ]);

        return $newProject;
    }
}
