# A.S Tasks

**Legend:** ✅ done · 🟡 partially done (see note) · ⬜ not started

> Progress notes added 2026-07-15. Items marked ✅/🟡 were actioned on branch
> `feature/as-tasks`. Homepage content edits (page/section/card changes) live in
> the local WordPress database; CSS/layout changes are committed in the child
> theme (`07_Source/Themes/ave-child/style.css`).

---

## HOMEPAGE

### Header
- ⬜ Follow picture made on ChatGPT for reference. Font should be green and bigger than the slideshow title and description. Should be centred. Remove car photo.

### Slideshow images
- ⬜ All images/descriptions/titles should be clickable to their designated page. All names should have "-" followed by their region/major city. If it's not possible to click, make a "see more" button below — ensure it's formatted correctly.
- ⬜ AI descriptions on all.
- ⬜ Add created border from most recent image creation on ChatGPT.
- ⬜ Berlin — Stock Photo ID: 1437602624 — Major cities page
- ⬜ Augsburg (Bavaria) — Bavaria page — Email Photo
- ⬜ Berchtesgadener Land (Bavaria) — Email Photo
- ⬜ Bamberg (Bavaria) — Stock Photo ID: 84577729
- ⬜ Heidelberg (The Black Forest region) — Stock Photo ID: 84651187
- ⬜ Black Forest Region Cake — Stock Photo ID: 75689197
- ⬜ Zell-Mosel (The Rhine, Moselle and Eifel) — Stock Photo ID: 84411985
- ⬜ Trier (The Rhine, Moselle and Eifel) — Stock Photo ID: 1105361522
- ⬜ Hamburg (Major cities of Germany) — Stock Photo ID: 47886229
- ⬜ Bastei bridge in Saxon Switzerland, Eastern Germany — Stock Photo ID: 1742066453
- ⬜ Colditz Castle (Special Interest Holidays) — Email Photo
- ⬜ Football camps in Bavaria (Special Interest Holidays) — Email Photo

### Testimonials (homepage section)
- ⬜ Sub-title: "loved by independent tourists" line under "and private groups". If it must be on two lines, split into defined areas as stated before. *(Awaiting exact wording from A.S — current heading is "Loved by independent travellers and private groups".)*
- ✅ Ensure tags are accurate — no groups, but a few special interest and tailor-made. *(Featured set is now Tailor-made ×2 + Special Interest ×1; the Groups testimonial was removed.)*
- 🟡 Ensure pics are accurate to the place the review is based on. If a review isn't based on a specific place, A.S will provide a photo and tag. *(The 3 featured reviews are place-based and use their region images (Bavaria, Rhine & Mosel, Black Forest). Awaiting A.S for any specific photo/tag swaps.)*
- ✅ Remove the stars.
- ✅ Only 3.
- ✅ Remove "what our travellers say".
- ✅ Get rid of initials in circle. *(Removed from the shared testimonial card, so it's gone on the Testimonials page too.)*

### Wide Choice of Destinations
- ✅ Rhine, Eastern buttons don't work. Get rid of "learn more" and make the photos clickable. *(Removed the "Learn More" button from all six cards and made the whole card clickable to its destination page — the four short cards (Rhine, Northern, Eastern, Major Cities) weren't clickable because their content sat above the card's overlay link. Also fixed each card's link, incl. Major Cities which pointed to the Rhine page. Bavaria & Black Forest descriptions still reveal on hover.)*

### Wide choice of options for the independent traveller
- ✅ Remove entirely. *(Removed both the mobile and desktop variants of the section, including all option buttons.)*

### Special Interest holidays
- ✅ Move Berlin Burlesque into its own card in the Special Interests section (Enquire Now). *(Added as a card matching the others; button reads ENQUIRE and links to the contact page. The old stand-alone Berlin Burlesque section was removed.)*
- ✅ Ensure all cards are in alphabetical order.
- ✅ Ensure all cards are the same size. *(Uniform width + image ratio; all cards equalised to the same height with the footer aligned.)*

### Single photo
- ✅ Remove the photo. *(Removed the stand-alone Eagle's Nest image.)*

### Special Events
- ✅ All cards should be the same size as the Special Interest holiday cards. *(Resized to match, converted to a clean centred static row, and equalised to the same height as the Special Interest cards.)*

### Blogs
- ✅ Add borders. *(Copied the Special Interest card border style; also pinned each "Read more" button to the bottom of its card.)*
- ✅ Ensure all cards are the same size. *(Equal-height cards with footers aligned.)*

### Second Footer
- ⬜ Centre the email.
- ⬜ Make it a little bigger.

---

## Testimonials Page
- ⬜ Remove "Traveller favourites".
- ⬜ 40+ years of expertise, not 30.
- ⬜ Remove "for thirty years", replace with "for more than 15 years".
- ⬜ Replace "what our travellers say" with "Planning bespoke holidays to Germany — Loved by our travellers" above the reviews. Remove "every testimonial" and "all reviews", replace with "testimonials".
- ⬜ Remove stars off cards.
- ⬜ Ensure tags are accurate.
- ⬜ Get rid of initial in circle.
- ⬜ Get rid of the second footer's light-green border; make it a bit smaller — but larger than the original homepage one.

---

## Business to Business page (Trade)
- ✅ Replace Blogs on the nav bar. *(Header nav now shows "Trade" in place of "Blog", linking to the new /trade/ page. Blog itself is untouched and still linked in the footer.)*
- 🟡 Create full design; follow the design of the Testimonials page apart from content. Header with images, and all footers, should be copied. *(The /trade/ page was created by cloning the Testimonials layout — top & bottom hero photos, the email section and the footer are all in place. Body content is placeholder for now; final design/copy still to do.)*
- 🟡 Header. *(Placeholder hero in place — real heading/eyebrow/CTA copy pending.)*
- 🟡 Introduction. *(Placeholder intro in place — real copy pending.)*
- 🟡 Contact me — this must be its own form on the same page as the contact form, with a slider next to "Get in touch" toggling between Individual enquiries / Business enquiries (defaulting to individual). The slider swaps the form; the individual form is the one that already exists.
  *(The trade/business enquiry form is specified and documented in `01_Documentation/TRADE_ENQUIRY_FORM.md` as a labelled Quform duplicate that reuses the existing email pipeline. **Build is blocked pending two values from A.S: the recipient email the current contact form uses, and the current Quform form ID.** The individual/business slider toggle on the existing Contact page is not yet built. Note: this toggle lives on the pre-existing Contact page, not the Trade page.)*
  - 🟡 Business form contains:
    - Business name
    - Email address
    - Phone number
    - Trade Enquiry
    *(Fields documented, not yet built in Quform.)*
