<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Etransporttokens;
use App\Models\Efacturaparams;

class EtransportController extends Controller
{
    /**
     * Upload transport declaration (v1)
     */
    public function upload(Request $request)
    {
        try {
            $cif = $request->cif;
            $xmlContent = $request->xml_content;
            $version = $request->version ?? 1; // Default to version 1

            if (!$cif || !$xmlContent) {
                return response()->json(['error' => 'CIF and XML content are required'], 400);
            }

            // Get access token
            $token = $this->getAccessToken($cif);
            if (!$token) {
                return response()->json(['error' => 'Unable to obtain access token'], 401);
            }

            // Determine URL based on environment
            $baseUrl = env('APP_ENV') === 'production'
                ? env('ETRANSPORT_PROD_BASE_URL', 'https://api.anaf.ro/prod/ETRANSPORT/ws/v1')
                : env('ETRANSPORT_TEST_BASE_URL', 'https://api.anaf.ro/test/ETRANSPORT/ws/v1');

            $url = $baseUrl . '/upload/ETRANSP/' . $cif;
            if ($version == 2) {
                $url .= '/' . $version;
            }

            // Make the API call
            $response = Http::withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/xml',
                ])
                ->post($url, $xmlContent);

            if ($response->successful()) {
                $responseData = $response->json();

                // Log successful upload
                Log::info('ETRANSPORT upload successful', [
                    'cif' => $cif,
                    'version' => $version,
                    'response' => $responseData
                ]);

                return response()->json($responseData);
            } else {
                Log::error('ETRANSPORT upload failed', [
                    'cif' => $cif,
                    'version' => $version,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'error' => 'Upload failed',
                    'status' => $response->status(),
                    'message' => $response->body()
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('ETRANSPORT upload exception', [
                'error' => $e->getMessage(),
                'cif' => $request->cif ?? null
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * List transport declarations
     */
    public function lista(Request $request)
    {
        try {
            $cif = $request->cif;
            $zile = $request->zile ?? 30; // Default to 30 days

            if (!$cif || $zile < 1 || $zile > 60) {
                return response()->json(['error' => 'Valid CIF and zile (1-60) are required'], 400);
            }

            // Get access token
            $token = $this->getAccessToken($cif);
            if (!$token) {
                return response()->json(['error' => 'Unable to obtain access token'], 401);
            }

            // Determine URL based on environment
            $baseUrl = env('APP_ENV') === 'production'
                ? env('ETRANSPORT_PROD_BASE_URL', 'https://api.anaf.ro/prod/ETRANSPORT/ws/v1')
                : env('ETRANSPORT_TEST_BASE_URL', 'https://api.anaf.ro/test/ETRANSPORT/ws/v1');

            $url = $baseUrl . '/lista/' . $zile . '/' . $cif;

            // Make the API call
            $response = Http::withToken($token)->get($url);

            if ($response->successful()) {
                $responseData = $response->json();

                Log::info('ETRANSPORT lista successful', [
                    'cif' => $cif,
                    'zile' => $zile
                ]);

                return response()->json($responseData);
            } else {
                Log::error('ETRANSPORT lista failed', [
                    'cif' => $cif,
                    'zile' => $zile,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'error' => 'Lista request failed',
                    'status' => $response->status(),
                    'message' => $response->body()
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('ETRANSPORT lista exception', [
                'error' => $e->getMessage(),
                'cif' => $request->cif ?? null
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Check status of transport declaration
     */
    public function stareMesaj(Request $request)
    {
        try {
            $idIncarcare = $request->id_incarcare;

            if (!$idIncarcare) {
                return response()->json(['error' => 'id_incarcare is required'], 400);
            }

            // For status check, we need to use the CIF from the original upload
            // This might need to be stored in a database table
            // For now, we'll assume the CIF is provided or we need to modify this
            $cif = $request->cif; // This should be provided

            if (!$cif) {
                return response()->json(['error' => 'CIF is required for status check'], 400);
            }

            // Get access token
            $token = $this->getAccessToken($cif);
            if (!$token) {
                return response()->json(['error' => 'Unable to obtain access token'], 401);
            }

            // Determine URL based on environment
            $baseUrl = env('APP_ENV') === 'production'
                ? env('ETRANSPORT_PROD_BASE_URL', 'https://api.anaf.ro/prod/ETRANSPORT/ws/v1')
                : env('ETRANSPORT_TEST_BASE_URL', 'https://api.anaf.ro/test/ETRANSPORT/ws/v1');

            $url = $baseUrl . '/stareMesaj/' . $idIncarcare;

            // Make the API call
            $response = Http::withToken($token)->get($url);

            if ($response->successful()) {
                $responseData = $response->json();

                Log::info('ETRANSPORT stareMesaj successful', [
                    'id_incarcare' => $idIncarcare
                ]);

                return response()->json($responseData);
            } else {
                Log::error('ETRANSPORT stareMesaj failed', [
                    'id_incarcare' => $idIncarcare,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'error' => 'Stare mesaj request failed',
                    'status' => $response->status(),
                    'message' => $response->body()
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('ETRANSPORT stareMesaj exception', [
                'error' => $e->getMessage(),
                'id_incarcare' => $request->id_incarcare ?? null
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get transport information for transporters
     */
    public function info(Request $request)
    {
        try {
            $cuiOp = $request->cui_op; // Required
            $cuiDecl = $request->cui_decl; // Optional
            $uit = $request->uit; // Optional
            $refDecl = $request->ref_decl; // Optional

            if (!$cuiOp) {
                return response()->json(['error' => 'cui_op is required'], 400);
            }

            // Get access token
            $token = $this->getAccessToken($cuiOp);
            if (!$token) {
                return response()->json(['error' => 'Unable to obtain access token'], 401);
            }

            // Determine URL based on environment
            $baseUrl = env('APP_ENV') === 'production'
                ? env('ETRANSPORT_PROD_BASE_URL', 'https://api.anaf.ro/prod/ETRANSPORT/ws/v1')
                : env('ETRANSPORT_TEST_BASE_URL', 'https://api.anaf.ro/test/ETRANSPORT/ws/v1');

            $url = $baseUrl . '/info?' . http_build_query($params);

            // Make the API call
            $response = Http::withToken($token)->get($url);

            if ($response->successful()) {
                $responseData = $response->json();

                Log::info('ETRANSPORT info successful', [
                    'cui_op' => $cuiOp,
                    'cui_decl' => $cuiDecl,
                    'uit' => $uit,
                    'ref_decl' => $refDecl
                ]);

                return response()->json($responseData);
            } else {
                Log::error('ETRANSPORT info failed', [
                    'cui_op' => $cuiOp,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'error' => 'Info request failed',
                    'status' => $response->status(),
                    'message' => $response->body()
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('ETRANSPORT info exception', [
                'error' => $e->getMessage(),
                'cui_op' => $request->cui_op ?? null
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Get access token for ANAF ETRANSPORT API
     */
    private function getAccessToken($cif)
    {
        try {
            $companyId = session('company_id');

            // Check if we have a valid token for this CIF and company
            $token = Etransporttokens::where('cui', $cif)
                ->where('company_id', $companyId)
                ->where('data_obtinerii', '<=', Carbon::today())
                ->where('data_expirare', '>', Carbon::today())
                ->first();

            if ($token && $token->access_token) {
                Log::info('Using existing valid ETRANSPORT token', ['cif' => $cif]);
                return $token->access_token;
            }

            // If we have an expired token, try to refresh it
            if ($token && $token->refresh_token) {
                Log::info('Refreshing expired ETRANSPORT token', ['cif' => $cif]);

                $efacturaparams = Efacturaparams::first();
                if (!$efacturaparams) {
                    Log::error('Efacturaparams not configured for ETRANSPORT token refresh');
                    return null;
                }

                $response = Http::asForm()->post($efacturaparams->link_token, [
                    'client_id' => env('CLIENT_ANAF_ID'),
                    'client_secret' => env('CLIENT_ANAF_SECRET'),
                    'refresh_token' => $token->refresh_token,
                    'grant_type' => 'refresh_token',
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    // Update the token in database
                    $token->update([
                        'access_token' => $data['access_token'],
                        'refresh_token' => $data['refresh_token'] ?? $token->refresh_token,
                        'data_obtinerii' => Carbon::today(),
                        'data_expirare' => Carbon::today()->addDays($data['expires_in'] / 86400),
                    ]);

                    Log::info('ETRANSPORT token refreshed successfully', ['cif' => $cif]);
                    return $data['access_token'];
                } else {
                    Log::error('ETRANSPORT token refresh failed', [
                        'cif' => $cif,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                }
            }

            // If no token exists or refresh failed, we need to initiate OAuth2 flow
            // For now, create a placeholder record and return null
            // The actual OAuth2 flow should be implemented similar to e-factura
            Etransporttokens::updateOrCreate(
                ['cui' => $cif, 'company_id' => $companyId],
                ['cui' => $cif, 'company_id' => $companyId]
            );

            Log::warning('ETRANSPORT OAuth2 flow not implemented - token needs to be obtained manually', ['cif' => $cif]);
            return null;

        } catch (\Exception $e) {
            Log::error('Error getting ETRANSPORT access token', [
                'cif' => $cif,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}