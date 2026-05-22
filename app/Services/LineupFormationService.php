<?php

namespace App\Services;

class LineupFormationService
{
    public const FORMATIONS = ['4-4-2', '4-3-3', '4-2-3-1', '3-5-2', '5-3-2', '3-4-3', '4-5-1'];

    public function slots(string $formation): array
    {
        $lines = match ($formation) {
            '4-3-3' => [['GK'], ['LB', 'LCB', 'RCB', 'RB'], ['LCM', 'CM', 'RCM'], ['LW', 'ST', 'RW']],
            '4-2-3-1' => [['GK'], ['LB', 'LCB', 'RCB', 'RB'], ['LDM', 'RDM'], ['LAM', 'CAM', 'RAM'], ['ST']],
            '3-5-2' => [['GK'], ['LCB', 'CB', 'RCB'], ['LWB', 'LCM', 'CM', 'RCM', 'RWB'], ['LST', 'RST']],
            '5-3-2' => [['GK'], ['LWB', 'LCB', 'CB', 'RCB', 'RWB'], ['LCM', 'CM', 'RCM'], ['LST', 'RST']],
            '3-4-3' => [['GK'], ['LCB', 'CB', 'RCB'], ['LM', 'LCM', 'RCM', 'RM'], ['LW', 'ST', 'RW']],
            '4-5-1' => [['GK'], ['LB', 'LCB', 'RCB', 'RB'], ['LM', 'LCM', 'CM', 'RCM', 'RM'], ['ST']],
            default => [['GK'], ['LB', 'LCB', 'RCB', 'RB'], ['LM', 'LCM', 'RCM', 'RM'], ['LST', 'RST']],
        };

        $lineCount = count($lines);
        $slots = [];

        foreach ($lines as $lineIndex => $line) {
            $count = count($line);
            foreach ($line as $slotIndex => $slot) {
                $slots[] = [
                    'slot_key' => $slot,
                    'field_x' => (int) round((($slotIndex + 1) / ($count + 1)) * 100),
                    'field_y' => (int) round((($lineIndex + 1) / ($lineCount + 1)) * 100),
                ];
            }
        }

        return $slots;
    }
}
