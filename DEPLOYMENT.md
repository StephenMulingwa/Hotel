# Hotel Management System - Render Deployment Guide

## Prerequisites
1. A GitHub account
2. A Render account (free tier available)
3. Your code pushed to a GitHub repository

## Step 1: Prepare Your Repository

1. **Push your code to GitHub:**
   ```bash
   git init
   git add .
   git commit -m "Initial commit - Hotel Management System"
   git branch -M main
   git remote add origin https://github.com/yourusername/hotel-management.git
   git push -u origin main
   ```

## Step 2: Deploy to Render

### Option A: Using Render Dashboard (Recommended)

1. **Go to [Render Dashboard](https://dashboard.render.com)**
2. **Click "New +" → "Web Service"**
3. **Connect your GitHub repository**
4. **Configure the service:**
   - **Name:** `hotel-management`
   - **Environment:** `PHP`
   - **Build Command:** Leave empty (no build step needed)
   - **Start Command:** `php -S 0.0.0.0:$PORT -t .`
   - **Plan:** Free

### Option B: Using render.yaml (Automated)

1. **The `render.yaml` file is already configured**
2. **Render will automatically detect it and use the configuration**

## Step 3: Database Setup

1. **In Render Dashboard, go to "New +" → "PostgreSQL"**
2. **Configure the database:**
   - **Name:** `hotel-db`
   - **Database:** `hotel_management`
   - **User:** `hotel_user`
   - **Plan:** Free

3. **Connect the database to your web service:**
   - Go to your web service settings
   - Add environment variable: `DATABASE_URL` (Render will provide this automatically)

## Step 4: Environment Variables

Add these environment variables in your Render web service:

```
APP_ENV=production
APP_DEBUG=false
PHP_VERSION=8.1
```

## Step 5: Deploy

1. **Click "Create Web Service"**
2. **Wait for deployment to complete** (usually 2-3 minutes)
3. **Your app will be available at:** `https://your-app-name.onrender.com`

## Step 6: Initialize Database

1. **Visit:** `https://your-app-name.onrender.com/setup`
2. **This will create all necessary tables and seed initial data**
3. **Default admin credentials:**
   - **Email:** `admin@hotel.com`
   - **Password:** `password`

## Step 7: Access Your Application

1. **Main Application:** `https://your-app-name.onrender.com`
2. **Admin Dashboard:** `https://your-app-name.onrender.com/admin`
3. **Login with admin credentials**

## Troubleshooting

### Common Issues:

1. **Database Connection Error:**
   - Ensure `DATABASE_URL` environment variable is set
   - Check that PostgreSQL service is running

2. **500 Internal Server Error:**
   - Check Render logs for specific error messages
   - Ensure all PHP files are properly uploaded

3. **Page Not Found (404):**
   - Verify that `index.php` is in the root directory
   - Check that the start command is correct

### Checking Logs:

1. **Go to your Render service dashboard**
2. **Click on "Logs" tab**
3. **Look for error messages and debug information**

## File Structure

```
/
├── app/
│   ├── Controllers/
│   ├── Views/
│   └── Support/
├── public/
├── storage/
├── scripts/
├── index.php
├── bootstrap.php
├── config.php
├── composer.json
├── render.yaml
├── database_schema.sql
└── .renderignore
```

## Features Available After Deployment:

- ✅ **Customer Management**
- ✅ **Room Booking System**
- ✅ **Food Ordering**
- ✅ **Admin Dashboard**
- ✅ **Staff Management**
- ✅ **Chat System**
- ✅ **Payment Processing**
- ✅ **Review System**
- ✅ **Analytics**

## Security Notes:

1. **Change default admin password** after first login
2. **Update hotel settings** with your actual information
3. **Configure proper email settings** for production
4. **Enable HTTPS** (automatically provided by Render)

## Support:

If you encounter any issues:
1. Check the Render logs
2. Verify all environment variables are set
3. Ensure the database is properly connected
4. Contact support if needed

---

**Your Hotel Management System is now live on Render! 🎉**
