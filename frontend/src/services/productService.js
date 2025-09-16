import api from './api';

export const productService = {
  // Get all products
  getProducts: async (page = 1) => {
    const response = await api.get(`/products?page=${page}`);
    return response.data;
  },

  // Get single product
  getProduct: async (id) => {
    const response = await api.get(`/products/${id}`);
    return response.data;
  },

  // Create product
  createProduct: async (productData) => {
    const formData = new FormData();
    
    // Add text fields
    Object.keys(productData).forEach(key => {
      if (key === 'images') {
        // Handle file uploads
        if (productData.images && productData.images.length > 0) {
          productData.images.forEach(image => {
            formData.append('images[]', image);
          });
        }
      } else if (key === 'categories') {
        // Handle categories array
        if (productData.categories && productData.categories.length > 0) {
          productData.categories.forEach(categoryId => {
            formData.append('categories[]', categoryId);
          });
        }
      } else {
        formData.append(key, productData[key]);
      }
    });

    const response = await api.post('/products', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  // Update product
  updateProduct: async (id, productData) => {
    const formData = new FormData();
    formData.append('_method', 'PUT');
    
    // Add text fields
    Object.keys(productData).forEach(key => {
      if (key === 'images') {
        // Handle file uploads
        if (productData.images && productData.images.length > 0) {
          productData.images.forEach(image => {
            formData.append('images[]', image);
          });
        }
      } else if (key === 'categories') {
        // Handle categories array
        if (productData.categories && productData.categories.length > 0) {
          productData.categories.forEach(categoryId => {
            formData.append('categories[]', categoryId);
          });
        }
      } else {
        formData.append(key, productData[key]);
      }
    });

    const response = await api.post(`/products/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  // Delete product
  deleteProduct: async (id) => {
    const response = await api.delete(`/products/${id}`);
    return response.data;
  },

  importProducts: async (file) => {
    const formData = new FormData();
    formData.append("file", file);

    const response = await api.post('/products/import', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  }
};
