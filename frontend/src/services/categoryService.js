import api from './api';

export const categoryService = {
  // Get all categories
  getCategories: async (page = 1) => {
    const response = await api.get(`/categories?page=${page}`);
    return response.data;
  },

  // Get all categories without pagination
  getAllCategories: async () => {
    const response = await api.get('/categories?per_page=100');
    return response.data;
  },

  // Get single category
  getCategory: async (id) => {
    const response = await api.get(`/categories/${id}`);
    return response.data;
  },

  // Create category
  createCategory: async (categoryData) => {
    const response = await api.post('/categories', categoryData);
    return response.data;
  },

  // Update category
  updateCategory: async (id, categoryData) => {
    const response = await api.put(`/categories/${id}`, categoryData);
    return response.data;
  },

  // Delete category
  deleteCategory: async (id) => {
    const response = await api.delete(`/categories/${id}`);
    return response.data;
  }
};
