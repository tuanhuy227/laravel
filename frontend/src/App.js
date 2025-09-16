import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';

// Context
import { AuthProvider } from './contexts/AuthContext';

// Components
import Navbar from './components/common/Navbar';
import ProtectedRoute from './components/common/ProtectedRoute';

// Pages
import Home from './pages/Home';
import Dashboard from './pages/Dashboard';

// Auth Components
import Login from './components/auth/Login';
import Register from './components/auth/Register';

// Feature Components
import ProductList from './components/products/ProductList';
import CategoryList from './components/categories/CategoryList';

// Layout wrapper for protected pages
const Layout = ({ children }) => (
  <div className="min-h-screen bg-gray-50">
    <Navbar />
    <main>{children}</main>
  </div>
);

function App() {
  return (
    <AuthProvider>
      <Router>
        <div className="App">
          <Toaster
            position="top-right"
            toastOptions={{
              duration: 4000,
              style: {
                background: '#363636',
                color: '#fff',
              },
              success: {
                duration: 3000,
                theme: {
                  primary: 'green',
                  secondary: 'black',
                },
              },
            }}
          />
          
          <Routes>
            {/* Public Routes */}
            <Route path="/" element={<Layout><Home /></Layout>} />
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
            
            {/* Protected Routes */}
            <Route 
              path="/dashboard" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <Dashboard />
                  </Layout>
                </ProtectedRoute>
              } 
            />
            
            <Route 
              path="/products" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <ProductList />
                  </Layout>
                </ProtectedRoute>
              } 
            />
            
            <Route 
              path="/categories" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <CategoryList />
                  </Layout>
                </ProtectedRoute>
              } 
            />
            
            {/* Fallback route */}
            <Route path="*" element={<Navigate to="/" replace />} />
          </Routes>
        </div>
      </Router>
    </AuthProvider>
  );
}

export default App;
