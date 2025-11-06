import React from 'react';
import { Navigate } from 'react-router-dom';
import { ADMIN_TOKEN_KEY } from '../pages/AdminLogin'; 

interface AdminGuardProps {
  children: React.ReactNode;
}

const AdminGuard: React.FC<AdminGuardProps> = ({ children }) => {
  // Check if the mock admin token exists in local storage
  const isAuthenticated = !!localStorage.getItem(ADMIN_TOKEN_KEY);

  if (!isAuthenticated) {
    // If not authenticated, redirect to the login page
    return <Navigate to="/admin/login" replace />;
  }

  // If authenticated, render the children (the protected component)
  return <>{children}</>;
};

export default AdminGuard;