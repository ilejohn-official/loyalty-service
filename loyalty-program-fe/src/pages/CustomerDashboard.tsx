import React, { useState } from 'react';
import type { AchievementDisplay } from '../types/loyalty';
import { useLoyaltyData } from '../hooks/useLoyaltyData';
import { BoltIcon, TrophyIcon, CheckCircleIcon, SparklesIcon } from '@heroicons/react/24/solid';
import { useParams } from 'react-router-dom'; // Ensure this is imported for dynamic ID

const getDisplayName = (type: string) =>
  type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

interface AchievementAlertProps {
  name: string;
  onClose: () => void;
}

const AchievementAlert: React.FC<AchievementAlertProps> = ({ name, onClose }) => (
  <div className="fixed top-4 right-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white p-5 rounded-xl shadow-2xl z-50 max-w-sm animate-slide-in-right">
    <div className="flex items-start gap-3">
      <div className="flex-shrink-0 bg-white/20 rounded-full p-2">
        {/* Icon size h-4 w-4 (Alert) */}
        <TrophyIcon className="h-4 w-4 text-yellow-300" />
      </div>
      <div className="flex-1 min-w-0">
        <p className="font-bold text-lg mb-1">Achievement Unlocked!</p>
        <p className="text-sm text-green-50">{name}</p>
      </div>
      <button
        onClick={onClose}
        className="flex-shrink-0 text-white/80 hover:text-white transition-colors text-2xl leading-none -mt-1"
      >
        ×
      </button>
    </div>
  </div>
);


