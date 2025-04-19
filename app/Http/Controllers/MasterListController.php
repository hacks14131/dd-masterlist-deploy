<?php

namespace App\Http\Controllers;

use App\Models\Masterlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LengthException;

class MasterlistController extends Controller
{
    // Your existing controller methods...

    public function findDuplicates(Request $request)
    {
        $search = $request->input('search', '');
        $threshold = $request->input('threshold', 80); // Default similarity threshold (%)
        $potentialDuplicates = [];

        // If search query is provided, find potential duplicates
        if ($search) {
            // Find the record that matches closest to the search query
            $searchTerms = explode(' ', $search);
            $query = Masterlist::query();
            
            foreach ($searchTerms as $term) {
                $query->where(function($q) use ($term) {
                    $q->where('firstName', 'LIKE', "%$term%")
                      ->orWhere('middleName', 'LIKE', "%$term%")
                      ->orWhere('familyName', 'LIKE', "%$term%");
                });
            }
            
            $baseRecords = $query->get();
            
            // If we found some potential matches, look for similar records
            if ($baseRecords->count() > 0) {
                foreach ($baseRecords as $baseRecord) {
                    // Get all records to compare against
                    $allRecords = Masterlist::where('id', '!=', $baseRecord->id)->get();
                    
                    foreach ($allRecords as $record) {
                        // Calculate similarity based on Levenshtein distance
                        $firstNameSimilarity = $this->calculateSimilarity($baseRecord->firstName, $record->firstName);
                        $middleNameSimilarity = $this->calculateSimilarity($baseRecord->middleName, $record->middleName);
                        $familyNameSimilarity = $this->calculateSimilarity($baseRecord->familyName, $record->familyName);
                        
                        // Calculate overall similarity (weighted average)
                        $overallSimilarity = ($firstNameSimilarity * 0.4) + ($middleNameSimilarity * 0.2) + ($familyNameSimilarity * 0.4);
                        
                        // Add to potential duplicates if similarity is above threshold
                        if ($overallSimilarity >= $threshold / 100) {
                            $potentialDuplicates[] = [
                                'original' => $baseRecord,
                                'duplicate' => $record,
                                'similarity' => round($overallSimilarity * 100, 2),
                                'details' => [
                                    'firstName' => $firstNameSimilarity * 100,
                                    'middleName' => $middleNameSimilarity * 100,
                                    'familyName' => $familyNameSimilarity * 100
                                ]
                            ];
                        }
                    }
                }
            }
            
            // Sort by similarity (highest first)
            usort($potentialDuplicates, function($a, $b) {
                return $b['similarity'] <=> $a['similarity'];
            });
            
            // Remove duplicates (keeping only unique pairs)
            $uniquePairs = [];
            $processedPairs = [];
            
            foreach ($potentialDuplicates as $pair) {
                $key1 = $pair['original']->id . '-' . $pair['duplicate']->id;
                $key2 = $pair['duplicate']->id . '-' . $pair['original']->id;
                
                if (!in_array($key1, $processedPairs) && !in_array($key2, $processedPairs)) {
                    $uniquePairs[] = $pair;
                    $processedPairs[] = $key1;
                }
            }
            
            $potentialDuplicates = $uniquePairs;
        }
        return view('pages.find-duplicates', [
            'potentialDuplicates' => $potentialDuplicates,
            'search' => $search,
            'threshold' => $threshold
        ]);
    }
    
    /**
     * Calculate similarity between two strings (0-1 scale)
     */
    private function calculateSimilarity($str1, $str2) 
    {
        if (empty($str1) && empty($str2)) {
            return 1.0; // Both empty means they're identical
        }
        
        if (empty($str1) || empty($str2)) {
            return 0.0; // One empty and one not means completely different
        }
        
        $str1 = strtolower($str1);
        $str2 = strtolower($str2);
        
        // Maximum possible Levenshtein distance
        $maxLen = max(strlen($str1), strlen($str2));
        if ($maxLen === 0) {
            return 1.0; // Both strings are empty
        }
        
        // Calculate the Levenshtein distance
        $levenshteinDist = levenshtein($str1, $str2);
        
        // Convert to similarity (1 - normalized distance)
        return 1 - ($levenshteinDist / $maxLen);
    }
    
    /**
     * Scan the entire database for potential duplicates
     */
    public function scanForDuplicates(Request $request)
    {
        $threshold = $request->input('threshold', 80); // Default similarity threshold (%)
        $potentialDuplicates = [];
        
        // Get all records
        $allRecords = Masterlist::all();
        $processedPairs = [];
        
        // Compare each record with every other record
        foreach ($allRecords as $record1) {
            foreach ($allRecords as $record2) {
                // Skip comparing a record with itself
                if ($record1->id === $record2->id) {
                    continue;
                }
                
                // Skip if this pair has already been processed (in reverse order)
                $key1 = $record1->id . '-' . $record2->id;
                $key2 = $record2->id . '-' . $record1->id;
                
                if (in_array($key1, $processedPairs) || in_array($key2, $processedPairs)) {
                    continue;
                }
                
                // Calculate similarity
                $firstNameSimilarity = $this->calculateSimilarity($record1->firstName, $record2->firstName);
                $middleNameSimilarity = $this->calculateSimilarity($record1->middleName, $record2->middleName);
                $familyNameSimilarity = $this->calculateSimilarity($record1->familyName, $record2->familyName);
                
                // Calculate overall similarity (weighted average)
                $overallSimilarity = ($firstNameSimilarity * 0.4) + ($middleNameSimilarity * 0.2) + ($familyNameSimilarity * 0.4);
                
                // Add to potential duplicates if similarity is above threshold
                if ($overallSimilarity >= $threshold / 100) {
                    $potentialDuplicates[] = [
                        'original' => $record1,
                        'duplicate' => $record2,
                        'similarity' => round($overallSimilarity * 100, 2),
                        'details' => [
                            'firstName' => round($firstNameSimilarity * 100, 2),
                            'middleName' => round($middleNameSimilarity * 100, 2),
                            'familyName' => round($familyNameSimilarity * 100, 2)
                        ]
                    ];
                    
                    // Mark this pair as processed
                    $processedPairs[] = $key1;
                }
            }
        }
        
        // Sort by similarity (highest first)
        usort($potentialDuplicates, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });
        
        return view('pages.find-duplicates', [
            'potentialDuplicates' => $potentialDuplicates,
            'threshold' => $threshold,
            'search' => '',
            'fullScan' => true
        ]);
    }
}