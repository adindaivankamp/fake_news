<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Images;
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
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            // 1. Upload ke Cloudinary
            $file = $request->file('gambar');

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
            log::info('Request baru dibuat dengan ID: ' . $url);
            // 4. Panggil API Python
        //     $response = Http::timeout(300)
        // ->post('http://localhost:8004/image-detection', [
        //     'image_url' => $url
        // ]);
            // Log::info($response->body());
            if (true) {
                // $res = $response->json();
                $res = [
    "similarity_score" => 0.2,
    "avg_date_scaled" => 0.5245532759761746,
    "prediction" => 0,
    "confidence" => 0.62,
    "data" => [
        [
            "link" => "https://www.knoxnews.com/story/entertainment/dining/2018/02/27/new-south-knoxville-restaurant-burger-boys-opens-chapman-highway/376340002/",
            "thumbnail" => "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcSzv5K0XQS7GYuUB1nVDW3bYP_fvGnMJPC8sydG8G_5lDhI76o0",
            "title" => "New South Knoxville restaurant, Burger Boys, opens on ...",
            "date" => "2018-02-27",
            "img_distance" => 0.9868010878562927,
            "pred_label" => "not similar",
            "date_diff" => 3003,
            "date_scaled" => 1.385837193911317
        ],
        [
            "link" => "https://www.gettyimages.fi/photos/burger-king-drive-thru",
            "thumbnail" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRVL9EsYnNCvgSui7IGw-W0-5JP9F3gWWupwim8NFpnmE1BbXG",
            "title" => "Burger King Drive Thru Stock Photos",
            "date" => "2023-11-22",
            "img_distance" => 0.3931922912597656,
            "pred_label" => "similar",
            "date_diff" => 909,
            "date_scaled" => 0.0
        ]
    ]
];
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
                if ($isHoax) {
                    $hoaxPercentage = round($res['confidence'] * 100);
                    $factPercentage = 100 - $hoaxPercentage;
                } else {
                    $factPercentage = round($res['confidence'] * 100);
                    $hoaxPercentage  = 100 - $factPercentage;
                }
                
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
                    'sources' => $links,

                    'data' => [
                        'indication' => $finalLabel,
                        'confidence_score' => [
                            'hoax' => $hoaxPercentage,
                            'fact' => $factPercentage
                        ],
                        'image_preview' => $url
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