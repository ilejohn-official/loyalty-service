import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import CustomerDashboard from './pages/CustomerDashboard';
import AdminDashboard from './pages/AdminDashboard';
import AdminLogin from './pages/AdminLogin';

const App = () => (
  <Router>
    <Routes>
      {/* Customer Dashboard */}
      <Route path="/" element={<CustomerDashboard />} /> 

      {/* Admin Routes */}
      <Route path="/admin/login" element={<AdminLogin />} />
      <Route path="/admin/dashboard" element={<AdminDashboard />} />

      {/* Fallback route (optional) */}
      <Route path="*" element={<div>404 Not Found</div>} />
    </Routes>
  </Router>
);

export default App;