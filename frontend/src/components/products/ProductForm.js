import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { productService } from '../../services/productService';
import toast from 'react-hot-toast';
import LoadingSpinner from '../common/LoadingSpinner';

const ProductForm = ({ product, categories, onSave, onCancel }) => {
  const [loading, setLoading] = useState(false);
  const [selectedImages, setSelectedImages] = useState([]);
  const [previewImages, setPreviewImages] = useState([]);

  const {
    register,
    handleSubmit,
    setValue,
    watch,
    formState: { errors }
  } = useForm({
    defaultValues: {
      name: product?.name || '',
      description: product?.description || '',
      price: product?.price || '',
      stock: product?.stock || '',
      categories: product?.categories?.map(cat => cat.id) || []
    }
  });

  const watchedCategories = watch('categories');

  const handleImageChange = (e) => {
    const files = Array.from(e.target.files);
    setSelectedImages(files);

    // Create preview URLs
    const previews = files.map(file => ({
      file,
      url: URL.createObjectURL(file)
    }));
    setPreviewImages(previews);
  };

  const removeImage = (index) => {
    const newImages = selectedImages.filter((_, i) => i !== index);
    const newPreviews = previewImages.filter((_, i) => i !== index);
    setSelectedImages(newImages);
    setPreviewImages(newPreviews);
  };

  const onSubmit = async (data) => {
    setLoading(true);
    try {
      const formData = {
        ...data,
        images: selectedImages
      };

      if (product) {
        await productService.updateProduct(product.id, formData);
        toast.success('Product updated successfully');
      } else {
        await productService.createProduct(formData);
        toast.success('Product created successfully');
      }
      
      onSave();
    } catch (error) {
      console.error('Error saving product:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label className="block text-sm font-medium text-gray-700">
            Product Name
          </label>
          <input
            {...register('name', { required: 'Product name is required' })}
            type="text"
            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            placeholder="Enter product name"
          />
          {errors.name && (
            <p className="mt-1 text-sm text-red-600">{errors.name.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700">
            Price
          </label>
          <input
            {...register('price', { 
              required: 'Price is required',
              min: { value: 0, message: 'Price must be positive' }
            })}
            type="number"
            step="0.01"
            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            placeholder="0.00"
          />
          {errors.price && (
            <p className="mt-1 text-sm text-red-600">{errors.price.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700">
            Stock
          </label>
          <input
            {...register('stock', { 
              required: 'Stock is required',
              min: { value: 0, message: 'Stock must be positive' }
            })}
            type="number"
            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            placeholder="0"
          />
          {errors.stock && (
            <p className="mt-1 text-sm text-red-600">{errors.stock.message}</p>
          )}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700">
            Categories
          </label>
          <select
            {...register('categories')}
            multiple
            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            size="4"
          >
            {categories.map(category => (
              <option key={category.id} value={category.id}>
                {category.name}
              </option>
            ))}
          </select>
          <p className="mt-1 text-sm text-gray-500">
            Hold Ctrl/Cmd to select multiple categories
          </p>
        </div>
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700">
          Description
        </label>
        <textarea
          {...register('description', { required: 'Description is required' })}
          rows={4}
          className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
          placeholder="Enter product description"
        />
        {errors.description && (
          <p className="mt-1 text-sm text-red-600">{errors.description.message}</p>
        )}
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700">
          Product Images
        </label>
        <input
          type="file"
          multiple
          accept="image/*"
          onChange={handleImageChange}
          className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
        />
        
        {/* Image Previews */}
        {previewImages.length > 0 && (
          <div className="mt-4">
            <p className="text-sm font-medium text-gray-700 mb-2">Image Previews:</p>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {previewImages.map((preview, index) => (
                <div key={index} className="relative">
                  <img
                    src={preview.url}
                    alt={`Preview ${index + 1}`}
                    className="h-24 w-24 object-cover rounded-lg"
                  />
                  <button
                    type="button"
                    onClick={() => removeImage(index)}
                    className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                  >
                    ×
                  </button>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Existing Images (for edit mode) */}
        {product && product.images && product.images.length > 0 && (
          <div className="mt-4">
            <p className="text-sm font-medium text-gray-700 mb-2">Current Images:</p>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {product.images.map((image, index) => (
                <div key={index} className="relative">
                  <img
                    src={`http://localhost:8080/storage/${image.path}`}
                    alt={`Current ${index + 1}`}
                    className="h-24 w-24 object-cover rounded-lg"
                  />
                </div>
              ))}
            </div>
          </div>
        )}
      </div>

      <div className="flex justify-end space-x-4">
        <button
          type="button"
          onClick={onCancel}
          className="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </button>
        <button
          type="submit"
          disabled={loading}
          className="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {loading ? <LoadingSpinner size="small" /> : (product ? 'Update' : 'Create')}
        </button>
      </div>
    </form>
  );
};

export default ProductForm;
