# Odik Shop — UPDATE package

Extract into: /home/sameerth/odhikshop.shop/

## 1. Run the SQL first
cPanel > phpMyAdmin > sameerth_odikshop > SQL tab, paste deploy/SQL-pixel-steadfast.sql
(it adds 5 courier columns to the orders table).

## 2. Extract this zip over the site
Upload here, Extract, then delete the zip.

## 3. Clear the view cache
Delete every .php file inside storage/framework/views/ (keep .gitignore).

## What is in this release
- Admin > Pixel & Tracking  : Meta Pixel + Conversions API (server-side), GTM, GA4,
                              TikTok Pixel, custom head/body scripts.
- Admin > Steadfast Courier : API credentials, webhook, live ping / balance /
                              tracking, automatic or manual parcel booking.
- Order detail page         : Send to Steadfast + live courier status.

## Not included (on purpose)
- .env                      : would overwrite the live database password.
- storage/app/public/       : would overwrite uploaded product images.
