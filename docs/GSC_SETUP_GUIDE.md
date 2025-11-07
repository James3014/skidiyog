# Google Search Console (GSC) Setup Guide

## Overview
This guide will help you set up Google Search Console for tracking and optimizing your website's search performance.

## Prerequisites
- Google account
- Access to https://skidiyog.zeabur.app
- GA4 Measurement ID (for cross-domain tracking)

---

## Step 1: Verify Website Ownership

### Method 1: HTML File Upload (Recommended)
1. Go to [Google Search Console](https://search.google.com/search-console)
2. Click "Add Property" → "URL prefix"
3. Enter: `https://skidiyog.zeabur.app`
4. Choose "HTML file" verification method
5. Download the verification file (e.g., `googleXXXXXXXXXXXXXXXX.html`)
6. Upload to website root directory
7. Click "Verify" in GSC

### Method 2: Meta Tag (Alternative)
1. Follow steps 1-3 above
2. Choose "HTML tag" method
3. Copy the meta tag: `<meta name="google-site-verification" content="XXXXX" />`
4. Add to `pageHeader.php` in `<head>` section
5. Deploy changes to Zeabur
6. Click "Verify" in GSC

### Method 3: GA4 Property (If GA4 Already Configured)
1. Ensure GA4 tracking is active on the site
2. In GSC verification, choose "Google Analytics"
3. GSC will auto-verify if you have admin access to GA4

---

## Step 2: Submit Sitemap

1. In GSC, navigate to your verified property
2. Go to "Sitemaps" in left sidebar
3. Enter sitemap URL: `https://skidiyog.zeabur.app/sitemap.php`
4. Click "Submit"
5. Wait 1-3 days for Google to crawl

### Sitemap Details
- **Dynamic Sitemap**: Auto-generated from database
- **Updates**: Real-time (reflects database changes immediately)
- **Included Pages**:
  - Homepage (/)
  - Park pages (23 parks)
  - Instructor pages (91 instructors)
  - Article pages (30 articles)
  - Static pages (parkList, instructorList, articleList)

---

## Step 3: Configure Cross-Domain Tracking (Optional)

If you want to track user journeys across `skidiyog.zeabur.app` and `diy.ski`:

1. **Set up diy.ski property** in GSC separately
2. **Link GA4 to both properties**:
   - In GSC → Settings → Associations
   - Link to the same GA4 property for both domains
3. **Verify cross-domain tracking** in GA4:
   - GA4 → Admin → Data Streams → Configure tag settings
   - Configure your domains: `skidiyog.zeabur.app`, `diy.ski`, `www.diy.ski`

---

## Step 4: Monitor Search Performance

### Key Metrics to Track
1. **Total Clicks**: Users clicking from Google search results
2. **Total Impressions**: How often your site appears in search
3. **Average CTR**: Click-through rate (clicks/impressions)
4. **Average Position**: Your ranking in search results

### Recommended Reports
- **Performance Report**: Overall search performance
- **Pages Report**: Top-performing pages
- **Queries Report**: Keywords driving traffic
- **Coverage Report**: Indexing status and errors

---

## Step 5: Fix Common Issues

### Issue: "Discovered - currently not indexed"
**Solution**:
- Ensure page has quality content
- Check robots.txt doesn't block the page
- Request indexing via "URL Inspection" tool

### Issue: "Crawled - currently not indexed"
**Solution**:
- Improve page content quality
- Add internal links to the page
- Wait (Google may index later)

### Issue: "Excluded by 'noindex' tag"
**Expected for**:
- /bkAdmin/* (admin pages)
- /login.php
- All debug/test files

**Not expected for**:
- Public pages (parks, articles, instructors)
- If public pages are noindex, check for accidental meta tags

---

## Step 6: Request Indexing for Important Pages

### Priority Pages to Index First
1. Homepage: `https://skidiyog.zeabur.app/`
2. Park List: `https://skidiyog.zeabur.app/parkList.php`
3. Top Parks:
   - Naeba: `https://skidiyog.zeabur.app/park.php?name=naeba`
   - Hakuba: `https://skidiyog.zeabur.app/park.php?name=hakuba`
   - Nozawa: `https://skidiyog.zeabur.app/park.php?name=nozawa`
4. Instructor List: `https://skidiyog.zeabur.app/instructorList.php`
5. Article List: `https://skidiyog.zeabur.app/articleList.php`

### How to Request Indexing
1. In GSC, go to "URL Inspection" (top search bar)
2. Enter full URL (e.g., `https://skidiyog.zeabur.app/parkList.php`)
3. Wait for inspection to complete
4. If "URL is not on Google", click "Request Indexing"
5. Repeat for each priority page

---

## Step 7: GA4 Integration

### Update GA4 Measurement ID
1. Get your GA4 Measurement ID (format: `G-XXXXXXXXXX`)
2. Edit file: `includes/ga4_tracking.php`
3. Update line 12:
   ```php
   $GA4_MEASUREMENT_ID = 'G-YOUR-ACTUAL-ID';
   ```
4. Deploy to Zeabur

### Verify GA4 Tracking
1. Visit your website with browser DevTools open (F12)
2. Go to Console tab
3. Look for logs: `[GA4] Custom events initialized`
4. Test a booking button click, should see: `[GA4] Booking intent tracked`

---

## Step 8: Set Up Alerts (Optional)

### Recommended Alerts
1. **Indexing Issues Alert**
   - GSC → Settings → Preferences → Email notifications
   - Enable "Coverage issues"
   - Notifies when pages are blocked or have errors

2. **Manual Actions Alert**
   - Enable "Manual actions" notifications
   - Critical: notifies if Google penalizes your site

3. **Search Performance Drop Alert**
   - Use GA4 anomaly detection
   - Set up custom alert for >20% traffic drop

---

## Maintenance Checklist

### Weekly
- [ ] Check "Performance" report for traffic trends
- [ ] Review new "Coverage" errors (if any)

### Monthly
- [ ] Analyze top-performing queries and pages
- [ ] Request indexing for new important pages
- [ ] Review mobile usability issues
- [ ] Check Core Web Vitals report

### Quarterly
- [ ] Full site audit via "URL Inspection" tool
- [ ] Review and update FAQ content based on search queries
- [ ] Analyze competitor rankings for target keywords

---

## Useful Resources

- [Google Search Console Help](https://support.google.com/webmasters)
- [SEO Starter Guide](https://developers.google.com/search/docs/fundamentals/seo-starter-guide)
- [GA4 Cross-Domain Tracking](https://support.google.com/analytics/answer/10071811)
- [Sitemap Best Practices](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap)

---

## Troubleshooting

### Q: Sitemap shows errors in GSC
**A**: Check `https://skidiyog.zeabur.app/sitemap.php` in browser. Should see valid XML. If blank, check database connection.

### Q: Pages not appearing in Google after 2 weeks
**A**:
1. Check `robots.txt` doesn't block them
2. Ensure no `noindex` meta tag
3. Request indexing manually
4. Add internal links to the page from homepage

### Q: GA4 not tracking events
**A**:
1. Check browser console for errors
2. Verify `$GA4_MEASUREMENT_ID` is set correctly
3. Use [GA4 DebugView](https://support.google.com/analytics/answer/7201382) to debug

### Q: Cross-domain tracking not working
**A**:
1. Verify both domains are in GA4 configuration
2. Check `linker` domains in `ga4_tracking.php`
3. Test by clicking link from one domain to another, check if `_gl` parameter appears in URL

---

**Last Updated**: 2025-11-08
**Author**: SKIDIY Tech Team
