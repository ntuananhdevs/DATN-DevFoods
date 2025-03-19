import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Home from '@pages/client/home/Home';
import Login from '@pages/auth/Login';
import Register from '@pages/auth/Register';
import AdminLayout from '@layouts/Admin/AdminLayout';

const AppRoutes = () => (
  <BrowserRouter>
    <Routes>
      <Route path="/" element={<Home />} />
      <Route path="/login" element={<Login />} />
      <Route path="/register" element={<Register />} />
      <Route path="/admin/*" element={<AdminLayout />} />
    </Routes>
  </BrowserRouter>
);

export default AppRoutes;
