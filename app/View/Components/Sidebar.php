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
            'super_admin' => 'System Administration',
            'coach' => 'Tactical Intel - ELITE SCOUTING',
            'manager' => 'Management Center',
            'player' => 'Player Hub',
            default => '',
        };
    }

    private function getNavItems(): array
    {
        return match ($this->role) {
            'super_admin' => [
                ['label' => 'Kontrol Paneli', 'route' => 'super_admin.dashboard', 'url' => route('super_admin.dashboard'), 'icon' => 'dashboard'],
                ['label' => 'divider', 'section' => 'Yönetim'],
                ['label' => 'Kullanıcılar', 'route' => 'super_admin.users.*', 'url' => route('super_admin.users.index'), 'icon' => 'user-shield'],
                ['label' => 'Takımlar', 'route' => 'super_admin.teams.*', 'url' => route('super_admin.teams.index'), 'icon' => 'team'],
                ['label' => 'Oyuncular', 'route' => 'super_admin.players.*', 'url' => route('super_admin.players.index'), 'icon' => 'users'],
            ],
            'coach' => [
                ['label' => 'Kontrol Paneli', 'route' => 'coach.dashboard', 'url' => route('coach.dashboard'), 'icon' => 'dashboard'],
                ['label' => 'Takımlarım', 'route' => 'coach.teams.*', 'url' => route('coach.teams.index'), 'icon' => 'team'],
                ['label' => 'Oyuncular', 'route' => 'coach.players.*', 'url' => route('coach.players.index'), 'icon' => 'users'],
                ['label' => 'Değerlendirme Yap', 'route' => 'coach.evaluations.*', 'url' => '#', 'icon' => 'clipboard'],
                ['label' => 'Antrenmanlar', 'route' => 'coach.trainings.*', 'url' => route('coach.trainings.index'), 'icon' => 'dumbbell'],
                ['label' => 'Maçlar', 'route' => 'coach.matches.*', 'url' => route('coach.matches.index'), 'icon' => 'chart-bar'],
                ['label' => 'Analizler (AI Panel)', 'route' => 'coach.analysis.*', 'url' => '#', 'icon' => 'brain'],
                ['label' => 'Kadro Oluştur', 'route' => 'coach.lineups.*', 'url' => '#', 'icon' => 'layout'],
                ['label' => 'Akıllı Kadro Önerisi', 'route' => 'coach.smart-squad.*', 'url' => '#', 'icon' => 'sparkles'],
            ],
            'manager' => [
                ['label' => 'Kontrol Paneli', 'route' => 'manager.dashboard', 'url' => route('manager.dashboard'), 'icon' => 'dashboard'],
                ['label' => 'Takımım', 'route' => 'manager.teams.*', 'url' => route('manager.teams.index'), 'icon' => 'team'],
                ['label' => 'Oyuncular', 'route' => 'manager.players.*', 'url' => route('manager.players.index'), 'icon' => 'users'],
                ['label' => 'Antrenmanlar', 'route' => 'manager.trainings.*', 'url' => route('manager.trainings.index'), 'icon' => 'dumbbell'],
                ['label' => 'Maçlar', 'route' => 'manager.matches.*', 'url' => route('manager.matches.index'), 'icon' => 'chart-bar'],
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
