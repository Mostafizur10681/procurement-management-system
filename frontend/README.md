# Frontend - Vue.js Application

This is the frontend application for the Procurement Management System, built with Vue 3, Vite, and Element Plus.

## Features

- **Modern Vue 3 Architecture**: Using Composition API and `<script setup>` syntax
- **Component-Based Design**: Reusable components with proper separation of concerns
- **State Management**: Pinia for centralized state management
- **UI Framework**: Element Plus for consistent and professional UI
- **Routing**: Vue Router for single-page application navigation
- **HTTP Client**: Axios for API communication with interceptors
- **Charts**: Chart.js for data visualization and analytics
- **Responsive Design**: Mobile-friendly interface

## Project Structure

```
frontend/
├── public/
│   ├── favicon.ico
│   └── index.html
├── src/
│   ├── assets/           # Static assets
│   ├── components/       # Reusable components
│   │   └── layout/       # Layout components
│   ├── router/           # Vue Router configuration
│   ├── services/         # API service layer
│   ├── stores/           # Pinia stores
│   ├── utils/            # Utility functions
│   ├── views/            # Page components
│   │   ├── auth/         # Authentication pages
│   │   ├── admin/        # Admin pages
│   │   ├── procurement/  # Procurement pages
│   │   ├── vendors/      # Vendor pages
│   │   └── reports/      # Reports pages
│   ├── App.vue           # Root component
│   └── main.js           # Application entry point
├── .gitignore
├── index.html
├── package.json
├── vite.config.js
└── README.md
```

## Getting Started

### Prerequisites
- Node.js 16+ 
- npm or yarn

### Installation

1. **Install dependencies**
   ```bash
   npm install
   ```

2. **Start development server**
   ```bash
   npm run dev
   ```

3. **Build for production**
   ```bash
   npm run build
   ```

4. **Preview production build**
   ```bash
   npm run preview
   ```

## Available Scripts

- `npm run dev` - Start development server with hot reload
- `npm run build` - Build for production
- `npm run preview` - Preview production build
- `npm run lint` - Run ESLint to check code quality

## Configuration

### Vite Configuration
The Vite configuration is in `vite.config.js`:
- Vue plugin support
- Path aliases (`@` points to `src/`)
- Development server proxy to backend API
- Build optimizations

### Environment Variables
Create a `.env.local` file for environment-specific variables:
```bash
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_NAME=Procurement Management System
```

## Key Components

### Layout Components
- `AppHeader.vue` - Application header with user menu
- `AppSidebar.vue` - Navigation sidebar with role-based menu items

### Authentication
- `Login.vue` - User login form
- `Register.vue` - User registration form

### Main Features
- `Dashboard.vue` - Main dashboard with charts and statistics
- `ProcurementRequests.vue` - List and manage procurement requests
- `CreateProcurementRequest.vue` - Form to create new requests
- `ProcurementRequestDetail.vue` - Detailed view of requests
- `Vendors.vue` - Vendor management interface
- `Reports.vue` - Analytics and reporting dashboard

## State Management

### Pinia Stores
- `auth.js` - User authentication and authorization state
- Additional stores can be added for specific features

### API Services
- `auth.js` - Authentication API calls
- `procurement.js` - Procurement-related API calls

## Routing

### Route Structure
- `/login` - Login page
- `/register` - Registration page
- `/dashboard` - Main dashboard
- `/procurement-requests` - Procurement requests list
- `/procurement-requests/create` - Create new request
- `/vendors` - Vendor management
- `/reports` - Analytics and reports
- `/admin/*` - Admin-only pages

### Route Guards
- Authentication required for protected routes
- Role-based access control for admin/manager pages
- Redirect logic for authenticated/unauthenticated users

## UI Components

### Element Plus
The application uses Element Plus for UI components:
- Forms and form validation
- Tables with pagination
- Dialogs and modals
- Charts and data visualization
- Navigation and layout components

### Custom Components
- Layout components for consistent structure
- Form components with validation
- Table components with custom actions
- Chart components for data visualization

## API Integration

### HTTP Client Configuration
- Axios instance with base URL configuration
- Request interceptors for authentication headers
- Response interceptors for error handling
- Automatic token management

### Error Handling
- Global error handling for API responses
- User-friendly error messages
- Automatic logout on authentication failure

## Development Guidelines

### Code Style
- Use Vue 3 Composition API with `<script setup>`
- Follow Vue.js style guide
- Use TypeScript where applicable (future enhancement)
- Proper component naming and file organization

### Best Practices
- Component reusability and modularity
- Proper state management patterns
- Efficient API usage with caching
- Responsive design principles
- Accessibility considerations

### Performance
- Lazy loading for routes and components
- Optimized bundle size with Vite
- Efficient reactivity patterns
- Proper memory management

## Deployment

### Build Process
1. Run `npm run build` to create production assets
2. Deploy `dist/` folder to web server
3. Configure web server for SPA routing
4. Set up proper caching headers

### Server Configuration
Example Nginx configuration for SPA:
```nginx
location / {
    try_files $uri $uri/ /index.html;
}
```

## Contributing

1. Follow the established code style
2. Write meaningful component names
3. Add proper comments for complex logic
4. Test thoroughly before submitting changes
5. Consider performance implications

## Troubleshooting

### Common Issues
- **CORS errors**: Ensure backend allows frontend origin
- **404 errors**: Check API endpoint configuration
- **Build failures**: Verify all dependencies are installed
- **Hot reload issues**: Restart development server

### Debug Tips
- Use browser dev tools for component inspection
- Check network tab for API calls
- Use Vue DevTools for state inspection
- Enable verbose logging in development

## Future Enhancements

- TypeScript migration
- Unit testing with Vitest
- E2E testing with Cypress
- PWA capabilities
- Offline support
- Real-time notifications
- Advanced filtering and search
- Export functionality enhancements

---

For more information, refer to the main project README.md file.
