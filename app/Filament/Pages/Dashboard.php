<?php

namespace App\Filament\Pages;

use App\Models\Room;
use App\Models\WaterTemperature;
use App\Models\WindowCheck;
use App\Models\Maintain;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Widget;

// First, create the main Dashboard page
class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.pages.dashboard';
    
    protected function getHeaderWidgets(): array
    {
        return [
            DashboardStatsWidget::class,
        ];
    }
}

// Stats Overview Widget
class DashboardStatsWidget extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = auth()->id();
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Get counts
        $totalRooms = Room::where('user_id', $userId)->count();
        $waterTempsThisMonth = WaterTemperature::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->count();
        $windowChecksThisMonth = WindowCheck::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->count();
        $maintenanceRecords = Maintain::where('user_id', $userId)->count();
        
        // Calculate completion rates
        $waterTempCompletion = $totalRooms > 0 ? round(($waterTempsThisMonth / $totalRooms) * 100) : 0;
        $windowCheckCompletion = $totalRooms > 0 ? round(($windowChecksThisMonth / $totalRooms) * 100) : 0;
        
        // Count issues
        $waterIssues = WaterTemperature::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->where('has_fault', true)
            ->count();
        $windowIssues = WindowCheck::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->where(function($query) {
                $query->where('fit_for_purpose', false)
                      ->orWhere('status', false);
            })
            ->count();

        return [
            BaseStatsOverviewWidget\Stat::make('Total Rooms', $totalRooms)
                ->description('Rooms under management')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),
                
            BaseStatsOverviewWidget\Stat::make('Water Temp Checks', $waterTempsThisMonth . '/' . $totalRooms)
                ->description($waterTempCompletion . '% complete this month')
                ->descriptionIcon($waterTempCompletion >= 80 ? 'heroicon-m-check-circle' : 'heroicon-m-clock')
                ->color($waterTempCompletion >= 80 ? 'success' : 'warning'),
                
            BaseStatsOverviewWidget\Stat::make('Window Checks', $windowChecksThisMonth . '/' . $totalRooms)
                ->description($windowCheckCompletion . '% complete this month')
                ->descriptionIcon($windowCheckCompletion >= 80 ? 'heroicon-m-check-circle' : 'heroicon-m-clock')
                ->color($windowCheckCompletion >= 80 ? 'success' : 'warning'),
                
            BaseStatsOverviewWidget\Stat::make('Issues Found', ($waterIssues + $windowIssues))
                ->description('Water: ' . $waterIssues . ' | Windows: ' . $windowIssues)
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color(($waterIssues + $windowIssues) > 0 ? 'danger' : 'success'),
        ];
    }
}

