import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { UserGroupIcon, ArrowRightStartOnRectangleIcon, EyeIcon } from '@heroicons/react/24/solid';
import { fetchAdminDashboardData, type UserSummary } from '../api/adminApi';
import { ADMIN_TOKEN_KEY } from './AdminLogin';

const AdminDashboard: React.FC = () => {
  const [data, setData] = useState<UserSummary[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const navigate = useNavigate();

  const loadData = async () => {
    setLoading(true);
    setError(null);
    try {
      const result = await fetchAdminDashboardData();
      setData(result.data.data);
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'An unexpected error occurred.';
      setError(errorMessage);

      if (errorMessage.includes("Authentication failed") || errorMessage.includes("token not found")) {
        localStorage.removeItem(ADMIN_TOKEN_KEY);
        navigate('/admin/login');
      }
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadData();
  }, []);

  const handleLogout = () => {
    localStorage.removeItem(ADMIN_TOKEN_KEY);
    navigate('/admin/login');
  };

  const viewCustomerDashboard = (userId: number) => {
    navigate(`/${userId}`);
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="inline-block animate-spin rounded-full h-10 w-10 border-4 border-indigo-500 border-t-transparent"></div>
      </div>
    );
  }

  if (error && !error.includes("Authentication failed")) {
    return (
      <div className="min-h-screen p-8 bg-gray-50">
        <div className="bg-red-100 p-6 rounded-lg text-red-700 max-w-4xl mx-auto mt-10">
          <p className="font-semibold text-lg mb-2">Error Loading Admin Data</p>
          <p>{error}</p>
          <button onClick={loadData} className="mt-4 text-sm text-red-600 hover:text-red-800 font-medium underline">
            Retry Fetch
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-100 p-6 sm:p-10">
      <div className="max-w-7xl mx-auto">

        {/* Header */}
        <header className="flex justify-between items-center bg-white p-6 rounded-xl shadow-lg mb-8">
          <h1 className="text-3xl font-extrabold text-slate-800 flex items-center">
            <UserGroupIcon className="h-7 w-7 text-indigo-600 mr-3" />
            Admin Dashboard
          </h1>
          <button
            onClick={handleLogout}
            className="flex items-center text-sm font-medium text-red-600 hover:text-red-800 transition duration-150 p-2 rounded-md hover:bg-red-50"
          >
            <ArrowRightStartOnRectangleIcon className="h-5 w-5 mr-1" />
            Logout
          </button>
        </header>

        {/* User Data Table */}
        <div className="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  User ID
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Name / Email
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Achievements Count
                </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Badges Count
                </th>
                <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {data.map((user) => (
                <tr key={user.id} className="hover:bg-indigo-50/50 transition duration-100">
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                    {user.id}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm font-medium text-gray-900">{user.name}</div>
                    <div className="text-sm text-gray-500">{user.email}</div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span className="font-semibold text-gray-800">{user.achievements_count}</span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span className="font-semibold text-yellow-600">{user.badges_count}</span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button
                      onClick={() => viewCustomerDashboard(user.id)}
                      className="text-indigo-600 hover:text-indigo-900 flex items-center justify-end"
                      title={`View dashboard for User ID ${user.id}`}
                    >
                      View Dashboard
                      <EyeIcon className="h-4 w-4 ml-1" />
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard;