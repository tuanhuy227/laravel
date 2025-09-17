import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { postService } from '../../services/postService.js';
import toast from 'react-hot-toast';
import LoadingSpinner from '../common/LoadingSpinner';
import Pagination from '../common/Pagination';
import Modal from '../common/Modal';
import PostForm from './PostForm';

const PostList = () => {
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [pagination, setPagination] = useState({});
  const [showModal, setShowModal] = useState(false);
  const [editingPost, setEditingPost] = useState(null);
  const navigate = useNavigate();
  const [file, setFile] = useState(null);
  const [message, setMessage] = useState("");
  const [types, setTypes] = useState([]);


  useEffect(() => {
    fetchPosts();
    fetchTypes();
  }, []);

  const fetchPosts = async (page = 1) => {
    try {
      setLoading(true);
      const data = await postService.getPosts(page);
      setPosts(data.data);
      setPagination({
        currentPage: data.current_page,
        totalPages: data.last_page,
        total: data.total
      });
    } catch (error) {
      toast.error('Error fetching posts');
    } finally {
      setLoading(false);
    }
  };

    const fetchTypes = async () => {
      try {
        const data = await postService.getAllTypes();
        setTypes(data || []);
        console.log(data);
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    };


  const handleDelete = async (id) => {
    if (window.confirm('Are you sure you want to delete this product?')) {
      try {
        await postService.deletePost(id);
        toast.success('Product deleted successfully');
        fetchPosts(pagination.currentPage);
      } catch (error) {
        toast.error('Error deleting product');
      }
    }
  };

  const handleEdit = (product) => {
    setEditingPost(product);
    setShowModal(true);
  };

  const handleCreate = () => {
    setEditingPost(null);
    setShowModal(true);
  };

  const handleModalClose = () => {
    setShowModal(false);
    setEditingPost(null);
  };

  const handlePostSaved = () => {
    fetchPosts(pagination.currentPage);
    handleModalClose();
  };


  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <LoadingSpinner size="large" />
      </div>
    );
  }

  const handleFileChange = (e) => {
    setFile(e.target.files[0]);
  };

  const handleUpload = async () => {
    if (!file) {
      setMessage("Please select a file first!");
      return;
    }

    try {
      const response = await postService.importposts(file);
      setMessage(response.message);
      fetchPosts();
    } catch (error) {
      console.error(error);
      setMessage("Import failed!");
    }
  };

  return (
    <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div className="px-4 py-6 sm:px-0">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-3xl font-bold text-gray-900">posts</h1>
          <button
            onClick={handleCreate}
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
          >
            Add Product
          </button>
        </div>
        <div className="p-4">
      <h2 className="text-xl mb-2">Import posts (CSV)</h2>
      <input type="file" accept=".csv" onChange={handleFileChange} />
      <button
        onClick={handleUpload}
        className="ml-2 px-4 py-2 bg-blue-500 text-white rounded"
      >
        Import
      </button>
      {message && <p className="mt-2">{message}</p>}
    </div>

        {posts.length === 0 ? (
          <div className="text-center py-12">
            <p className="text-gray-500 text-lg">No posts found</p>
            <button
              onClick={handleCreate}
              className="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
            >
              Create your first product
            </button>
          </div>
        ) : (
          <>
            <div className="bg-white shadow overflow-hidden sm:rounded-md">
              <ul className="divide-y divide-gray-200">
                {posts.map((post) => (
                  <li key={post.id}>
                    <div className="px-4 py-4 flex items-center justify-between">
                      <div className="flex items-center">
                        <div className="flex-shrink-0 h-16 w-16">
                          {post.images && post.images.length > 0 ? (
                            <img
                              className="h-16 w-16 rounded-lg object-cover"
                              src={`http://localhost:8080/storage/${post.images[0].path}`}
                              alt={post.title}
                            />
                          ) : (
                            <div className="h-16 w-16 rounded-lg bg-gray-300 flex items-center justify-center">
                              <span className="text-gray-500 text-sm">No Image</span>
                            </div>
                          )}
                        </div>
                      </div>
                      <div className="flex items-center space-x-4">
                        <div className="text-right">
                          <div className="text-lg font-medium text-gray-900">
                            {post.content}
                          </div>
                          <div className="text-sm text-gray-500">
                            {post.author}
                          </div>
                        </div>
                        <div className="flex space-x-2">
                          <button
                            onClick={() => handleEdit(post)}
                            className="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm"
                          >
                            Edit
                          </button>
                          <button
                            onClick={() => handleDelete(post.id)}
                            className="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"
                          >
                            Delete
                          </button>
                        </div>
                      </div>
                    </div>
                  </li>
                ))}
              </ul>
            </div>

            <Pagination
              currentPage={pagination.currentPage}
              totalPages={pagination.totalPages}
              onPageChange={fetchPosts}
            />
          </>
        )}

        <Modal
          isOpen={showModal}
          onClose={handleModalClose}
          title={editingPost ? 'Edit Product' : 'Create Product'}
          size="large"
        >
          <PostForm
            post={editingPost}
            onSave={handlePostSaved}
            onCancel={handleModalClose}
            types={types}
          />
        </Modal>
      </div>
    </div>
  );
};

export default PostList;
