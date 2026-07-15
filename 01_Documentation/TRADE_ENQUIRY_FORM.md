# Trade / Business Enquiry Form (Quform)

How to add a **Business-to-Business "Trade Enquiry" form** by duplicating the
existing Quform contact form, so trade submissions reuse the working email
pipeline but are **clearly labelled** and never mixed in with individual
enquiries.

> **Why this lives in a doc, not in code.** Quform stores every form definition,
> field, and notification-email setting **in the WordPress database** — not in
> this repository (which version-controls only the child theme and docs). So the
> form is built in **WP-admin → Forms (Quform)**, and this file is the
> reproducible, reviewable record of exactly what to configure. See
> [Tech Stack](TECH_STACK.md) and the [Customisation Audit](CUSTOMISATION_AUDIT.md).

Related task: `A.S tasks.md` → *Business to Business page → contact me*.

---

## Objective

- A second enquiry form for trade/business contacts.
- Fields: **Business name, Email address, Phone number, Trade Enquiry** (message).
- Delivered to the **same business inbox** the current contact form already
  emails (the outbound path is Post SMTP — no new plumbing required).
- Every trade submission is unmistakably a **trade** enquiry, both in the inbox
  and in the Quform entries list, so it is never confused with an individual
  enquiry.

The front-end **slider toggle** ("Get in touch" → Individual / Business) lives on
the **pre-existing Contact page** and swaps between the two forms there. It is a
**separate front-end task** — see [Placement](#placement-existing-contact-page)
below. This document covers the form and its labelling only. (The Business-to-
Business page is unrelated future work, tracked separately.)

---

## Before you start

- [ ] **Export Quform entries first.** Quform holds ~190 live enquiry entries —
      protect them before touching the plugin. See
      [Backup Strategy](BACKUP_STRATEGY.md) and
      [Customisation Audit](CUSTOMISATION_AUDIT.md).
- [ ] Confirm the **recipient address** the current contact form's notification
      sends to (WP-admin → Forms → *General Contact Form* → **Email** →
      Notification → *To*). Reuse that exact address so nothing new has to be
      verified in Post SMTP. _Record it here once confirmed:_ `__________`.
- [ ] Note the current form's **ID** (shown in the Forms list / shortcode, e.g.
      `[quform id="1" name="..."]`). _Record it here:_ `__________`.
- [ ] Do this in **staging/Local first** if available
      ([Local Dev Workflow](LOCAL_DEV_WORKFLOW.md)), then repeat in production.

---

## Step 1 — Duplicate the existing contact form

1. WP-admin → **Forms** (Quform).
2. Find the current individual contact form (**General Contact Form**).
3. Use the row action **Duplicate** (Quform 2.9.x provides a duplicate/copy
   action per form). This copies the form **and its working notification email
   settings**, so the outbound pipeline is inherited rather than rebuilt.
4. Open the copy and **rename it** to `Trade Enquiry — Business` (a distinct name
   keeps its entries filed under a separate form in the entries screen — the
   first layer of "not mixed in").

---

## Step 2 — Set the business fields

Edit the duplicated form's fields so it contains exactly (in order):

| Field           | Quform element | Required | Notes |
| --------------- | -------------- | -------- | ----- |
| Business name   | Single Line Text | Yes    | Label: **Business name** |
| Email address   | Email          | Yes      | Label: **Email address** |
| Phone number    | Phone / Single Line Text | Yes | Label: **Phone number** |
| Trade Enquiry   | Paragraph Text (textarea) | Yes | Label: **Trade Enquiry** — this is the message body |

Remove any individual-only fields carried over from the original (e.g. name,
travel dates, destination, number of travellers) so the trade form is clean.

Keep any **spam/CAPTCHA** element the original had (CAPTCHA 4WP / Quform's own),
and keep the **submit button**. Do not remove hidden/system fields Quform needs.

---

## Step 3 — Label submissions as TRADE (do all three)

Use layered labelling so a trade enquiry is obvious wherever it appears:

**3a. Notification email subject — the primary signal.**
Form → **Email** → the notification that goes to the business inbox → **Subject**:

```
[TRADE ENQUIRY] New business enquiry from %businessname%
```

Use Quform's field variable for the business-name value (insert via the
"Add variable" picker so the token matches the actual field key — the label
above is illustrative). The `[TRADE ENQUIRY]` prefix lets the inbox filter/sort
these apart from individual enquiries at a glance.

**3b. A fixed "Enquiry Type" value in the entry & email body.**
Add a **Hidden** field named `Enquiry Type` with a fixed default value of
`Trade`, and make sure it is included in the notification email template. This
tags the stored entry and the email body, so even if a subject line is edited
downstream the classification survives.

**3c. Distinct confirmation / reply context (optional but recommended).**
If the original sends the enquirer an auto-reply, review its wording so a
business contact gets a business-appropriate confirmation rather than the
individual-holiday copy.

> **Net effect:** trade enquiries arrive under a separate form name, with a
> `[TRADE ENQUIRY]` subject prefix, and carry an `Enquiry Type: Trade` tag in
> both the entry and the email body — three independent ways to keep them out of
> the individual-enquiry stream.

---

## Step 4 — Confirm delivery settings

- **To:** the business inbox recorded in [Before you start](#before-you-start).
- **From / Reply-To:** match the original form's working configuration (Post SMTP
  handles sending; do not change the authenticated From address, or delivery can
  break silently — see [Customisation Audit](CUSTOMISATION_AUDIT.md)).
- Leave Post SMTP untouched — no new sender address is introduced.

---

## Placement (existing Contact page)

- Both forms live on the **pre-existing Contact page**. Embed the new trade form
  with its Quform shortcode / the Quform WPBakery element:
  `[quform id="<new-id>" name="Trade Enquiry — Business"]`.
- The **individual form is the one that already exists** on that page — embed it
  unchanged for the "Individual" state.
- The **slider toggle** that shows one form or the other under a single
  "Get in touch" heading is front-end work (show/hide the two embedded forms on
  the Contact page). Track it as its own task; it does not change any Quform
  config. When built, it should default to **Individual enquiries**, per
  `A.S tasks.md`.
- The Business-to-Business page is a separate future build and does **not** host
  this toggle.

> Styling note: Quform renders with off-brand defaults (lime-green `#89C712`,
> 5px radius) per the [Design System Audit](DESIGN_SYSTEM_AUDIT.md). Any visual
> alignment to the brand applies equally to both forms and is a separate styling
> pass.

---

## Test checklist

- [ ] Submit the trade form with valid data → email arrives at the business inbox.
- [ ] Subject line begins with `[TRADE ENQUIRY]`.
- [ ] Email body shows Business name, Email, Phone, Trade Enquiry, and
      `Enquiry Type: Trade`.
- [ ] The entry is stored under the **Trade Enquiry — Business** form, separate
      from individual entries.
- [ ] Required-field validation and spam/CAPTCHA still work.
- [ ] Submit the **individual** form → still behaves and is labelled as before
      (no regression to the existing pipeline).
- [ ] Clear WP Rocket cache after publishing the page.

---

## Rollback

The trade form is an independent duplicate — it shares no fields with the
original. To roll back, **delete the "Trade Enquiry — Business" form** (or unset
its page embed). The original General Contact Form and its 190 entries are
untouched throughout.
