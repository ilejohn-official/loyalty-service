import type { CustomerLoyaltySummary } from '../types/loyalty';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1';

/**
 * Fetches the customer's loyalty summary from the real backend API.
 */
export async function fetchCustomerLoyaltyData(userId: number): Promise<CustomerLoyaltySummary> {
    const url = `${API_BASE_URL}/users/${userId}/achievements`;
    
    // We add authorization if needed (e.g., if this is protected by a Bearer token)
    const headers = { 
        'Content-Type': 'application/json',
        // Example: 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
    };

    try {
        const response = await fetch(url, { headers });

        if (!response.ok) {
            const errorBody = await response.json();
            throw new Error(`API Error ${response.status}: ${errorBody.message || 'Failed to fetch loyalty data'}`);
        }

        const data: CustomerLoyaltySummary = await response.json();
        return data;

    } catch (error) {
        console.error("Error fetching loyalty data:", error);
        throw error;
    }
}