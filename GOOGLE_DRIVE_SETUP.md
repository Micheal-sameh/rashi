# Google Drive Integration Setup Guide

This guide will help you set up Google Drive integration for uploading competition ranking PDFs.

## Prerequisites

- A Google account
- Access to Google Cloud Console

## Step 1: Create a Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Note down your project name

## Step 2: Enable Google Drive API

1. In your project, go to "APIs & Services" > "Library"
2. Search for "Google Drive API"
3. Click on it and press "Enable"

## Step 3: Create OAuth 2.0 Credentials

1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "OAuth client ID"
3. If prompted, configure the OAuth consent screen:
   - Choose "External" for user type
   - Fill in the required fields (App name, User support email, Developer contact)
   - Add your email to test users
4. For Application type, choose "Web application"
5. Add authorized redirect URIs:
   - `http://localhost:8000/oauth2callback`
   - Your production URL if applicable
6. Click "Create"
7. Download the JSON file or copy the Client ID and Client Secret

## Step 4: Configure Environment Variables

Add the following to your `.env` file:

```env
GOOGLE_DRIVE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_DRIVE_CLIENT_SECRET=your-client-secret
GOOGLE_DRIVE_REDIRECT_URI=https://rashiwrafi.avarewase.com/oauth2callback
```

## Step 5: Generate Refresh Token

Run the following command:

```bash
php artisan google:drive-token
```

Follow the instructions:
1. Copy the URL shown in the terminal
2. Paste it in your browser
3. Sign in with your Google account
4. Grant permissions
5. You'll be redirected to a URL - copy the `code` parameter from the URL
6. Paste the code back into the terminal
7. Copy the refresh token shown and add it to your `.env`:

```env
GOOGLE_DRIVE_REFRESH_TOKEN=your-refresh-token-here
```

## Step 6: Verify the Setup

The PDFs will be uploaded to the Google Drive folder:
https://drive.google.com/drive/folders/1MQUSdtkV519Mv-dr60xzZruoOcVwaEst

Make sure the service account or OAuth user has access to this folder.

## Troubleshooting

### "Access denied" error
- Make sure the refresh token is valid
- Check if the Google Drive API is enabled
- Verify the OAuth consent screen is properly configured

### "File not found" error
- Verify the folder ID in `GoogleDriveService.php`
- Make sure the account has write access to the folder

### Refresh token not generated
- Revoke the app access from your Google account settings
- Run the command again with `prompt=consent` (already configured)

## How It Works

When a competition finishes:
1. The system generates a PDF ranking report
2. The PDF is uploaded to Google Drive
3. The file is set to "anyone with link can view"
4. An email is sent to group admins with the Google Drive link
5. The Excel report is still attached to the email

## Security Notes

- Keep your `.env` file secure
- Don't commit credentials to version control
- Rotate refresh tokens periodically
- Monitor API usage in Google Cloud Console
