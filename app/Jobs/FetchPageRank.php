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
        $domain = parse_url($this->analysis->page_url, PHP_URL_HOST);
        $apiKey = config('services.openpagerank.key');

        $response = Http::withHeaders([
            'API-OPR' => $apiKey
        ])->get('https://openpagerank.com/api/v1.0/getPageRank', [
            'domains[]' => $domain
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $rank = $data['response'][0]['page_rank_decimal'] ?? null;

            $this->analysis->update([
                'page_rank' => $rank
            ]);
        } else {
            \Log::warning('❌ OpenPageRank failed', ['status' => $response->status(), 'body' => $response->body()]);
        }
    }
}
