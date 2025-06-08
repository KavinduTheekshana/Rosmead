<?php

namespace App\Http\Controllers;

use App\Models\Maintain;
use Illuminate\Http\Request;

class MaintainController extends Controller
{
    public function print(Request $request, $recordId)
    {
        $record = Maintain::findOrFail($recordId);
        
        // Ensure user can only access their own records
        if ($record->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        $filterYear = $request->get('year', now()->year);
        
        // Get filtered data
        $filteredComments = $record->commentsForYear($filterYear)->get()->map(function($comment) {
            return [
                'date' => $comment->date->format('Y-m-d'),
                'comment' => strip_tags($comment->comment ?? '')
            ];
        })->toArray();

        $filteredGuardChecks = $record->fireDoorGuardChecksForYear($filterYear)->get()->map(function($check) {
            return [
                'checked_date' => $check->checked_date->format('Y-m-d'),
                'notes' => strip_tags($check->notes ?? '')
            ];
        })->toArray();

        $filteredBatteryReplacements = $record->fireDoorGuardBatteryReplacementsForYear($filterYear)->get()->map(function($replacement) {
            return [
                'replaced_date' => $replacement->replaced_date->format('Y-m-d'),
                'notes' => strip_tags($replacement->notes ?? '')
            ];
        })->toArray();

        // Simple data structure
        $data = [
            'record' => [
                'room_number' => $record->room_number,
                'bed_type' => $record->bed_type ?? '',
                'has_bed_rails' => $record->has_bed_rails ? 1 : 0,
                'has_bed_rail_covers' => $record->has_bed_rail_covers ? 1 : 0,
                'mattress_type' => $record->mattress_type ?? '',
                'air_mattress_machine_type' => $record->air_mattress_machine_type ?? '',
                'has_sensor_mat' => $record->has_sensor_mat ? 1 : 0,
                'has_ceiling_light' => $record->has_ceiling_light ? 1 : 0,
                'has_ceiling_fan' => $record->has_ceiling_fan ? 1 : 0,
                'has_wall_light' => $record->has_wall_light ? 1 : 0,
                'has_bathroom_light' => $record->has_bathroom_light ? 1 : 0,
                'has_ac' => $record->has_ac ? 1 : 0,
                'has_door_lock' => $record->has_door_lock ? 1 : 0,
                'door_lock_pin' => $record->door_lock_pin ?? '',
                'has_tv' => $record->has_tv ? 1 : 0,
                'tv_model' => $record->tv_model ?? '',
                'has_tv_remote' => $record->has_tv_remote ? 1 : 0,
                'tv_place' => $record->tv_place ?? '',
                'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
            ],
            'filter_year' => $filterYear,
            'filtered_comments' => $filteredComments,
            'filtered_guard_checks' => $filteredGuardChecks,
            'filtered_battery_replacements' => $filteredBatteryReplacements,
        ];
        
    public function download(Request $request, $recordId)
    {
        $record = Maintain::findOrFail($recordId);
        
        // Ensure user can only access their own records
        if ($record->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        $filterYear = $request->get('year', now()->year);
        
        try {
            // Get filtered data
            $filteredComments = $record->commentsForYear($filterYear)->get()->map(function($comment) {
                return [
                    'date' => $comment->date->format('Y-m-d'),
                    'comment' => strip_tags($comment->comment ?? '')
                ];
            })->toArray();

            $filteredGuardChecks = $record->fireDoorGuardChecksForYear($filterYear)->get()->map(function($check) {
                return [
                    'checked_date' => $check->checked_date->format('Y-m-d'),
                    'notes' => strip_tags($check->notes ?? '')
                ];
            })->toArray();

            $filteredBatteryReplacements = $record->fireDoorGuardBatteryReplacementsForYear($filterYear)->get()->map(function($replacement) {
                return [
                    'replaced_date' => $replacement->replaced_date->format('Y-m-d'),
                    'notes' => strip_tags($replacement->notes ?? '')
                ];
            })->toArray();

            // Simple data structure
            $data = [
                'record' => [
                    'room_number' => $record->room_number,
                    'bed_type' => $record->bed_type ?? '',
                    'has_bed_rails' => $record->has_bed_rails ? 1 : 0,
                    'has_bed_rail_covers' => $record->has_bed_rail_covers ? 1 : 0,
                    'mattress_type' => $record->mattress_type ?? '',
                    'air_mattress_machine_type' => $record->air_mattress_machine_type ?? '',
                    'has_sensor_mat' => $record->has_sensor_mat ? 1 : 0,
                    'has_ceiling_light' => $record->has_ceiling_light ? 1 : 0,
                    'has_ceiling_fan' => $record->has_ceiling_fan ? 1 : 0,
                    'has_wall_light' => $record->has_wall_light ? 1 : 0,
                    'has_bathroom_light' => $record->has_bathroom_light ? 1 : 0,
                    'has_ac' => $record->has_ac ? 1 : 0,
                    'has_door_lock' => $record->has_door_lock ? 1 : 0,
                    'door_lock_pin' => $record->door_lock_pin ?? '',
                    'has_tv' => $record->has_tv ? 1 : 0,
                    'tv_model' => $record->tv_model ?? '',
                    'has_tv_remote' => $record->has_tv_remote ? 1 : 0,
                    'tv_place' => $record->tv_place ?? '',
                    'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
                ],
                'filter_year' => $filterYear,
                'filtered_comments' => $filteredComments,
                'filtered_guard_checks' => $filteredGuardChecks,
                'filtered_battery_replacements' => $filteredBatteryReplacements,
            ];
            
            // Try to generate PDF using the same view but with minimal processing
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('maintain-print', $data);
            
            $filename = "room-{$record->room_number}-{$filterYear}.pdf";
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('PDF Download Error: ' . $e->getMessage());
            
            // Fallback to CSV if PDF fails
            return $this->downloadCsv($record, $filterYear);
        }
    }
    
    private function downloadCsv($record, $filterYear)
    {
        // Create CSV content as fallback
        $csv = "Room Maintenance Record - Year {$filterYear}\n\n";
        $csv .= "Room Number,{$record->room_number}\n";
        $csv .= "Bed Type,{$record->bed_type}\n";
        $csv .= "Has Bed Rails," . ($record->has_bed_rails ? 'Yes' : 'No') . "\n";
        $csv .= "Mattress Type,{$record->mattress_type}\n";
        $csv .= "Has TV," . ($record->has_tv ? 'Yes' : 'No') . "\n";
        $csv .= "Has Ceiling Light," . ($record->has_ceiling_light ? 'Yes' : 'No') . "\n";
        $csv .= "Has Ceiling Fan," . ($record->has_ceiling_fan ? 'Yes' : 'No') . "\n";
        $csv .= "Has Wall Light," . ($record->has_wall_light ? 'Yes' : 'No') . "\n";
        $csv .= "Has Bathroom Light," . ($record->has_bathroom_light ? 'Yes' : 'No') . "\n";
        $csv .= "Has AC," . ($record->has_ac ? 'Yes' : 'No') . "\n";
        $csv .= "Has Door Lock," . ($record->has_door_lock ? 'Yes' : 'No') . "\n";
        $csv .= "Last Updated,{$record->updated_at->format('Y-m-d H:i:s')}\n";
        
        // Add comments
        $csv .= "\nComments ({$filterYear})\n";
        $csv .= "Date,Comment\n";
        $comments = $record->commentsForYear($filterYear)->get();
        foreach ($comments as $comment) {
            $cleanComment = str_replace(['"', "\n", "\r"], ['""', ' ', ' '], $comment->comment);
            $csv .= $comment->date->format('Y-m-d') . ',"' . $cleanComment . '"' . "\n";
        }
        
        // Add guard checks
        $csv .= "\nFire Door Guard Checks ({$filterYear})\n";
        $csv .= "Date,Notes\n";
        $guardChecks = $record->fireDoorGuardChecksForYear($filterYear)->get();
        foreach ($guardChecks as $check) {
            $cleanNotes = str_replace(['"', "\n", "\r"], ['""', ' ', ' '], $check->notes ?? '');
            $csv .= $check->checked_date->format('Y-m-d') . ',"' . $cleanNotes . '"' . "\n";
        }
        
        // Add battery replacements
        $csv .= "\nBattery Replacements ({$filterYear})\n";
        $csv .= "Date,Notes\n";
        $batteryReplacements = $record->fireDoorGuardBatteryReplacementsForYear($filterYear)->get();
        foreach ($batteryReplacements as $replacement) {
            $cleanNotes = str_replace(['"', "\n", "\r"], ['""', ' ', ' '], $replacement->notes ?? '');
            $csv .= $replacement->replaced_date->format('Y-m-d') . ',"' . $cleanNotes . '"' . "\n";
        }
        
        $filename = "room-{$record->room_number}-{$filterYear}.csv";
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($csv),
        ]);
    }
}