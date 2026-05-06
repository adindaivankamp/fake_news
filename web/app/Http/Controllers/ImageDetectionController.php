<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Images;
use App\Models\TextRequest; 
use App\Models\ImageSearchResult;
use App\Models\ImageSearchResults;
use App\Models\Requests;
use Illuminate\Support\Facades\Http;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class ImageDetectionController extends Controller
{
    public function detect(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            // 1. Upload ke Cloudinary
            $file = $request->file('image');

        Log::info('Menerima file: ' . $file->getClientOriginalName());

        // 🔥 Setup Cloudinary
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);

        // 🔥 Upload langsung (NO MOVE)
        $upload = (new UploadApi())->upload($file->getRealPath(), [
            'folder' => 'fake_news_system'
        ]);

        $url = $upload['secure_url'];

        Log::info('Upload sukses: ' . $url);
            Log::info('Gambar berhasil diupload ke Cloudinary: ' . $url);
            // 2. Simpan ke tabel 'images'
            $imgRecord = Images::create([
                'file_path' => $url,    
                'original_filename' => $file->getClientOriginalName(),
                'uploaded_by' => auth()->id() ?? 1
            ]);

            // 3. Inisialisasi awal di tabel 'requests'
            $newReq = Requests::create([
                'image_id' => $imgRecord->id,
                'status' => 'processing'
            ]);

            // 4. Panggil API Python
            $response = Http::post('http://localhost:8000/image-detection', ['image_url' => $url]);
            Log::info($response->body());
            if ($response->successful()) {
                $res = $response->json();
                $links = collect($res['data'])
    ->pluck('link')
    ->toArray();
                // 5. Simpan ke tabel 'image_search_results'
                ImageSearchResults::create([
                    'request_id' => $newReq->id,
                    'source_url' => $links,
                    'similarity_score' => $res['similarity_score'],
                    'mean_date_score' => $res['avg_date_scaled'],
                ]);

                // 6. Update hasil akhir di tabel 'requests'
                $isHoax = $res['prediction'] == 1;
                $finalLabel = $isHoax ? 'HOAX' : 'FAKTA';
                
                // Menghitung persentase untuk tampilan bar di Figma
                $hoaxPercentage = round($res['confidence'] * 100);
                $factPercentage = 100 - $hoaxPercentage;

                $newReq->update([
                    'final_label' => $finalLabel,
                    'final_confidence' => $res['confidence'],
                    'status' => 'completed'
                ]);

                // 7. RETURN SESUAI FIGMA
                return response()->json([
                    'status' => 'success',
                    'verdict' => strtolower($finalLabel),
                    'confidence' => $hoaxPercentage,
                    'summary' => 'Analisis gambar menunjukkan indikasi ' . $finalLabel . ' dengan tingkat kepercayaan ' . $hoaxPercentage . '%.',
                    'data' => [
                        'indication' => $finalLabel,
                        'confidence_score' => [
                            'hoax' => $hoaxPercentage,
                            'fakta' => $factPercentage
                        ],
                        'image_preview' => $url,
                        'sources' => collect($res['data'])->map(function($item) {
                            return [
                                'title' => $item['title'],
                                'url' => $item['link'],
                                'date' => $item['date'] ?? 'N/A'
                            ];
                        })
                    ]
                ]);
            }

            return response()->json(['status' => 'error', 'message' => 'API Python Gagal'], 500);

        } catch (\Exception $e) {
            Log::error('Cloudinary ERROR: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}