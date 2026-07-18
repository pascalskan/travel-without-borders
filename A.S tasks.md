# A.S Tasks

**Legend:** ✅ done · 🟡 partially done (see note) · ⬜ not started

> Progress notes added 2026-07-15. Items marked ✅/🟡 were actioned on branch
> `feature/as-tasks`. Homepage content edits (page/section/card changes) live in
> the local WordPress database; CSS/layout changes are committed in the child
> theme (`07_Source/Themes/ave-child/style.css`).

---

## HOMEPAGE

### Header
- ✅ Follow picture made on ChatGPT for reference. Font should be green and bigger than the slideshow title and description. Should be centred. Remove car photo. *(Intro (heading + welcome + Learn More) moved above the slideshow, all in one grey section; text centred and enlarged (heading 44px, welcome 23px); car image removed. Colours follow the reference image — dark heading + green welcome text; say if you'd rather the heading itself be green.)*

### Slideshow images
- ✅ All images/descriptions/titles should be clickable to their designated page. All names should have "-" followed by their region/major city. *(All 12 slides rebuilt: the whole slide links to its destination page, and titles are "Name – Region", e.g. "Berlin – Major Cities".)*
- ✅ AI descriptions on all. *(One-line description written for every slide.)*
- ✅ Add created border from most recent image creation on ChatGPT. *(Added a green "matted frame" around each slide image matching the reference — a green frame with a thin grey gap. Slides now fill the frame (centre-cropped) so every image sits neatly inside it.)*
- 🟡 The 12 slide photos. *(Built with the images A.S selected. Only Trier used the exact stock photo (1105361522) — the other 7 stock IDs were not in the media library, so existing/selected images were used per A.S's picks. Slot 6 used the existing full-size Black Forest gateau. Note: Augsburg (390×390), Colditz moody (450×400), Berchtesgadener Land (640×480) and Bamberg (1280×426) are low-res/odd-ratio and may want higher-res replacements.)*
  - ✅ Berlin — twilight Brandenburg Gate → Major Cities (Berlin) page
  - ✅ Augsburg (Bavaria) — email photo → Augsburg page
  - ✅ Berchtesgadener Land (Bavaria) — "SLOT 3" email photo → Berchtesgadener Land page
  - ✅ Bamberg (Bavaria) — email photo → Bamberg page
  - ✅ Heidelberg — existing image → Heidelberg page
  - ✅ Black Forest Cake — existing black-forest-gateau → Black Forest page
  - ✅ Zell-Mosel — Zell-Mosel-new → Zell-Mosel page
  - ✅ Trier — exact stock 1105361522 → Trier page
  - ✅ Hamburg — Hamburg.jpg → Hamburg page
  - ✅ Bastei bridge — bastei.jpg → Eastern Germany page (no own page)
  - ✅ Colditz Castle — moody b&w email photo → Colditz page
  - ✅ Football camps in Bavaria — "football V1" (9000×6000) → Football Camps page

### Testimonials (homepage section)
- ✅ Sub-title: "loved by independent tourists" line under "and private groups". If it must be on two lines, split into defined areas as stated before. *(Heading changed to "Loved by independent tourists and private groups"; when it wraps it now breaks as "Loved by independent tourists" / "and private groups".)*
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
- ✅ Centre the email. *(Replaced the old off-centre `ld_icon_box` with the standard `twb_email_strip` component used on every other page — now centred.)*
- ✅ Make it a little bigger. *(The email address is larger in the standard strip; the whole homepage email section now matches the other pages.)*

---

## Testimonials Page
- ✅ Remove "Traveller favourites". *(Featured carousel heading cleared.)*
- ✅ 40+ years of expertise, not 30. *(Trust stat now reads 40+.)*
- ✅ Remove "for thirty years", replace with "for more than 15 years". *(Hero intro updated.)*
- ✅ Replace "what our travellers say" with "Planning bespoke holidays to Germany – Loved by our travellers" above the reviews. Remove "every testimonial" and "all reviews", replace with "testimonials". *(Hero heading updated; the grid section now just reads "Testimonials".)*
- ✅ Remove stars off cards. *(show_rating turned off on both the carousel and the grid.)*
- ✅ Ensure tags are accurate. *(Real testimonials added, each tagged by section: J Shaw = Tailor-made / Mosel Valley; A Corbin & Karen = Special Interest / Colditz; Schott = Tailor-made / Idstein.)*
- ✅ Get rid of initial in circle. *(Already removed from the shared testimonial card.)*
- ✅ Card layout reworked per A.S. *(Featured "Traveller favourites" carousel removed entirely. The page is now a single uniform grid of equal-height cards (480px). The two long reviews — J Shaw and The Schott Family — are "wide" cards spanning two columns (cards 1–2 of their row), with A Corbin and Karen as normal single cards beside them. Each card shows a teaser of the review; if it doesn't fit, a "Show more" button appears and opens a pop-up with an enlarged image + the full review. Short reviews that fit show no button.)*

---

## Business to Business page (Trade)
- ✅ Replace Blogs on the nav bar. *(Header nav now shows "Trade" in place of "Blog", linking to the new /trade/ page. Blog itself is untouched and still linked in the footer.)*
- ✅ Create full design; follow the design of the Testimonials page apart from content. Header with images, and all footers, should be copied. *(Layout follows the Testimonials page — top hero, trust-stats strip, two body bands, closing hero, email strip and footer. Full trade content now written (see below). NOTE: the hero photos are still the placeholder Neuschwanstein (top) / Dresden (bottom); the "Header/footer" task under V2 (top → Augsburg, bottom → TBC) is separate and pending A.S's bottom photo.)*
- ✅ Header. *(Eyebrow "Travel Trade"; H1 "A Germany specialist for the travel trade"; intro explaining TWB partners with agents, tour operators and group organisers to plan tailor-made Germany trips for their clients; CTA "Make a trade enquiry" → the contact page's Business form.)*
- ✅ Introduction. *(Written in the site's voice from a full read of About/Homepage/Tailor-made/Groups. Trust stats (30+ years Germany-only · 7 regions · ATOL bonded); "Why partner with us" section; "How we can help you" with a 7-point list (tailor-made itineraries, groups, special interest, special events, all travel options, all accommodation tiers, ATOL protection); "Let's work together" closing CTA. The two CTAs deep-link to /contact/?enquiry=business, which now opens straight on the Business/Trade form. "30+ years" follows the About page — flag if you'd rather match the Testimonials "40+".)*
- ✅ Contact me — its own form on the same page as the contact form, with a slider next to "Get in touch" toggling between Individual enquiries / Business enquiries (defaulting to individual). The slider swaps the form; the individual form is the one that already exists.
  *(Built and tested on Local. The two values previously "blocked on A.S" were found in the local DB — the contact form delivers to `mail@travelwithoutborders.co.uk`, and the individual form is Quform `id=1` ("Tailor-made Holidays Form"). The Trade form is Quform `id=3` ("Trade Enquiry — Business"), created by duplicating form 1 via Quform's own API, with relabelled fields and a `[TRADE ENQUIRY]` subject prefix, delivering to the same inbox. A new child-theme element `[twb_enquiry_toggle]` (inc/enquiry-toggle.php + own CSS/JS) renders both forms under a segmented Individual/Business switch defaulting to Individual; the Contact page (ID 4014) embeds it. Both SEND buttons normalised to the brand green. See `01_Documentation/TRADE_ENQUIRY_FORM.md`.)*
  - ✅ Business form contains:
    - Business name
    - Email address
    - Phone number
    - Trade Enquiry
    *(Built as Quform form id=3; labels confirmed live.)*
  - ✅ End-to-end delivery tested on Local. *(Submitted the Trade form → Quform entry stored (#191) and the notification email was generated correctly: To `mail@travelwithoutborders.co.uk`, subject `[TRADE ENQUIRY] New business enquiry from Trade Enquiry — Business`, body carrying all four fields. On Local the mail is captured by Local's Mailpit catcher (Post SMTP's Gmail OAuth2 can't authenticate on the `.local` domain, so it falls back to PHP mail() → Mailpit). On production it will deliver to the real inbox via the same Post SMTP → Gmail pipeline that already sends the individual enquiries — no extra setup. A real inbox delivery from Local is only possible by switching Local's Post SMTP to a Gmail App Password.)*



V2:


Footers:

	Ensure the footer across the full site is accurate containing the new testimonials page and the trade page.

Homepage:

	General:

		Fix grey, white pattern.
		Standardise spelling of Mosel. some instances are spelt differently.

	Slideshow:
		
		Neuschwanstein castle should be added in after Augsburg berlin, not replacing but after. this 			should be the snowy picture we have used as the top photo on the testimonials page
		Add Dresden photo, the same one used for the testimonials page at the bottom.

	Testimonials:
		
		change title to "Bespoke Germany Planning Holiday Feedback"
		change Karen review to the scott family
		change the A Corben review summary to include the POW war camp stuff.
		Replace J Shaw with mosel image
		new Scott family picture will be uploaded when received

	special interest:

		ensure all buttons can be clicked not just the photo. for the berlin berlsesque one, the enquire 		button takes u to the contact form where as the picture should take u to its designated page.

	special events:

		the pictures are clickable but the buttons arnt clickable, fix this

	
Testimonials page
	
	header/footer:
		
		Replacing both the top and bottom photo. The top photo is is the first photo sent on WhatsApp. the 		bottom 	photo is the second photo.

	Testimonials:	

		Reviews:

			scott family needs location.
			scott family needs picture
			New review named Andy Starling (same size as Karen review card) (June 2026 - special 				interest)
		
		Format Of Cards:
	
			J Shaw and Scott family cards should now follow the format of the cards on the homepage. 			the picture on the left and review on the right. the cards should cover the whole width of 			all the testimonials rather than leaving space for the a smaller card on its right. The 3 			remaining reviews will keep the smaller card format with the picture on the top.

			Order:
	
				J Shaw (picture on the left) (covers full width of 3 cards instead of 2, replacing 				the space the smaller card lives on its right)
				Scott family (Follows same format as J Shaw card) (exists below J Shaw review)
				remaining 3 Reviews exist on the final row below Scott Family. Consistent smaller 				cards with pictures on the top.


		Nav bar position:
			
			to be swapped with groups.


Destinations:

	Cards:

		✅ only the pictures and titles of the cards are clickable not the actual "more" button. *(Made all "MORE" buttons real clickable links to the same destination as the card — across the Destinations, Bavaria, Black Forest, Rhine/Mosel/Eifel, Northern, Eastern and Major Cities region pages, plus the Special Interest and Special Events listing pages. 77 buttons in total.)*

	Bavaria:

		✅ the same thing applies here, the actual "more" buttons arnt clickable only the images and titles. *(MORE buttons now clickable, same fix.)*
		✅ Somehow when clicking the images, it is redirecting me off my back up and onto the live site? *(ROOT CAUSE FOUND: all 12 Bavaria card links pointed to the absolute LIVE URL `https://travelwithoutborders.co.uk/…`. Converted them to local relative paths so they stay on the backup.)*
		✅ Some of the images redirect to a 404, all clickable things should redirect to their correct page. *(Fixed — e.g. the Augsburg card linked to `…/holidays-to-augsburg/` which 404s locally; corrected to the real local slug `…/augsburg/`. Re-audit shows 0 bad links across all card pages.)*


	Rest of the page directly under destinations:

		✅ Check all images, titles and buttons such as "more" are clickable. ensure they all redirect 			correctly not to unknown pages, giving 404 errors or anything else they are not supposed to do. *(Audited every ld_content_box card site-wide: all resolve to valid local pages, MORE buttons linked.)*


Special interests/special events:

	✅ do all the checks u did for all child pages for destinations but for special interests and special events. *(Full audit of the Special Interest + Special Events listing pages and all 17 child pages: every card's image, title and MORE button is clickable and points to the correct LOCAL page (verified a MORE click navigates, e.g. Christmas Markets). No live-site redirects and no 404s — the only two absolute links found (Mercedes-Benz Museum, Porsche Museum on the Motorcar page) are valid external links. Burlesque + Motorcar cards correctly point to the contact page.)*




Contact:

	Address:
		
		✅ change "out address" to "Registered Address" *(The address box heading on the Contact page now reads "Registered Address".)*
		✅ Add England *(Added "England" as the final line of the address: Unit 7 / Salisbury House / Wheatfield Way / Hinckley / LE10 1YG / England.)*

Trade:
	
	Header/footer:

		Replace the top photo with a photo of Augsburg
		replace the bottom photo with a photo of 


Talk:
	Destinations:

		all pages

			the writing in the top header black box isn't too clear as the colour isn't clear 				against black, consider changing to white.
			Double check all distances.
			Confirm this quote is correct "Travel without Borders offers a wide variety of travel (air-			rail/fly-drive/self-drive/rail) and accommodation (hotel/guesthouse) options.

			What to do:

				bullet points that carries over under the next line however they go under the 					bullet point rather than where the actual text starts after the bullet point.

		Bavaria:
		
			Oberammergau:
		
				Oberammergau passion play has no page under special events, only available from 				clicking register your interest in Oberammergau page under destination/Bavaria.
				no credit given under sliding images

			Regensburg:

				no credit given for any pictures

			Rothenburg Ob Tauber:
	
				little to no description under title.

		Black Forest:

			Freiburg:

				no credit for any of the pictures.

			Hiedelerg:
				
				no credit for any of the pictures.

			Konstanz (Lake Constance):

				little description under title.
				no credit given for any of the pictures.

			Lindau (Lake Constance):

				Little to no description under title.
				No credit given for pictures.

			Meersburg (Lake Constance):

				no credit given for pictures.

			Schluchsee:

				no credit given for pictures.

			Titisee_Neustadt:
		
				little to no description under title.
				No credit given for pictures.

			Triberg:

				Little description under title.
				No credit given for pictures.


		Eastern Germany:

			Eisenach:

				No picture in header.
				no credit given for pictures.

			Erfurt:

				no credit given for picture collage.
				picture collage sizing isn't consistent.

			Potsdam:

				Small description under Title.

			Rostock and Warnemunde:

				Small description under title.
				No picture in header
			
			Schwerin:

				small description under title.


		Northern Germany:

			Celle:
	
				little description under title.


			Hamelin:

				little to no description under title.


			Goslar (Harz Mountains):

				no credit given for pictures.
				sizing of pictures in collage isn't consistent.


		The Rhine, Mosel and Eifel:

			
			Aachen:

				no credit given for pictures.


			Bernkastel-Kues:

				no credit given for pictures.

			
			Boppard:

				little description under title.
				no credit given for pictures.

			
			Cochem:

				no credit given for photos.


			Daun:

				No credit given for photos.


			Koblenz:

				No credit given for picture collage.


			Rudesheim:

				No credit given for picture collage


			Trier:

				No credit given for picture collage.


		
		Major cities in Germany

			Bremen:

				Travel Facts format doesn't match all other pages. 
				section on its right doesn't line up

			
			Dusseldorf:

				Little description under title
				ALOT of info under "What to do"

			Frankfurt:
		
				Little description under title
				Extra bullet point under "what to do" with no content.

			Hannover:
				
				no credit given for picture collage.

			Stuttgart:

				picture doesn't load/exist anymore. description: Weindorf 0216 © Stuttgart 					Marketing GmbH Christoph Düpper


	Special interests:

		The Colditz Castle Experience:

			Small description under title.


		The Eagles's Nest (Kehlsteinhaus) Experience:

			small description under title.
			no credit given for pictures.
			Centre sub-titles including ("sample packages for the independent traveller by Air" and 			"Sample package for private groups" with both their child-titles)


		Fine Wine and Dine:

			No credit for pictures.

		
		Augsburg Football Tour:
	
			little to no description under title.
			no credit given for pictures.
			nothing next to the picture of a football in a goal.
			centre the coming soon section.


		Motorcar Enthusiasts holidays:

			Centre "Sample packages for the independent traveller by Air" + description.
			nothing next to "motorcar Weekend Dream" card. potentially a picture.


	Special Events:

		Stuttgart Canstatter Volksfest:

			no picture in header.

		
		Christmas market: 

			no credit on pictures.


		Cologne Carnival:

			no credit on photos.


	Tailor-Made Holidays:

		Tailor-made Holiday enquiry form should not exist, this should direct you to the contact page, 			where you complete that form, not an additional separate form on this page

	Testimonials:

		potential to change Header/Footer pictures. remove footer picture, change Header picture to match 		special events page style.

	Trade:
		
		Same change applied above (testimonials Header and Footer picture change)
	