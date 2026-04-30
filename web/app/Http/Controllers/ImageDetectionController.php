<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\TextRequest; 
use App\Models\ImageSearchResult;
use Illuminate\Support\Facades\Http;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
            $upload = Cloudinary::upload($file->getRealPath(), ['folder' => 'fake_news_system']);
            $url = $upload->getSecurePath();

            // 2. Simpan ke tabel 'images'
            $imgRecord = Image::create([
                'file_path' => $url,
                'original_filename' => $file->getClientOriginalName(),
            ]);

            // 3. Inisialisasi awal di tabel 'requests'
            $newReq = TextRequest::create([
                'image_id' => $imgRecord->id,
                'status' => 'processing'
            ]);

            // 4. Panggil API Python
            $response = Http::post('http://localhost:8000/image-detection', ['image_url' => $url]);

            if ($response->successful()) {
                $res = $response->json();

                // 5. Simpan ke tabel 'image_search_results'
                ImageSearchResult::create([
                    'request_id' => $newReq->id,
                    'source_url' => $res['data'],
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
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}