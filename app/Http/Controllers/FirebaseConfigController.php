<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FirebaseConfigController extends Controller
{
    /**
     * Get Firebase configuration for frontend
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfig()
    {
        try {
            // Check if Firebase auth is enabled
            if (!config('firebase.auth.enabled')) {
                return response()->json([
                    'enabled' => false,
                    'message' => 'Firebase authentication is disabled'
                ]);
            }

            // Check if Google provider is enabled
            if (!config('firebase.auth.providers.google.enabled')) {
                return response()->json([
                    'enabled' => false,
                    'message' => 'Google authentication is disabled'
                ]);
            }

            $config = config('firebase.web_config');
            
            // Validate required configuration fields
            $requiredFields = ['apiKey', 'authDomain', 'projectId'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (empty($config[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                Log::error('Missing Firebase configuration fields', [
                    'missing_fields' => $missingFields,
                    'config' => $config
                ]);
                
                return response()->json([
                    'enabled' => false,
                    'message' => 'Firebase configuration is incomplete. Missing: ' . implode(', ', $missingFields)
                ], 500);
            }

            // Remove null values from config
            $config = array_filter($config, function($value) {
                return $value !== null && $value !== '';
            });

            return response()->json([
                'enabled' => true,
                'config' => $config,
                'providers' => [
                    'google' => config('firebase.auth.providers.google.enabled', false)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Firebase config error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'enabled' => false,
                'message' => 'Failed to load Firebase configuration'
            ], 500);
        }
    }
} 