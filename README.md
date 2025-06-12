# Post to Facebook

Automatically share your WordPress posts to your **Facebook Page** using the official Graph API.

## Features

- ✅ Auto-post WordPress posts to your Facebook Page
- ✏️ Customize post message format
- 🔒 Uses secure Facebook Graph API
- 🔁 Works with permanent page access tokens — no frequent re-auth needed

---

## 📦 Installation

1. Upload the plugin to your WordPress `/wp-content/plugins/` directory or install via **Plugins > Add New** in wp-admin.
2. Activate the plugin from the **Plugins** menu.
3. Go to **Settings > Post to Facebook** to configure.
4. Follow the instructions below to create a Facebook App and get your Page Access Token.

---

## 🛠️ Facebook App Setup & Page Token Generation

To connect your WordPress site to your Facebook Page, follow these steps:

### 🔧 Step 1: Create a Facebook App

1. Visit [https://developers.facebook.com/apps](https://developers.facebook.com/apps)
2. Click **"Create App"**
3. Choose **"Business"** or **"None"**
4. Enter your **App Name** (e.g., `WP Auto Poster`)
5. Provide contact email, then click **Create App**

---

### 🔑 Step 2: Add Required Permissions

Inside your new app:

1. Go to **App Review > Permissions and Features**
2. Add and request the following permissions:
   - `pages_manage_posts`
   - `pages_show_list`
   - `pages_read_engagement`
3. Switch your app to **Live Mode** (after review, if needed)

---

### 🔐 Step 3: Get a User Access Token

1. Go to **Tools > Access Token Tool** in the Developer Dashboard
2. Generate a token with scopes:
   ```
   pages_manage_posts, pages_show_list, pages_read_engagement, public_profile
   ```
3. Copy this short-lived **User Token**

---

### 📄 Step 4: Get Page Access Token

1. Go to [Graph API Explorer](https://developers.facebook.com/tools/explorer/)
2. Paste your **User Token**
3. Run:
   ```
   GET /me/accounts
   ```
4. Find the `access_token` for your desired Facebook Page — this is your **Page Access Token**

---

### ♾️ Step 5: Convert to Long-Lived Token (Optional but recommended)

If you want a long-lasting token:

1. Open this URL in a browser (replace placeholders):
   ```
   https://graph.facebook.com/v19.0/oauth/access_token?
   grant_type=fb_exchange_token&
   client_id=YOUR_APP_ID&
   client_secret=YOUR_APP_SECRET&
   fb_exchange_token=YOUR_SHORT_LIVED_USER_TOKEN
   ```
2. You'll get a **long-lived user token**
3. Use that token in **Graph API Explorer** again and run:
   ```
   GET /me/accounts
   ```
4. Copy the new `access_token` for your page — now it's **long-lived**

> ⚠️ Page tokens generally remain valid unless permissions are removed or the user's password is changed.

---

### 💾 Step 6: Save Page Token in WordPress

1. In WordPress Admin, go to: **Settings > Post to Facebook**
2. Paste:
   - Your **Facebook Page ID**
   - Your **Page Access Token**
3. Save changes — done!

---

## ❓ FAQ

### Does this plugin support Facebook Groups?
> No. Only **Facebook Pages** are supported via Graph API.

### Do I need to reauthenticate every few hours?
> No. A properly generated **long-lived Page Token** remains valid indefinitely.

### Does this plugin support custom post types?
> Not yet. Future updates may include this.

---


## 📃 License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).