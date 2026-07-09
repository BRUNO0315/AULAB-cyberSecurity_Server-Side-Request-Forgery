<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PreviewController extends Controller
{
    /**
     * Mostra il form.
     */
    public function index()
    {
        return view('preview');
    }

    /**
     * Recupera l'anteprima del sito.
     */
    public function show(Request $request)
    {
        // Validazione URL
        $request->validate([
            'url' => [
                'required',
                'url',
                'max:2048',
            ],
        ]);

        try {

            // Richiesta HTTP con timeout
            $response = Http::timeout(5)
                ->connectTimeout(3)
                ->get($request->url);

            // Controlla che la richiesta sia andata a buon fine
            if (!$response->successful()) {
                return back()->withErrors([
                    'url' => 'Impossibile raggiungere il sito.'
                ]);
            }

            // Controllo del tipo di contenuto
            $contentType = $response->header('Content-Type');

            if (!$contentType || !str_contains($contentType, 'text/html')) {
                return back()->withErrors([
                    'url' => 'L\'URL non restituisce una pagina HTML.'
                ]);
            }

            $html = $response->body();

            // Titolo
            preg_match('/<title>(.*?)<\/title>/is', $html, $title);

            // Open Graph
            preg_match('/<meta\s+property="og:title"\s+content="([^"]*)"/i', $html, $ogTitle);
            preg_match('/<meta\s+property="og:description"\s+content="([^"]*)"/i', $html, $ogDescription);
            preg_match('/<meta\s+property="og:image"\s+content="([^"]*)"/i', $html, $ogImage);

            $preview = [
                'title' => $ogTitle[1] ?? ($title[1] ?? 'Nessun titolo trovato'),
                'description' => $ogDescription[1] ?? 'Nessuna descrizione disponibile.',
                'image' => $ogImage[1] ?? null,
                'url' => $request->url,
            ];

            return view('preview', compact('preview'));

        } catch (\Exception $e) {

            return back()->withErrors([
                'url' => 'Errore durante il recupero dell\'anteprima.'
            ]);

        }
    }
}