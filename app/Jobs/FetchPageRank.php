<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\SeoAnalysis;

class FetchPageRank implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $analysis;

    public function __construct(SeoAnalysis $analysis)
    {
        $this->analysis = $analysis;
    }

    public function handle()
{
    try {
        $domain = parse_url($this->analysis->page_url, PHP_URL_HOST);
        $apiKey = config('services.openpagerank.key');

        \Log::info('🔍 FetchPageRank avec SSL désactivé', ['domain' => $domain]);

        $response = Http::timeout(10)
            ->withOptions([
                'verify' => false,
            ])
            ->withHeaders(['API-OPR' => $apiKey])
            ->get('https://openpagerank.com/api/v1.0/getPageRank', [
                'domains[]' => $domain
            ]);

        \Log::info('📡 API Response', ['status' => $response->status()]);

        if ($response->successful()) {
            $data = $response->json();
            
            // ⬅️ EXTRACTION DES DEUX CHAMPS
            $rank = $data['response'][0]['page_rank_decimal'] ?? null;
            $global = $data['response'][0]['rank'] ?? null;
            
            \Log::info('✅ PageRank success', [
                'rank' => $rank,
                'global' => $global  // ⬅️ AJOUTEZ CETTE LIGNE
            ]);
            
            // ⬅️ SAUVEGARDE DES DEUX CHAMPS
            $this->analysis->update([
                'page_rank' => $rank,
                'page_rank_global' => $global  // ⬅️ AJOUTEZ CETTE LIGNE
            ]);
            
            \Log::info('💾 PageRank sauvegardé', [
                'analysis_id' => $this->analysis->id,
                'page_rank' => $rank,
                'page_rank_global' => $global
            ]);
        } else {
            \Log::warning('❌ PageRank API failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        }
        
    } catch (\Exception $e) {
        \Log::error('💥 FetchPageRank exception', ['message' => $e->getMessage()]);
    }
}
}
