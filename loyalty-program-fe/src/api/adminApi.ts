import { ADMIN_TOKEN_KEY } from '../pages/AdminLogin';

export interface UserSummary {
  id: number;
  name: string;
  email: string;
  is_admin: boolean;
  achievements_count: number;
  badges_count: number; 
}

export interface AdminPaginatorData {
  current_page: number;
  data: UserSummary[];
}

export interface AdminDashboardData {
  data: AdminPaginatorData;
  meta: {
    total_users: number;
  };
}

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1';

/**
 * Fetches the list of all users' loyalty summaries for the Admin Dashboard.
 * Requires the mock token in the Authorization header.
 */
export async function fetchAdminDashboardData(): Promise<AdminDashboardData> {
  const token = localStorage.getItem(ADMIN_TOKEN_KEY);
  
  if (!token) {
    throw new Error("Admin token not found. Please log in.");
  }

  const url = `${API_BASE_URL}/admin/users/achievements`;

  const headers = { 
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}` 
  };

  const response = await fetch(url, { headers });

  if (response.status === 401 || response.status === 403) {
      localStorage.removeItem(ADMIN_TOKEN_KEY);
      throw new Error("Authentication failed. Redirecting to login.");
  }

  if (!response.ok) {
    const errorBody = await response.json();
    throw new Error(`API Error ${response.status}: ${errorBody.message || 'Failed to fetch admin data'}`);
  }

  const data: AdminDashboardData = await response.json();
  return data;
}