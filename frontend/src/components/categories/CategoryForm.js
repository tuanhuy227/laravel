import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { categoryService } from '../../services/categoryService';
import toast from 'react-hot-toast';
import LoadingSpinner from '../common/LoadingSpinner';

const CategoryForm = ({ category, onSave, onCancel }) => {
  const [loading, setLoading] = useState(false);

  const {
    register,
    handleSubmit,
    watch,
    setValue,
    formState: { errors }
  } = useForm({
    defaultValues: {
      name: category?.name || '',
      slug: category?.slug || '',
      description: category?.description || '',
      status: category?.status ?? true
    }
  });

  const watchedName = watch('name');

  // Auto-generate slug from name
  React.useEffect(() => {
    if (watchedName && !category) {
      const slug = watchedName
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
      setValue('slug', slug);
    }
  }, [watchedName, setValue, category]);

  const onSubmit = async (data) => {
    setLoading(true);
    try {
      if (category) {
        await categoryService.updateCategory(category.id, data);
        toast.success('Category updated successfully');
      } else {
        await categoryService.createCategory(data);
        toast.success('Category created successfully');
      }
      
      onSave();
    } catch (error) {
      console.error('Error saving category:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <div>
        <label className="block text-sm font-medium text-gray-700">
          Category Name
        </label>
        <input
          {...register('name', { required: 'Category name is required' })}
          type="text"
          className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
          placeholder="Enter category name"
        />
        {errors.name && (
          <p className="mt-1 text-sm text-red-600">{errors.name.message}</p>
        )}
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700">
          Slug
        </label>
        <input
          {...register('slug', { 
            required: 'Slug is required',
            pattern: {
              value: /^[a-z0-9]+(?:-[a-z0-9]+)*$/,
              message: 'Slug must be lowercase letters, numbers, and hyphens only'
            }
          })}
          type="text"
          className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
          placeholder="category-slug"
        />
        {errors.slug && (
          <p className="mt-1 text-sm text-red-600">{errors.slug.message}</p>
        )}
        <p className="mt-1 text-sm text-gray-500">
          This will be used in URLs. It should be unique and URL-friendly.
        </p>
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700">
          Description
        </label>
        <textarea
          {...register('description')}
          rows={4}
          className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
          placeholder="Enter category description (optional)"
        />
      </div>

      <div className="flex items-center">
        <input
          {...register('status')}
          type="checkbox"
          className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
        />
        <label className="ml-2 block text-sm text-gray-900">
          Active
        </label>
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
          {loading ? <LoadingSpinner size="small" /> : (category ? 'Update' : 'Create')}
        </button>
      </div>
    </form>
  );
};

export default CategoryForm;
