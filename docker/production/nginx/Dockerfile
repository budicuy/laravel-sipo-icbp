# Stage 1: Build assets
FROM node:18-alpine AS builder

# Set working directory
WORKDIR /var/www

# Copy package files
COPY package*.json ./

# Install Node.js dependencies
RUN npm ci --only=production

# Copy Laravel application code
COPY . .

# Build assets
RUN npm run build

# Stage 2: Nginx production image
FROM nginx:alpine

# Install required packages
RUN apk add --no-cache \
    curl \
    && rm -rf /var/cache/apk/*

# Copy custom Nginx configuration
COPY docker/production/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/production/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy built assets from builder stage
COPY --from=builder /var/www/public/build /var/www/public/build

# Copy the Laravel application code
COPY . /var/www

# Set correct permissions
RUN chown -R nginx:nginx /var/www \
    && find /var/www -type d -exec chmod 755 {} \; \
    && find /var/www -type f -exec chmod 644 {} \; \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Create log directory
RUN mkdir -p /var/log/nginx \
    && chown -R nginx:nginx /var/log/nginx

# Switch to non-root user
USER nginx

# Expose port
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8080/ || exit 1

# Start Nginx
CMD ["nginx", "-g", "daemon off;"]
