<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhoisService;

class WhoisController extends Controller
{
    public function testWhois(WhoisService $whois)
{
    $data = $whois->lookup('mada-diary-tour.com');
    return response()->json($data);
}
}
