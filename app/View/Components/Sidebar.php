<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public array $navItems;

    public string $role;

    public string $subtitle;

    public function __construct()
    {
        $user = auth()->user();
        $this->role = $user->role;
        $this->navItems = $this->getNavItems();
        $this->subtitle = match ($this->role) {
            'coach' => 'Tactical Intel - ELITE SCOUTING',
            'manager' => 'Management Center',
            'player' => 'Player Hub',
            default => '',
        };
    }

    private function getNavItems(): array
    {
        return match ($this->role) {
            'coach' => [
                ['label' => 'Kontrol Paneli', 'route' => 'coach.dashboard', 'url' => route('coach.dashboard'), 'icon' => 'dashboard'],
                ['label' => 'Oyuncular', 'route' => 'coach.players.*', 'url' => '#', 'icon' => 'users'],
                ['label' => 'Değerlendirme Yap', 'route' => 'coach.evaluations.*', 'url' => '#', 'icon' => 'clipboard'],
                ['label' => 'Antrenman Ekle', 'route' => 'coach.trainings.*', 'url' => '#', 'icon' => 'dumbbell'],
                ['label' => 'Maç İstatistikleri', 'route' => 'coach.matches.*', 'url' => '#', 'icon' => 'chart-bar'],
                ['label' => 'Analizler (AI Panel)', 'route' => 'coach.analysis.*', 'url' => '#', 'icon' => 'brain'],
                ['label' => 'Kadro Oluştur', 'route' => 'coach.lineups.*', 'url' => '#', 'icon' => 'layout'],
                ['label' => 'Akıllı Kadro Önerisi', 'route' => 'coach.smart-squad.*', 'url' => '#', 'icon' => 'sparkles'],
                ['label' => 'Takım Genel Bakış', 'route' => 'coach.team.*', 'url' => '#', 'icon' => 'team'],
            ],
            'manager' => [
                ['label' => 'Kontrol Paneli', 'route' => 'manager.dashboard', 'url' => route('manager.dashboard'), 'icon' => 'dashboard'],
                ['label' => 'Oyuncular', 'route' => 'manager.players.*', 'url' => '#', 'icon' => 'users'],
                ['label' => 'Takım Genel Bakış', 'route' => 'manager.team.*', 'url' => '#', 'icon' => 'team'],
                ['label' => 'Oyuncu Yönetimi', 'route' => 'manager.player-management.*', 'url' => '#', 'icon' => 'user-cog'],
                ['label' => 'Antrenör Yönetimi', 'route' => 'manager.coach-management.*', 'url' => '#', 'icon' => 'user-shield'],
                ['label' => 'Raporlar', 'route' => 'manager.reports.*', 'url' => '#', 'icon' => 'file-text'],
                ['label' => 'divider', 'section' => 'Sistem'],
                ['label' => 'Analizler (AI Panel)', 'route' => 'manager.analysis.*', 'url' => '#', 'icon' => 'brain'],
            ],
            'player' => [
                ['label' => 'Kontrol Paneli', 'route' => 'player.dashboard', 'url' => route('player.dashboard'), 'icon' => 'dashboard'],
                ['label' => 'Performansım', 'route' => 'player.performance.*', 'url' => '#', 'icon' => 'activity'],
                ['label' => 'Gelişim Raporlarım', 'route' => 'player.reports.*', 'url' => '#', 'icon' => 'trending-up'],
                ['label' => 'Antrenman Geçmişi', 'route' => 'player.trainings.*', 'url' => '#', 'icon' => 'calendar'],
            ],
            default => [],
        };
    }

    public function render(): View
    {
        return view('components.sidebar');
    }
}
