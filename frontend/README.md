# Laravel React Frontend

A modern React frontend for the Laravel API backend with authentication and CRUD functionality.

## Features

- **Authentication**: Register, Login, Logout with JWT tokens
- **Product Management**: Create, Read, Update, Delete products with images
- **Category Management**: Create, Read, Update, Delete categories
- **Image Upload**: Multiple image upload with previews
- **Responsive Design**: Built with Tailwind CSS
- **Form Validation**: Client-side validation with react-hook-form
- **Toast Notifications**: User feedback with react-hot-toast
- **Protected Routes**: Route protection with authentication

## Tech Stack

- **React 18**: Frontend framework
- **React Router DOM**: Client-side routing
- **Tailwind CSS**: Utility-first CSS framework
- **Axios**: HTTP client for API calls
- **React Hook Form**: Form handling and validation
- **React Hot Toast**: Toast notifications
- **Heroicons**: Icon library

## Project Structure

```
src/
├── components/
│   ├── auth/              # Authentication components
│   ├── categories/        # Category management
│   ├── common/            # Reusable components
│   └── products/          # Product management
├── contexts/              # React contexts
├── pages/                 # Main pages
└── services/              # API service functions
```

## Getting Started

### Prerequisites

- Node.js 16+ 
- npm or yarn
- Laravel backend running on http://localhost:8080

### Installation

1. Navigate to frontend directory:
   ```bash
   cd frontend
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Start the development server:
   ```bash
   npm start
   ```

4. Open [http://localhost:3000](http://localhost:3000) in your browser

### Environment Variables

Create a `.env` file in the frontend directory:

```
REACT_APP_API_URL=http://localhost:8080/api
```

## API Integration

The frontend communicates with the Laravel backend through RESTful APIs:

### Authentication Endpoints
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user` - Get current user

### Product Endpoints
- `GET /api/products` - List products (paginated)
- `POST /api/products` - Create product
- `GET /api/products/{id}` - Get single product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

### Category Endpoints
- `GET /api/categories` - List categories (paginated)
- `POST /api/categories` - Create category
- `GET /api/categories/{id}` - Get single category
- `PUT /api/categories/{id}` - Update category
- `DELETE /api/categories/{id}` - Delete category

## Authentication Flow

1. User registers or logs in
2. Backend returns JWT token
3. Token stored in localStorage
4. Token sent with all API requests via Authorization header
5. Protected routes check for valid token

## Component Overview

### Authentication
- `Login.js` - Login form with validation
- `Register.js` - Registration form with validation
- `ProtectedRoute.js` - Route protection wrapper

### Products
- `ProductList.js` - Display products with pagination
- `ProductForm.js` - Create/edit product form with image upload

### Categories
- `CategoryList.js` - Display categories in table format
- `CategoryForm.js` - Create/edit category form with slug generation

### Common Components
- `Navbar.js` - Navigation with authentication state
- `LoadingSpinner.js` - Loading indicators
- `Modal.js` - Reusable modal component
- `Pagination.js` - Pagination component

## Available Scripts

- `npm start` - Start development server
- `npm build` - Build for production
- `npm test` - Run tests
- `npm eject` - Eject from Create React App

## Features in Detail

### Product Management
- Create products with name, description, price, stock
- Upload multiple images with preview
- Assign categories to products
- Edit and delete products
- Paginated product listing

### Category Management
- Create categories with name, slug, description
- Auto-generate URL-friendly slugs
- Toggle active/inactive status
- Edit and delete categories
- Paginated category listing

### User Experience
- Responsive design for mobile and desktop
- Loading states for all async operations
- Error handling with user-friendly messages
- Form validation with helpful error messages
- Toast notifications for user feedback

## Deployment

1. Build the production version:
   ```bash
   npm run build
   ```

2. Serve the `build` directory with a web server

3. Update `REACT_APP_API_URL` to point to your production API

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request
