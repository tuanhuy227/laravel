import { get } from 'react-hook-form';
import api from './api';

export const postService = {
  // Get all posts
  getPosts: async (page = 1) => {
    const response = await api.get(`/posts?page=${page}`);
    return response.data;
  },

  // Get single Post
  getPost: async (id) => {
    const response = await api.get(`/posts/${id}`);
    return response.data;
  },

  // Create Post
  createPost: async (PostData) => {
    const formData = new FormData();
    
    // Add text fields
    Object.keys(PostData).forEach(key => {
      if (key === 'images') {
        // Handle file uploads
        if (PostData.images && PostData.images.length > 0) {
          PostData.images.forEach(image => {
            formData.append('images[]', image);
          });
        }
      } else if (key === 'categories') {
        // Handle categories array
        if (PostData.categories && PostData.categories.length > 0) {
          PostData.categories.forEach(categoryId => {
            formData.append('categories[]', categoryId);
          });
        }
      } else {
        formData.append(key, PostData[key]);
      }
    });

    const response = await api.post('/posts', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  // Update Post
  updatePost: async (id, PostData) => {
    const formData = new FormData();
    formData.append('_method', 'PUT');
    
    // Add text fields
    Object.keys(PostData).forEach(key => {
      if (key === 'images') {
        // Handle file uploads
        if (PostData.images && PostData.images.length > 0) {
          PostData.images.forEach(image => {
            formData.append('images[]', image);
          });
        }
      } else if (key === 'categories') {
        // Handle categories array
        if (PostData.categories && PostData.categories.length > 0) {
          PostData.categories.forEach(categoryId => {
            formData.append('categories[]', categoryId);
          });
        }
      } else {
        formData.append(key, PostData[key]);
      }
    });

    const response = await api.post(`/posts/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  // Delete Post
  deletePost: async (id) => {
    const response = await api.delete(`/posts/${id}`);
    return response.data;
  },

  importposts: async (file) => {
    const formData = new FormData();
    formData.append("file", file);

    const response = await api.post('/posts/import', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },
  getAllTypes: async () => {
    const response = await api.get('/types');
    return response.data;
  }
};
