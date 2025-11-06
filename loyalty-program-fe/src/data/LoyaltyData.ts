// src/hooks/useLoyaltyData.ts (UPDATED FOR MERGING DATA)

import { useState, useEffect } from 'react';
import type { CustomerLoyaltySummary, AchievementDisplay, AchievementProgress } from '../types/loyalty';
import { fetchCustomerLoyaltyData } from '../api/loyaltyApi';

interface LoyaltyHookState {
    summary: CustomerLoyaltySummary | null;
    achievements: AchievementDisplay[]; // Merged list for display
    loading: boolean;
    error: string | null;
    refetch: () => void;
}

// Helper to convert Laravel date string (e.g., "2025-10-29 15:24:37.000000") to ISO format
const toDisplayDate = (dateString: string) => {
    // Replace space with T to make it ISO-like, necessary for JS Date parsing in some environments
    return new Date(dateString.replace(' ', 'T').split('.')[0]).toLocaleDateString();
}

// Helper to convert achievement type to display name
const getDisplayName = (type: string) => 
  type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

/**
 * Custom hook to fetch and consolidate loyalty data.
 */
export function useLoyaltyData(userId: number): LoyaltyHookState {
    const [summary, setSummary] = useState<CustomerLoyaltySummary | null>(null);
    const [achievements, setAchievements] = useState<AchievementDisplay[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [fetchCount, setFetchCount] = useState(0); 

    const refetch = () => setFetchCount(prev => prev + 1);

    useEffect(() => {
        const loadData = async () => {
            setLoading(true);
            setError(null);
            
            try {
                const data = await fetchCustomerLoyaltyData(userId);
                setSummary(data);

                // --- DATA MERGING LOGIC ---
                const unlockedMap = new Map(
                    data.data.unlocked.map(a => [a.achievement_type, a])
                );
                
                const mergedAchievements = data.data.progress.map((progressItem: AchievementProgress): AchievementDisplay => {
                    const unlockedItem = unlockedMap.get(progressItem.achievement_type);
                    const isUnlocked = !!unlockedItem;
                    
                    let description = `Goal: ${progressItem.target}`;
                    let progressPct = (progressItem.current / progressItem.target) * 100;

                    // If unlocked, pull date and ID from the unlocked item
                    if (isUnlocked) {
                        progressPct = 100;
                        description = JSON.parse(unlockedItem!.metadata).milestone || description;
                    }
                    
                    return {
                        id: unlockedItem?.id || null, // ID is only available if unlocked
                        achievement_type: progressItem.achievement_type,
                        displayName: getDisplayName(progressItem.achievement_type),
                        isUnlocked: isUnlocked,
                        unlocked_at: isUnlocked ? toDisplayDate(unlockedItem!.unlocked_at) : null,
                        current: progressItem.current,
                        target: progressItem.target,
                        progressPct: Math.min(100, Math.round(progressPct)),
                        description: description,
                    };
                });
                
                setAchievements(mergedAchievements);

            } catch (err) {
                setError(err instanceof Error ? err.message : 'An unexpected error occurred.');
            } finally {
                setLoading(false);
            }
        };
        
        loadData();
    }, [userId, fetchCount]);

    return { summary, achievements, loading, error, refetch };
}