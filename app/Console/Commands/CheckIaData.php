<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SeoAnalysis;

class CheckIaData extends Command
{
    protected $signature = 'ia:check {analysis_id}';
    protected $description = 'Vérifier les données IA sauvegardées';

    public function handle()
    {
        $analysis = SeoAnalysis::find($this->argument('analysis_id'));
        
        if (!$analysis) {
            $this->error('❌ Analyse non trouvée');
            return;
        }

        $this->info("📊 Données IA pour l'analyse #{$analysis->id}");
        $this->line("URL: {$analysis->page_url}");
        $this->line("Score IA: " . ($analysis->ai_score ?? 'NULL'));
        $this->line("Modèle utilisé: " . ($analysis->ai_model_used ?? 'NULL'));
        $this->line("Généré le: " . ($analysis->ai_generated_at ?? 'NULL'));
        $this->line("Analyse IA disponible: " . (!is_null($analysis->ai_score) || !empty($analysis->ai_raw_response) ? '✅ OUI' : '❌ NON'));
        
        $this->info("\n🔍 Problèmes identifiés (" . count($analysis->ai_issues ?? []) . "):");
        foreach ($analysis->ai_issues ?? [] as $index => $issue) {
            $this->line(" " . ($index + 1) . ". {$issue}");
        }
        
        $this->info("\n🎯 Priorités (" . count($analysis->ai_priorities ?? []) . "):");
        foreach ($analysis->ai_priorities ?? [] as $index => $priority) {
            $this->line(" " . ($index + 1) . ". {$priority}");
        }
        
        $this->info("\n✅ Checklist (" . count($analysis->ai_checklist ?? []) . "):");
        foreach ($analysis->ai_checklist ?? [] as $index => $item) {
            $this->line(" " . ($index + 1) . ". {$item}");
        }
        
        $this->info("\n📝 Réponse brute:");
        if ($analysis->ai_raw_response) {
            $this->line(substr($analysis->ai_raw_response, 0, 500) . '...');
            $this->line("Longueur totale: " . strlen($analysis->ai_raw_response) . " caractères");
        } else {
            $this->line('VIDE');
        }
        
        $this->info("\n🔄 Ancien format (ai_summary):");
        if ($analysis->ai_summary) {
            $this->line("Type: " . gettype($analysis->ai_summary));
            if (is_array($analysis->ai_summary)) {
                $this->line("Clés: " . implode(', ', array_keys($analysis->ai_summary)));
            }
        } else {
            $this->line('VIDE');
        }
    }
}