<?php

namespace App\Services;

class StreakService {
    public function update($user) {
        $today = now()->toDateString();
        $lastLogin = $user->last_login_date;

        if ($lastLogin === $today) {
            return $user->streak_count;
        }

        if ($lastLogin === now()->subDay()->toDateString()) {
            $user->streak_count += 1;
        } else {
            $user->streak_count = 1;
        }

        $user->last_login_date = $today;
        $user->save();

        return $user->streak_count;
    }
}