// --- MAIN DASHBOARD COMPONENT ---
const CustomerDashboard: React.FC = () => {
  const { userId } = useParams<{ userId: string }>();
  const numericUserId = userId ? parseInt(userId, 10) : null;

  const validUserId = numericUserId && !isNaN(numericUserId) ? numericUserId : 0;

  const { summary: loyaltySummary, achievements, loading, error, refetch } = useLoyaltyData(validUserId);

  const [showAchievementAlert, setShowAchievementAlert] = useState<boolean>(false);
  const [unlockedAchievementName, setUnlockedAchievementName] = useState<string>('');

  const simulateUnlock = () => {
    if (showAchievementAlert || !loyaltySummary) return;

    const nextAchievement = achievements.find(a => !a.isUnlocked);

    if (nextAchievement) {
      setUnlockedAchievementName(nextAchievement.displayName);
      setShowAchievementAlert(true);

      setTimeout(() => {
        setShowAchievementAlert(false);
      }, 4000);
    }
  };

  if (numericUserId === null || isNaN(numericUserId) || validUserId === 0) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <p className="text-xl text-red-500">Error: Invalid or missing User ID in URL.</p>
      </div>
    );
  }

  // --- Loading/Error States (Updated styling) ---
  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-12 w-12 border-4 border-indigo-600 border-t-transparent mb-4"></div>
          <p className="text-lg text-slate-600">Loading loyalty data...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center">
        <div className="bg-white p-10 rounded-2xl shadow-xl max-w-md border border-red-300">
          <div className="text-red-600 text-center">
            <p className="text-xl font-semibold mb-2">Error Loading Data</p>
            <p className="text-sm text-slate-600 mb-4">{error}</p>
            <button onClick={refetch} className="bg-red-50 hover:bg-red-100 text-red-600 font-medium py-2 px-4 rounded transition-colors">Try Again</button>
          </div>
        </div>
      </div>
    );
  }

  if (!loyaltySummary) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center">
        <p className="text-lg text-slate-500">No loyalty data available.</p>
      </div>
    );
  }

  // --- Data from API ---
  const { total_unlocked, highest_level, user } = loyaltySummary.data;

  // Use placeholder/derived values for missing API fields
  const highestBadgeName = highest_level ? getDisplayName(`level_${highest_level}_badge`) : 'No Badge Earned';
  const nextBadgeLevel = highest_level + 1;
  const nextBadgeProgress = 0;
  const totalCashbackEarned = 0.00;

  return (
    // Updated background
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
      {showAchievementAlert && (
        <AchievementAlert
          name={unlockedAchievementName}
          onClose={() => setShowAchievementAlert(false)}
        />
      )}

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Header (Updated styling) */}
        <header className="bg-white rounded-2xl shadow-xl p-6 mb-8 border border-slate-200">
          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h1 className="text-3xl font-bold text-slate-900 mb-1">Loyalty Dashboard</h1>
              <p className="text-sm text-slate-500">Tracking rewards for User ID: {numericUserId}</p>
            </div>
            <div className="text-left sm:text-right">
              <p className="font-semibold text-slate-900">{user.name}</p>
              <p className="text-sm text-slate-500">{user.email}</p>
            </div>
          </div>
        </header>

        {/* Stats Grid (Updated styling) */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

          {/* Main Stats Card */}
          <div className="lg:col-span-2 bg-white rounded-2xl shadow-xl p-6 border border-slate-200">
            <div className="flex items-center gap-2 mb-6">
              <div className="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg p-2">
                {/* Icon size h-4 w-4 */}
                <BoltIcon className="h-4 w-4 text-white" />
              </div>
              <div>
                <h2 className="text-lg font-semibold text-slate-900">Current Status</h2>
                <p className="text-sm text-slate-500">{highestBadgeName} · Level {highest_level}</p>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4 mb-6">
              <div className="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                <p className="text-sm text-slate-600 mb-1">Unlocked</p>
                <p className="text-2xl font-bold text-indigo-600">{total_unlocked}</p>
                <p className="text-xs text-slate-500 mt-1">Achievements</p>
              </div>
              <div className="bg-green-50 rounded-xl p-4 border border-green-100">
                <p className="text-sm text-slate-600 mb-1">Earned</p>
                <p className="text-2xl font-bold text-green-600">${totalCashbackEarned.toFixed(2)}</p>
                <p className="text-xs text-slate-500 mt-1">Cashback</p>
              </div>
            </div>

            <div>
              <div className="flex items-center justify-between mb-2">
                <p className="text-sm font-medium text-slate-700">Next Badge Level {nextBadgeLevel}</p>
                <p className="text-sm font-semibold text-indigo-600">{nextBadgeProgress}%</p>
              </div>
              <div className="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                <div
                  className="bg-gradient-to-r from-indigo-500 to-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                  style={{ width: `${nextBadgeProgress}%` }}
                ></div>
              </div>
            </div>
          </div>

          {/* Simulate Button Card (Updated styling) */}
          <div className="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white flex flex-col justify-center items-center">
            {/* Icon size h-4 w-4 */}
            <SparklesIcon className="h-4 w-4 mb-3 opacity-90" />
            <h3 className="text-lg font-semibold mb-2">Test Feature</h3>
            <p className="text-sm text-indigo-100 mb-4 text-center">Simulate unlocking your next achievement</p>
            <button
              onClick={simulateUnlock}
              className="w-full bg-white text-indigo-600 hover:bg-indigo-50 font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
              disabled={showAchievementAlert}
            >
              Unlock Achievement
            </button>
          </div>
        </div>

        {/* Achievements Grid (Updated styling) */}
        <div className="bg-white rounded-2xl shadow-xl p-6 border border-slate-200">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-bold text-slate-900">Your Achievements</h2>
            <span className="text-sm text-slate-500 bg-slate-100 px-3 py-1 rounded-full">{achievements.length} total</span>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {achievements.map((achievement: AchievementDisplay, index) => {
              const { isUnlocked, displayName, progressPct, description, unlocked_at } = achievement;

              return (
                <div
                  key={achievement.id || index}
                  className={`rounded-xl p-4 transition-all duration-300 border-2 ${isUnlocked
                    ? 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-200 hover:shadow-lg'
                    : 'bg-slate-50 border-slate-200 hover:border-indigo-300 hover:shadow-md' // Added hover effect
                    }`}
                >
                  <div className="flex items-start gap-3 mb-3">
                    <div className={`flex-shrink-0 rounded-lg p-2 ${isUnlocked ? 'bg-green-100' : 'bg-slate-200'}`}>
                      {isUnlocked ? (
                        // Icon size h-3 w-3
                        <CheckCircleIcon className="h-3 w-3 text-green-600" />
                      ) : (
                        // Icon size h-3 w-3
                        <TrophyIcon className="h-3 w-3 text-slate-400" />
                      )}
                    </div>
                    <div className="flex-1 min-w-0">
                      <h3 className="font-semibold text-slate-900 text-sm mb-1 truncate">{displayName}</h3>
                      <p className="text-xs text-slate-600">{description}</p>
                    </div>
                  </div>

                  {!isUnlocked && progressPct > 0 && (
                    <div className="mt-3">
                      <div className="flex items-center justify-between mb-1">
                        <span className="text-xs text-slate-500">Progress</span>
                        <span className="text-xs font-semibold text-indigo-600">{progressPct}%</span>
                      </div>
                      <div className="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                        <div
                          className="bg-indigo-500 h-1.5 rounded-full transition-all duration-300"
                          style={{ width: `${progressPct}%` }}
                        ></div>
                      </div>
                    </div>
                  )}

                  {isUnlocked && (
                    <div className="mt-3 pt-3 border-t border-green-200">
                      <p className="text-xs text-green-700 font-medium flex items-center gap-1">
                        <CheckCircleIcon className="h-3 w-3" />
                        Unlocked: {unlocked_at}
                      </p>
                    </div>
                  )}

                  {!isUnlocked && progressPct === 0 && (
                    <div className="mt-3 pt-3 border-t border-slate-200">
                      <p className="text-xs text-slate-400 font-medium">🔒 Locked</p>
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </div>
  );
};

export default CustomerDashboard;