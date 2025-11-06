export interface UserResource {
    id: number;
    name: string;
    email: string;
}

export interface AchievementProgress {
    achievement_type: string;
    target: number;
    current: number;
}

export interface AchievementUnlocked {
    id: number;
    achievement_type: string;
    unlocked_at: string; 
    metadata: string; 
}

export interface BadgeResource {
    id: number;
    badge_type: string;
    level: number;
    earned_at: string; // Date string
}


export interface CustomerLoyaltySummary {
    data: {
        progress: AchievementProgress[]; 
        unlocked: AchievementUnlocked[]; 
        total_unlocked: number;
        badges: BadgeResource[];
        total_earned: number;
        highest_level: number; 
        user: UserResource;
    };
}

export interface AchievementDisplay {
    id: number | null;
    achievement_type: string;
    displayName: string;
    isUnlocked: boolean;
    unlocked_at: string | null;
    current: number;
    target: number;
    progressPct: number;
    description: string;
}