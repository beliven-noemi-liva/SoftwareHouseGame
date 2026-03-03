<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function assignAvailableDevs()
    {
        // check if the projcet's state is ready
        if ($this->status !== 'ready') {
            return null;
        }

        // find the available devs
        $availableDevs = $this->game
            ->devs()
            ->whereNull('project_id')
            ->get();

        if ($availableDevs->isEmpty()) {
            return [
                'devs_assigned'       => 0,
                'status_changed'      => false,
                'new_project_created' => false,
            ];
        }

        // assign the devs
        foreach ($availableDevs as $dev) {
            $dev->update(['project_id' => $this->id]);
        }

        // change the project's state and save the initial complex
        $this->update([
            'status'          => 'in_progress',
            'initial_complex' => $this->complex,
        ]);

        // remove the procaccia method from here because it was also in the economy tick and here was slow
        // $this->sale->procacciaProgetto($this->game);
        return [
            'devs_assigned'       => $availableDevs->count(),
            'status_changed'      => true,
            'new_project_created' => true,
        ];
    }

    public function updateProgress()
    {
        // check if the project's state is in_progress
        if ($this->status !== 'in_progress') {
            return [
                'updated' => false,
                'message' => 'Project not in progress',
            ];
        }

        // sum the dev's exp
        $devs = $this->devs;
        $progressReduction = $devs->sum('exp');

        // decrease the complexity
        $newComplexity = max(0, $this->complex - $progressReduction);
        $this->complex = $newComplexity;

        // when the complexity is 0, change the project's state and remove the devs
        if ($newComplexity <= 0) {
            $this->status = 'done';
            foreach ($devs as $dev) {
                $dev->update(['project_id' => null]);
            }

            // update the patrimonio
            $game = $this->game;
            $game->patrimonio += $this->value;
            $game->save();
            $this->status = 'complete';
        }

        $this->save();

        return [
            'updated'            => true,
            'complex'            => $this->complex,
            'status'             => $this->status,
            'progress_reduction' => $progressReduction,
            'devs_count'         => $devs->count(),
        ];
    }
}
