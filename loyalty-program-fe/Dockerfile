FROM node:20-alpine

# Set the working directory
WORKDIR /app

# Copy package.json and package-lock.json first
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of the application code
COPY . .

# Expose the port where the Vite development server will run
EXPOSE 5173

# Start the development server with host 0.0.0.0 for external access
CMD ["npm", "run", "dev", "--", "--host"]