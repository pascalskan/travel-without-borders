# Technology Stack

Platform and dependency inventory for the Travel Without Borders website, as
recorded directly from the live site.

**Captured:** 25 June 2026
**Homepage:** <https://travelwithoutborders.co.uk>

## Platform

| Component     | Detail                                   |
| ------------- | ---------------------------------------- |
| CMS           | WordPress 7.0                            |
| Theme         | Ave (child theme) by Liquid Themes       |
| Theme add-ons | Ave Core v2.9.2, Ave Portfolio v1.0      |
| Page builder  | WPBakery / Visual Composer               |
| PHP           | 7.4.33                                   |

## Active Plugins (18)

| Plugin | Version |
| ------ | ------- |
| Akismet Anti-spam | 5.7 |
| Ave Core | 2.9.2 |
| Ave Portfolio | 1.0 |
| CAPTCHA 4WP | 7.6.0 |
| Contact Form 7 | 6.1.6 |
| Disable XML-RPC | 1.0.1 |
| Post SMTP | 3.9.4 |
| Quform | 2.9.3 |
| Really Simple Security | 9.6.0 |
| Redirection | 5.8.0 |
| Slider Revolution | 6.1.5 |
| Smush | 4.1.2 |
| Visual Composer Clipboard | 4.1.1 |
| Wordfence Security | 8.1.4 |
| WP Rocket | 3.18.2 |
| WPBakery Page Builder | 8.7.2 |
| Yoast Duplicate Post | 4.7 |
| Yoast SEO | 27.9 |

## Notes

- PHP 7.4.33 is end-of-life; custom code must remain compatible with it until
  the host is upgraded. See [DECISIONS.md](DECISIONS.md).
- Caching (WP Rocket) and image optimisation (Smush) are active — account for
  cache clearing during testing and deployment.
- This inventory should be re-verified before each major phase.
