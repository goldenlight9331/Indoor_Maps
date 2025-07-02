# Dockerfile
FROM node:20.13.1 AS build

# Set environment variables during the build process
ENV VITE_LARAVEL_URL=http://localhost:8001

WORKDIR /app

COPY ./package.json ./package-lock.json ./
RUN npm install

COPY ./ ./
RUN npm run build



FROM nginx:alpine
COPY --from=build /app/dist /usr/share/nginx/html
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 5173
CMD ["nginx", "-g", "daemon off;"]