<?php

namespace App\Helpers;

class AvatarHelper
{
    /**
     * Generate avatar URL from name initials
     * Using UI Avatars API
     */
    public static function generate($name, $size = 200)
    {
        $initials = self::getInitials($name);
        $background = self::getColorFromName($name);
        
        // Remove # from color
        $background = str_replace('#', '', $background);
        
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) 
               . "&size={$size}&background={$background}&color=fff&bold=true";
    }
    
    /**
     * Get initials from name
     */
    private static function getInitials($name)
    {
        $words = explode(' ', trim($name));
        
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        
        return strtoupper(substr($name, 0, 2));
    }
    
    /**
     * Generate consistent color from name
     */
    private static function getColorFromName($name)
    {
        $colors = [
            '2563eb', // Blue
            'dc2626', // Red
            '059669', // Green
            'ea580c', // Orange
            '7c3aed', // Purple
            'db2777', // Pink
            '0891b2', // Cyan
            '4f46e5', // Indigo
            '65a30d', // Lime
            'e11d48', // Rose
        ];
        
        $hash = 0;
        for ($i = 0; $i < strlen($name); $i++) {
            $hash = ord($name[$i]) + (($hash << 5) - $hash);
        }
        
        $index = abs($hash) % count($colors);
        return $colors[$index];
    }
    
    /**
     * Get avatar URL (from database or generate)
     */
    public static function getAvatar($user, $size = 200)
    {
        if (!empty($user->avatar_url)) {
            return asset('storage/avatars' . $user->avatar_url);
        }
        
        return self::generate($user->name, $size);
    }
}