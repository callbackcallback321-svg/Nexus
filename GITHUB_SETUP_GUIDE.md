# GitHub Setup Guide for Nexus Project

This guide will help you publish your Nexus project to GitHub.

## Prerequisites

1. **Git installed** - Check if Git is installed:
   ```powershell
   git --version
   ```
   If not installed, download from: https://git-scm.com/download/win

2. **GitHub account** - Create one at: https://github.com

## Step-by-Step Instructions

### Step 0: Configure Git (First Time Only)

If this is your first time using Git, configure your identity:

```powershell
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

**Important**: Use the same email address associated with your GitHub account!

### Step 1: Initialize Git Repository

Open PowerShell in your project directory and run:

```powershell
cd C:\Users\Office\Desktop\NEXUS
git init
```

### Step 2: Add All Files to Git

```powershell
git add .
```

### Step 3: Create Initial Commit

```powershell
git commit -m "Initial commit: Nexus project"
```

### Step 4: Create GitHub Repository

1. Go to https://github.com and sign in
2. Click the **"+"** icon in the top right corner
3. Select **"New repository"**
4. Fill in the details:
   - **Repository name**: `Nexus` (or your preferred name)
   - **Description**: "A Tool With Attractive Capabilities"
   - **Visibility**: Choose Public or Private
   - **DO NOT** initialize with README, .gitignore, or license (we already have these)
5. Click **"Create repository"**

### Step 5: Connect Local Repository to GitHub

After creating the repository, GitHub will show you commands. Use these (replace `YOUR_USERNAME` with your GitHub username):

```powershell
git remote add origin https://github.com/YOUR_USERNAME/Nexus.git
git branch -M main
git push -u origin main
```

**Note**: If you're using SSH instead of HTTPS:
```powershell
git remote add origin git@github.com:YOUR_USERNAME/Nexus.git
```

### Step 6: Push Your Code

```powershell
git push -u origin main
```

You'll be prompted for your GitHub username and password (or personal access token).

## Authentication Options

### Option 1: Personal Access Token (Recommended)

1. Go to GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Click "Generate new token (classic)"
3. Give it a name and select scopes: `repo` (full control)
4. Copy the token and use it as your password when pushing

### Option 2: GitHub CLI

Install GitHub CLI and authenticate:
```powershell
winget install GitHub.cli
gh auth login
```

## Future Updates

After making changes to your project:

```powershell
git add .
git commit -m "Description of your changes"
git push
```

## Useful Git Commands

- **Check status**: `git status`
- **View changes**: `git diff`
- **View commit history**: `git log`
- **Create new branch**: `git checkout -b feature-name`
- **Switch branches**: `git checkout branch-name`
- **Merge branch**: `git merge branch-name`

## Troubleshooting

### If you get "remote origin already exists":
```powershell
git remote remove origin
git remote add origin https://github.com/YOUR_USERNAME/Nexus.git
```

### If you need to force push (use with caution):
```powershell
git push -u origin main --force
```

### If you want to update your README on GitHub:
Make sure your README.md is in the root directory (it already is), and it will automatically display on your GitHub repository page.

## Next Steps

1. Add a license file (if needed)
2. Set up GitHub Actions for CI/CD (optional)
3. Add collaborators (if working in a team)
4. Create issues and project boards for task management
5. Enable GitHub Pages if you want to host documentation

---

**Need Help?** Check out GitHub's official documentation: https://docs.github.com

